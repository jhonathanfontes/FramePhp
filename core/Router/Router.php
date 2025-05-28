<?php
// core/Router/Router.php

namespace Core\Router;

use Core\Http\Request;

class Router
{
    private static $instance;
    private array $routes = [];
    private array $middlewares = [];
    private array $policies = [];
    private string $prefix = '';
    private string $currentName = '';
    private array $namedRoutes = [];
    private $fallback; // adiciona no início da classe

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

    public function group(array $options = [], callable $callback): void
    {
        $originalMiddlewares = $this->middlewares;
        $originalPrefix = $this->prefix;
        $originalPolicies = $this->policies;

        if (isset($options['prefix'])) {
            $this->prefix .= '/' . trim($options['prefix'], '/');
        }

        $callback($this);

        $this->middlewares = $originalMiddlewares;
        $this->prefix = $originalPrefix;
        $this->policies = $originalPolicies;
    }

    public function get(string $uri, array|callable $callback): static
    {
        return $this->addRoute('GET', $uri, $callback);
    }

    public function post(string $uri, array|callable $callback): static
    {
        return $this->addRoute('POST', $uri, $callback);
    }

    public function name(string $name): static
    {
        $this->currentName = $name;
        return $this;
    }

    private function addRoute(string $method, string $uri, array|callable $callback): static
    {
        $fullUri = rtrim($this->prefix . '/' . trim($uri, '/'), '/') ?: '/';

        $route = [
            'uri' => $fullUri,
            'callback' => $callback,
            'middlewares' => $this->middlewares
        ];

        if ($this->currentName) {
            $this->namedRoutes[$this->currentName] = $fullUri;
            $this->currentName = '';
        }

        $this->routes[$method][] = $route;

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

                // Definir $callback antes de usar nos middlewares
                $callback = $route['callback'];

                foreach ($route['middlewares'] as $middleware) {
                    if (class_exists($middleware)) {
                        (new $middleware())->handle($request, function ($request) use ($callback, $matches) {
                            if (is_array($callback)) {
                                [$class, $method] = $callback;
                                return call_user_func_array([new $class, $method], $matches);
                            }
                            return call_user_func($callback, $request);
                        });
                    }
                }

                $callback = $route['callback'];
                if (is_array($callback)) {
                    [$class, $method] = $callback;
                    call_user_func_array([new $class, $method], $matches);
                    return;
                }

                call_user_func_array($callback, $matches);
                return;
            }
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
  
        // Check if the route name exists in the namedRoutes array
        if (!isset($this->namedRoutes[$name])) {
            return null;
        }
        
        $url = $this->namedRoutes[$name];

        // Check if the route name exists in the namedRoutes array
        if (!isset($this->namedRoutes[$name])) {
            return null;
        }

        $url = $this->namedRoutes[$name];

        // Replace parameters in the URL
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $placeholder = '{' . $key . '}';
                if (str_contains($url, $placeholder)) {
                    $url = str_replace($placeholder, $value, $url);
                    unset($params[$key]); // Remove used parameter
                }
            }

            $url .= '?' . http_build_query($params);
        }

        return $url;
    }
}
