<?php

namespace Core\Router;

use Core\Http\Request;
use Core\Http\Response;

class Router
{
    private static $instance = null;
    private array $routes = [];
    private string $requestMethod;
    private string $requestUri;
    private array $currentGroup = [];
    private ?string $prefix = null;
    private array $middleware = [];
    private $namedRoutes = [];

    public function __construct()
    {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->requestUri = str_replace('/FramePhp/public', '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        $this->middleware = [];
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get(string $uri, $callback): self
    {
        $this->add('GET', $uri, $callback);
        return $this;
    }

    public function post(string $uri, $callback): self
    {
        $this->add('POST', $uri, $callback);
        return $this;
    }

    public function put(string $uri, $callback): self
    {
        $this->add('PUT', $uri, $callback);
        return $this;
    }

    public function delete(string $uri, $callback): self
    {
        $this->add('DELETE', $uri, $callback);
        return $this;
    }

    public function prefix(string $prefix): self
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function middleware($middleware): self
    {
        if (is_array($middleware)) {
            $this->middleware = array_merge($this->middleware ?? [], $middleware);
        } else {
            $this->middleware[] = $middleware;
        }
        return $this;
    }

    public function group(array $attributes, callable $callback): void
    {
        $previousGroup = $this->currentGroup;
        $previousMiddleware = $this->middleware;
        $previousPrefix = $this->prefix;

        $this->currentGroup = array_merge($this->currentGroup, $attributes);

        if (isset($attributes['prefix'])) {
            $this->prefix = ($previousPrefix ? $previousPrefix . '/' : '') . trim($attributes['prefix'], '/');
        }

        if (isset($attributes['middleware'])) {
            $this->middleware($attributes['middleware']);
        }

        $callback($this);

        $this->currentGroup = $previousGroup;
        $this->middleware = $previousMiddleware;
        $this->prefix = $previousPrefix;
    }

    public function where(array $patterns): self
    {
        $route = end($this->routes);
        $route['patterns'] = $patterns;
        $this->routes[key($this->routes)] = $route;
        return $this;
    }

    public function name(string $name): self
    {
        $route = end($this->routes);
        $route['name'] = $name;
        $this->routes[key($this->routes)] = $route;
        return $this;
    }

    private function add(string $method, string $uri, $callback): void
    {
        if ($this->prefix) {
            $uri = trim($this->prefix . '/' . trim($uri, '/'), '/');
        }

        $route = [
            'method' => $method,
            'uri' => $uri,
            'callback' => $callback,
            'middleware' => $this->middleware ?? []
        ];

        $this->routes[] = $route;

        if (isset($route['name'])) {
            $this->namedRoutes[$route['name']] = $route;
        }
    }

    private function matchRoute(array $route): bool
    {
        $routePath = '/' . trim($route['uri'], '/');
        $requestPath = '/' . trim($this->requestUri, '/');
        
        return $route['method'] === $this->requestMethod && 
               $routePath === $requestPath;
    }

    private function buildPatternFromRoute(array $route): string
    {
        $uri = $route['uri'];
        
        // Substitui parâmetros com padrões personalizados
        if (isset($route['patterns'])) {
            foreach ($route['patterns'] as $param => $pattern) {
                $uri = str_replace("{{$param}}", "($pattern)", $uri);
            }
        }

        // Substitui parâmetros restantes com padrão padrão
        $uri = preg_replace('/\{([^}]+)\}/', '([^/]+)', $uri);
        
        return "#^{$uri}$#";
    }

    public function dispatch()
    {
        $request = new Request();
        
        foreach ($this->routes as $route) {
            if ($this->matchRoute($route)) {
                try {
                    // Execute middlewares
                    $response = $this->executeMiddleware($route['middleware'] ?? [], $request, function() use ($route, $request) {
                        return $this->executeCallback($route['callback'], $request);
                    });
                    
                    if ($response instanceof Response) {
                        $response->send();
                        return;
                    }
                    
                    echo $response;
                    return;
                } catch (\Exception $e) {
                    // Log do erro
                    error_log($e->getMessage());
                    
                    // Exibir erro em modo de desenvolvimento
                    if (config('app.debug', false)) {
                        echo '<h1>Erro:</h1>';
                        echo '<p>' . $e->getMessage() . '</p>';
                        echo '<pre>' . $e->getTraceAsString() . '</pre>';
                        exit;
                    }
                    
                    // Em produção, mostrar página de erro genérica
                    echo 'Ocorreu um erro. Por favor, tente novamente mais tarde.';
                    exit;
                }
            }
        }
       
        $this->handleRouteNotFound();
    }
    
    private function executeMiddleware(array $middlewares, Request $request, \Closure $callback)
    {
        if (empty($middlewares)) {
            return $callback();
        }
        
        $middleware = array_shift($middlewares);
        $instance = new $middleware();
        
        return $instance->handle($request, function($request) use ($middlewares, $callback) {
            return $this->executeMiddleware($middlewares, $request, $callback);
        });
    }

    private function executeCallback($callback)
    {
        if (is_callable($callback)) {
            return call_user_func($callback);
        }

        if (is_array($callback)) {
            [$controller, $method] = $callback;
            if (is_string($controller)) {
                $controller = new $controller();
            }
            return call_user_func([$controller, $method]);
        }

        throw new \Exception("Callback inválido");
    }

    private function handleRouteNotFound()
    {
        // Rota não encontrada
        $error = [
            'type' => 'NotFoundError',
            'message' => 'A página solicitada não foi encontrada',
            'file' => __FILE__,
            'line' => __LINE__,
            'timestamp' => date('Y-m-d H:i:s'),
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
        ];

        // Usar o ErrorHandler para renderizar a página de erro
        $errorHandler = \Core\Error\ErrorHandler::getInstance();
        $errorHandler->renderErrorPage($error);
    }

    public function generateUrl($name, $params = [])
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \Exception("Route name '{$name}' not found");
        }

        $route = $this->namedRoutes[$name];
        $path = $route['uri'];

        // Substituir parâmetros na URL
        foreach ($params as $key => $value) {
            $path = str_replace('{' . $key . '}', $value, $path);
        }

        // Remover parâmetros opcionais não fornecidos
        $path = preg_replace('/\{[^}]+\}/', '', $path);
        $path = str_replace('//', '/', $path);

        return $path;
    }
}