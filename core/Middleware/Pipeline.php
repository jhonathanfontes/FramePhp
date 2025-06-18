<?php

namespace Core\Middleware;

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
        $pipeline = array_reduce(
            array_reverse($this->middlewares),
            $this->carry(),
            $destination
        );

        return $pipeline($this->passable);
    }

    /**
     * Retorna uma Closure que representa uma "fatia" da pilha de execução.
     * Esta função é o núcleo da correção.
     */
    protected function carry(): \Closure
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                // $pipe agora é o array no formato: [$classeDoMiddleware, $parametros]
                [$middlewareClass, $params] = $pipe;

                if (!class_exists($middlewareClass)) {
                    // Lança uma exceção se a classe do middleware não for encontrada.
                    throw new \Exception("Middleware class not found: {$middlewareClass}");
                }

                // Instancia o middleware, passando os parâmetros (ex: 'admin') para o seu construtor.
                $middlewareInstance = new $middlewareClass(...$params);

                // Chama o método handle na instância do middleware.
                return $middlewareInstance->handle($passable, $stack);
            };
        };
    }
}
