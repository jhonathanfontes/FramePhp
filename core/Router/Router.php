<?php

namespace Core\Router;

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

    private function __construct()
    {
        $middlewarePath = BASE_PATH . '/config/middleware.php';
        if (file_exists($middlewarePath)) {
            $this->middlewareAliases = require $middlewarePath;
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Métodos para verbos HTTP
    public function get(string $uri, $callback): RouteDefinition { return $this->addRoute('GET', $uri, $callback); }
    public function post(string $uri, $callback): RouteDefinition { return $this->addRoute('POST', $uri, $callback); }
    public function put(string $uri, $callback): RouteDefinition { return $this->addRoute('PUT', $uri, $callback); }
    public function patch(string $uri, $callback): RouteDefinition { return $this->addRoute('PATCH', $uri, $callback); }
    public function delete(string $uri, $callback): RouteDefinition { return $this->addRoute('DELETE', $uri, $callback); }

    private function addRoute(string $method, string $uri, $callback): RouteDefinition
    {
        $fullUri = rtrim($this->prefix . '/' . trim($uri, '/'), '/') ?: '/';
        $routeDefinition = new RouteDefinition($method, $fullUri, $callback, $this->groupMiddleware);
        $this->routes[] = $routeDefinition;
        return $routeDefinition;
    }

    public function group(array $options, callable $callback): void
    {
        $originalPrefix = $this->prefix;
        $originalGroupMiddleware = $this->groupMiddleware;

        $this->prefix .= '/' . trim($options['prefix'] ?? '', '/');
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

    /**
     * Gera uma URL completa para uma rota nomeada.
     * Este é o método que estava faltando na versão original.
     */
    public function generateUrl(string $name, array $params = []): ?string
    {
        if (!isset($this->namedRoutes[$name])) {
            return null; // Rota nomeada não encontrada
        }

        /** @var RouteDefinition $route */
        $route = $this->namedRoutes[$name];
        $url = $route->getUri();

        // Substitui os parâmetros na URL
        foreach ($params as $key => $value) {
            $url = preg_replace('/\{' . $key . '(\:[^\}]+)?\}/', (string) $value, $url);
        }

        // Retorna a URL completa usando a função helper base_url
        return base_url($url);
    }

    private function resolveMiddleware(string $middleware): array
    {
        [$name, $params] = array_pad(explode(':', $middleware, 2), 2, null);
        $className = $this->middlewareAliases[$name] ?? $name;
        return [$className, $params ? explode(',', $params) : []];
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
            echo "404 Not Found";
        }
    }

    private function processRoute(RouteDefinition $route, Request $request): void
    {
        $middlewares = array_map([$this, 'resolveMiddleware'], $route->getMiddlewares());
        $pipeline = new Pipeline();

        $response = $pipeline->send($request)
            ->through($middlewares)
            ->then(function ($request) use ($route) {
                $params = $route->getParams();
                $callback = $route->getCallback();

                if (is_array($callback)) {
                    [$controllerClass, $method] = $callback;
                    $controller = new $controllerClass();
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

    public function setFallback(callable $callback): void
    {
        $this->fallback = $callback;
    }
}