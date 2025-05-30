<?php

namespace Core\Router;

class RouteDefinition
{
    private Router $router;
    private string $method;
    private string $uri;
    private mixed $callback;

    public function __construct(Router $router, string $method, string $uri, array|callable $callback)
    {
        $this->router = $router;
        $this->method = $method;
        $this->uri = $uri;
        $this->callback = $callback;

        $this->router->addRoute($method, $uri, $callback);
    }

    public function name(string $name): self
    {
        // Aqui chamamos o método do Router
        $fullUri = $this->router->getFullUri($this->uri);
        $this->router->registerRouteName($name, $fullUri);
        return $this;
    }
}
