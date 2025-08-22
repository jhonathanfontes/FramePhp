<?php

namespace Core\Router;

use Core\Container\Container;
use Core\Http\Request;
use Core\Http\Response;
use Core\Middleware\Pipeline;

class Router
{
    private static $instance;
    private array $routes = [];
    private array $namedRoutes = [];
    private $fallback;
    private string $prefix = '';
    private array $groupMiddleware = [];
    private array $middlewareAliases = [];

    /**
     * O construtor é privado para garantir que a classe só possa ser instanciada via getInstance()
     */
    private function __construct()
    {
        $middlewarePath = BASE_PATH . '/config/middleware.php';
        if (file_exists($middlewarePath)) {
            $this->middlewareAliases = require $middlewarePath;
        }
    }

    /**
     * Este é o método estático que permite obter a única instância da classe Router.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // --- O restante da classe com todas as funcionalidades ---

    public function get(string $uri, $callback): RouteDefinition
    {
        return $this->addRoute('GET', $uri, $callback);
    }
    public function post(string $uri, $callback): RouteDefinition
    {
        return $this->addRoute('POST', $uri, $callback);
    }
    public function put(string $uri, $callback): RouteDefinition
    {
        return $this->addRoute('PUT', $uri, $callback);
    }
    public function patch(string $uri, $callback): RouteDefinition
    {
        return $this->addRoute('PATCH', $uri, $callback);
    }
    public function delete(string $uri, $callback): RouteDefinition
    {
        return $this->addRoute('DELETE', $uri, $callback);
    }

    /**
     * Define uma rota de redirecionamento de uma URI para outra.
     */
    public function redirect(string $from, string $to, int $statusCode = 302): RouteDefinition
    {
        return $this->addRoute('GET', $from, function () use ($to, $statusCode) {
            Response::redirectResponse($to, $statusCode)->send();
        })->name('redirect.' . md5($from)); // Gera um nome único para a rota de redirecionamento
    }

    private function addRoute(string $method, string $uri, $callback): RouteDefinition
    {
        // Constrói a URI completa de forma mais robusta
        $fullUri = rtrim($this->prefix, '/') . '/' . ltrim($uri, '/');

        // Corrige rotas como '//' ou '' para '/'
        if ($fullUri === '//' || $fullUri === '') {
            $fullUri = '/';
        }

        $routeDefinition = new RouteDefinition($method, $fullUri, $callback, $this->groupMiddleware);
        $this->routes[] = $routeDefinition;
        return $routeDefinition;
    }

    public function group(array $options, callable $callback): void
    {
        $originalPrefix = $this->prefix;
        $originalGroupMiddleware = $this->groupMiddleware;

        if (isset($options['prefix'])) {
            $this->prefix .= '/' . trim($options['prefix'], '/');
            $this->prefix = str_replace('//', '/', $this->prefix); // Remove barras duplas
        }
  
        if (isset($options['middleware'])) {
            $this->groupMiddleware = array_merge($this->groupMiddleware, (array) $options['middleware']);
        }

        $callback($this);

        $this->prefix = $originalPrefix;
        $this->groupMiddleware = $originalGroupMiddleware;
    }

    public function registerRouteName(string $name, RouteDefinition $route): void
    {
        $this->namedRoutes[$name] = $route;
    }

    public function generateUrl(string $name, array $params = []): ?string
    {
        if (!isset($this->namedRoutes[$name])) {
            return null;
        }
        $route = $this->namedRoutes[$name];
        $url = $route->getUri();
        foreach ($params as $key => $value) {
            $url = preg_replace('/\{' . $key . '(\:[^\}]+)?\}/', (string) $value, $url);
        }
        return base_url($url);
    }

    private function resolveMiddleware(string $middleware): array
    {
        [$name, $params] = array_pad(explode(':', $middleware, 2), 2, null);
        $className = $this->middlewareAliases[$name] ?? $name;
        return [$className, $params ? explode(',', $params) : []];
    }

    private function processRoute(RouteDefinition $route, Request $request): void
    {
        $middlewares = array_map([$this, 'resolveMiddleware'], $route->getMiddlewares());
        $pipeline = new Pipeline($request);

        $response = $pipeline->through($middlewares)
            ->then(function ($request) use ($route) {
                $params = $route->getParams();
                $callback = $route->getCallback();

                if (is_array($callback)) {
                    [$controllerClass, $method] = $callback;
                    $container = Container::getInstance();
                    $controller = $container->make($controllerClass);
                    $response = call_user_func_array([$controller, $method], $params);
                } else {
                    $response = call_user_func_array($callback, $params);
                }

                if (!$response instanceof Response) {
                    return new Response((string) $response);
                }
                return $response;
            });

        $response->send();
    }

    public function dispatch(): void
    {
        $request = new Request();
        $method = $request->input('_method', $request->getMethod());
        $path = $request->getPath();

        foreach ($this->routes as $route) {
            if ($route->matches($method, $path)) {
                $this->processRoute($route, $request);
                return;
            }
        }

        http_response_code(404);
        if ($this->fallback) {
            call_user_func($this->fallback);
        } else {
            // Um fallback simples caso a página de erro também falhe
            echo "404 - Rota não encontrada.";
        }
    }

    public function setFallback(callable $callback): void
    {
        $this->fallback = $callback;
    }
}