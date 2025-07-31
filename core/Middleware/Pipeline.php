<?php

namespace Core\Middleware;

use Core\Http\Request;
use Core\Http\Response;
use Core\Interface\MiddlewareInterface;

class Pipeline
{
    private array $middlewares = [];
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function through(array $middlewares): self
    {
        $this->middlewares = $middlewares;
        return $this;
    }

    public function then(callable $destination)
    {
        $pipeline = array_reduce(
            array_reverse($this->middlewares),
            $this->carry(),
            $this->prepareDestination($destination)
        );

        return $pipeline($this->request);
    }

    protected function carry(): callable
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                if (is_string($pipe)) {
                    $pipe = new $pipe();
                }

                if (!$pipe instanceof MiddlewareInterface) {
                    throw new \InvalidArgumentException('Middleware deve implementar MiddlewareInterface');
                }

                return $pipe->handle($passable, $stack);
            };
        };
    }

    protected function prepareDestination(callable $destination): callable
    {
        return function ($passable) use ($destination) {
            return $destination($passable);
        };
    }
}
