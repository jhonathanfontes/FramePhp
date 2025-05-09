<?php

namespace Core\Router;

class Router
{
    private array $routes = [];
    private string $requestMethod;
    private string $requestUri;

    public function __construct()
    {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->requestUri = $_SERVER['REQUEST_URI'];
    }

    public function add(string $method, string $uri, $callback): void
    {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'callback' => $callback
        ];
    }

    public function get(string $uri, $callback): void
    {
        $this->add('GET', $uri, $callback);
    }

    public function post(string $uri, $callback): void
    {
        $this->add('POST', $uri, $callback);
    }

    public function dispatch()
    {
        foreach ($this->routes as $route) {
            if ($this->matchRoute($route)) {
                return $this->executeCallback($route['callback']);
            }
        }
        
        // Rota não encontrada
        header("HTTP/1.0 404 Not Found");
        echo "404 - Página não encontrada";
    }

    private function matchRoute(array $route): bool
    {
        return $route['method'] === $this->requestMethod && 
               $route['uri'] === parse_url($this->requestUri, PHP_URL_PATH);
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