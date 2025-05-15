<?php

namespace Core\Router;

class Router
{
    private array $routes = [];
    private string $requestMethod;
    private string $requestUri;
    private array $currentGroup = [];
    private ?string $prefix = null;
    private array $middleware = [];

    public function __construct()
    {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->requestUri = str_replace('/FramePhp/public', '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        $this->middleware = [];
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
        foreach ($this->routes as $route) {
            if ($this->matchRoute($route)) {
                foreach ($route['middleware'] as $middleware) {
                    $instance = new $middleware();
                    $instance->handle();
                }
                return $this->executeCallback($route['callback']);
            }
        }
        
        // Rota não encontrada - usar ErrorHandler em vez de resposta direta
        \Core\Error\ErrorHandler::handleNotFound();
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
}