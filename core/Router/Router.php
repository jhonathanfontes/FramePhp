<?php

namespace Core\Router;

use Core\Http\Request;
use Core\Router\RouteDefinition;

class Router
{
    private static $instance;
    private array $routes = [];
    private array $middlewares = [];
    private array $policies = [];
    private string $prefix = '';
    private string $currentName = '';
    private array $namedRoutes = [];
    private $fallback;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function middleware(array $middlewares): static
    {
        $this->middlewares = $middlewares;
        return $this;
    }

    public function policy(array $policies): static
    {
        $this->policies = $policies;
        return $this;
    }

    public function group(array $options = [], callable $callback): void
    {
        $originalMiddlewares = $this->middlewares;
        $originalPrefix = $this->prefix;
        $originalPolicies = $this->policies;

        if (isset($options['prefix'])) {
            $this->prefix .= '/' . trim($options['prefix'], '/');
        }

        if (isset($options['middleware'])) {
            $this->middlewares = array_merge($this->middlewares, (array) $options['middleware']);
        }

        if (isset($options['policy'])) {
            $this->policies = array_merge($this->policies, (array) $options['policy']);
        }

        $callback($this);

        $this->middlewares = $originalMiddlewares;
        $this->prefix = $originalPrefix;
        $this->policies = $originalPolicies;
    }

    public function get(string $uri, array|callable $callback): RouteDefinition
    {
        return new RouteDefinition($this, 'GET', $uri, $callback);
    }

    public function post(string $uri, array|callable $callback): RouteDefinition
    {
        return new RouteDefinition($this, 'POST', $uri, $callback);
    }

    public function name(string $name): static
    {
        $this->currentName = $name;
        return $this;
    }

    public function addRoute(string $method, string $uri, array|callable $callback): static
    {
        $fullUri = rtrim($this->prefix . '/' . trim($uri, '/'), '/') ?: '/';

        $route = [
            'uri' => $fullUri,
            'callback' => $callback,
            'middlewares' => $this->middlewares,
            'policies' => $this->policies,
        ];

        $this->routes[$method][] = $route;

        if ($this->currentName) {
            $this->namedRoutes[$this->currentName] = $fullUri;
            $this->currentName = '';
        }

        return $this;
    }

    public function dispatch(): void
    {
        $request = new Request();
        $currentUri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/') ?: '/';
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes[$method] ?? [] as $route) {
            $pattern = preg_replace('#\{[\w]+\}#', '([\w-]+)', $route['uri']);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $currentUri, $matches)) {
                array_shift($matches);
                $callback = $route['callback'];

                // Executa Middlewares
                foreach ($route['middlewares'] as $middleware) {
                    if (class_exists($middleware)) {
                        $instance = new $middleware();
                        if (method_exists($instance, 'handle')) {
                            $instance->handle($request, function($request) use ($callback) {
                                if (is_array($callback)) {
                                    $controller = new $callback[0]();
                                    return $controller->{$callback[1]}($request);
                                }
                                return $callback($request);
                            });
                        }
                    }
                }

                // Executa Policies
                foreach ($route['policies'] as $policy) {
                    if (class_exists($policy)) {
                        $instance = new $policy();
                        if (method_exists($instance, 'authorize')) {
                            $instance->authorize();
                        }
                    }
                }

                if (is_array($callback)) {
                    [$class, $method] = $callback;
                    call_user_func_array([new $class, $method], $matches);
                } else {
                    call_user_func_array($callback, $matches);
                }
                return;
            }
        }

        // Fallback personalizado
        if ($this->fallback) {
            call_user_func($this->fallback);
            return;
        }

        http_response_code(404);
        echo "404 - Página não encontrada";
    }

    public function route(string $name): ?string
    {
        return $this->namedRoutes[$name] ?? null;
    }

    public function setFallback(callable $callback): void
    {
        $this->fallback = $callback;
    }

    public function generateUrl(string $name, ?array $params = []): ?string
    {

        if (!isset($this->namedRoutes[$name])) {
            return null;
        }

        $url = $this->namedRoutes[$name];

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $placeholder = '{' . $key . '}';
                if (str_contains($url, $placeholder)) {
                    $url = str_replace($placeholder, $value, $url);
                    unset($params[$key]);
                }
            }

            if (!empty($params)) {
                $url .= '?' . http_build_query($params);
            }
        }

        return $url;
    }
    public function getFullUri(string $uri): string
    {
        return rtrim($this->prefix . '/' . trim($uri, '/'), '/') ?: '/';
    }

    public function registerRouteName(string $name, string $fullUri): void
    {
        $this->namedRoutes[$name] = $fullUri;
    }
}
