<?php

namespace Core\Router;

use Core\Http\Request;
use Core\Http\Response;
use Core\Container\Container;

class Router
{
    private array $routes = [];
    protected array $namedRoutes = [];
    protected ?string $prefix = null;
    protected array $middleware = [];
    protected $fallback = null;
    private  string $requestMethod;
    private  string $requestPath;
    protected bool $useAutoRouting = true; // Nova propriedade para habilitar/desabilitar o auto-routing
    protected string $defaultController = 'Home'; // Controlador padrão
    protected string $defaultMethod = 'index'; // Método padrão
    protected array $currentGroup = []; // Added missing property

    private static ?self $instance = null;

    public function __construct(Request $request = null)
    {
        $this->requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $this->requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        $cacheFile = defined('BASE_PATH') ? BASE_PATH . '/bootstrap/cache/routes.php' : '';
        if ($cacheFile && file_exists($cacheFile) && config('app.env') === 'production') {
            $this->routes = require $cacheFile;
            foreach ($this->routes as $route) {
                if (isset($route['name'])) {
                    $this->namedRoutes[$route['name']] = $route;
                }
            }
        }
    }
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function get(string $uri, $callback): self
    {
        return $this->addRoute('GET', $uri, $callback);
    }
    public function post(string $uri, $callback): self
    {
        return $this->addRoute('POST', $uri, $callback);
    }
    public function put(string $uri, $callback): self
    {
        return $this->addRoute('PUT', $uri, $callback);
    }
    public function delete(string $uri, $callback): self
    {
        return $this->addRoute('DELETE', $uri, $callback);
    }

    protected function addRoute(string $method, string $uri, $callback): self
    {
        $uri = $this->prefix ? trim($this->prefix . '/' . trim($uri, '/'), '/') : trim($uri, '/');
        $route = [
            'method' => $method,
            'uri' => $uri,
            'callback' => $callback,
            'middleware' => $this->middleware,
        ];
        if (strpos($uri, '{') !== false) {
            $route['regex'] = $this->buildPatternFromRoute($route);
        }
        $this->routes[] = $route;
        return $this;
    }

    public function prefix(string $prefix): self
    {
        $this->prefix = trim($prefix, '/');
        return $this;
    }

    public function middleware($middleware): self
    {
        $this->middleware = array_merge($this->middleware, (array) $middleware);
        return $this;
    }

    public function group(array $attributes, callable $callback): void
    {
        $previous = [$this->currentGroup, $this->middleware, $this->prefix];

        $this->currentGroup = array_merge($this->currentGroup, $attributes);
        $this->prefix = isset($attributes['prefix']) ? trim($this->prefix . '/' . trim($attributes['prefix'], '/'), '/') : $this->prefix;
        if (isset($attributes['middleware'])) {
            $this->middleware($attributes['middleware']);
        }

        $callback($this);

        [$this->currentGroup, $this->middleware, $this->prefix] = $previous;
    }

    public function name(string $name): self
    {
        $lastIndex = array_key_last($this->routes);
        $this->routes[$lastIndex]['name'] = $name;
        $this->namedRoutes[$name] = $this->routes[$lastIndex];
        return $this;
    }

    public function where(array $patterns): self
    {
        $lastIndex = array_key_last($this->routes);
        $this->routes[$lastIndex]['patterns'] = $patterns;
        return $this;
    }

    protected function buildPatternFromRoute(array $route): string
    {
        $uri = $route['uri'];
        foreach ($route['patterns'] ?? [] as $param => $pattern) {
            $uri = str_replace("{{$param}}", "({$pattern})", $uri);
        }
        $uri = preg_replace('/\{[^}]+\}/', '([^/]+)', $uri);
        return "#^{$uri}$#";
    }

    public function dispatch()
    {
        $request = new Request();

        foreach ($this->routes as $route) {
            if ($this->matchRoute($route)) {
                return $this->handle($route, $request);
            }
        }

        if ($this->useAutoRouting && ($response = $this->autoRoute($request)) !== false) {
            echo $response;
            return;
        }

        $this->handleRouteNotFound();
    }

    protected function matchRoute(array $route): bool
    {
        $requestPath = '/' . trim($this->requestPath ?? '', '/');
        $routePath = '/' . trim($route['uri'] ?? '', '/');

        if ($route['method'] === $this->requestMethod && $routePath === $requestPath) {
            return true;
        }

        return isset($route['regex']) && $route['method'] === $this->requestMethod && preg_match($route['regex'], $requestPath);
    }

    protected function handle(array $route, Request $request)
    {
        try {
            $response = (new \Core\Middleware\Pipeline())
                ->send($request)
                ->through($this->resolveMiddlewares($route['middleware'] ?? []))
                ->then(fn($request) => $this->executeCallback($route['callback'], $request));

            $response instanceof Response ? $response->send() : print ($response);
        } catch (\Throwable $e) {
            if (config('app.debug', false)) {
                echo "<h1>Erro:</h1><p>{$e->getMessage()}</p><pre>{$e->getTraceAsString()}</pre>";
            } else {
                echo 'Ocorreu um erro. Por favor, tente novamente mais tarde.';
            }
        }
    }

    protected function resolveMiddlewares(array $aliases): array
    {
        $container = Container::getInstance();
        return array_map(fn($alias) => $container->resolveMiddleware($alias), $aliases);
    }

    protected function executeCallback($callback, Request $request)
    {
        if (is_callable($callback))
            return $callback($request);

        if (is_array($callback)) {
            [$controller, $method] = $callback;
            $instance = is_string($controller) ? new $controller() : $controller;
            return $instance->{$method}($request);
        }

        throw new \Exception("Callback inválido");
    }

    protected function handleRouteNotFound(): void
    {
        $error = [
            'type' => 'NotFoundError',
            'message' => 'A página solicitada não foi encontrada',
            'file' => __FILE__,
            'line' => __LINE__,
            'timestamp' => date('Y-m-d H:i:s'),
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
        ];

        \Core\Error\ErrorHandler::getInstance()->renderErrorPage($error);
    }

    public function generateUrl($name, $params = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \Exception("Rota '{$name}' não encontrada. Disponíveis: " . implode(', ', array_keys($this->namedRoutes)));
        }

        $uri = $this->namedRoutes[$name]['uri'];
        foreach ($params as $key => $value) {
            $uri = str_replace("{{$key}}", $value, $uri);
        }

        return '/' . ltrim($uri, '/');
    }

    public function setFallback(callable $callback): void
    {
        $this->fallback = $callback;
    }

    protected function autoRoute(Request $request)
    {
        $path = trim($request->getPath(), '/');
        $segments = explode('/', $path);

        $controller = ucfirst($segments[0] ?? $this->defaultController);
        $method = $segments[1] ?? $this->defaultMethod;
        $params = array_slice($segments, 2);

        return $this->executeController($controller, $method, $params, $request);
    }

    protected function executeController(string $controllerClass, string $method, array $params, Request $request)
    {
        $class = "App\\Controllers\\{$controllerClass}";
        if (!class_exists($class))
            return false;

        $instance = new $class();
        return method_exists($instance, $method) ? $instance->{$method}($request, ...$params) : false;
    }
}