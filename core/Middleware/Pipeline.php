<?php

namespace Core\Middleware;

use Core\Http\Request;
use Core\Http\Response;

class Pipeline
{
    protected array $middlewares = [];
    protected $passable;

    public function send($passable): static
    {
        $this->passable = $passable;
        return $this;
    }

    public function through(array $middlewares): static
    {
        $this->middlewares = $middlewares;
        return $this;
    }

    public function then(\Closure $destination)
    {
        return array_reduce(
            array_reverse($this->middlewares),
            fn($stack, $middleware) => fn($passable) => (new $middleware)->handle($passable, $stack),
            $destination
        )($this->passable);
    }
}