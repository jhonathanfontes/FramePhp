<?php

namespace Core\Router;

class RouteDefinition
{
    private string $method;
    private string $uri;
    private $callback;
    private array $middlewares;
    private array $params = [];
    private array $where = []; // Armazena as restrições (regex) para os parâmetros

    public function __construct(string $method, string $uri, $callback, array $middlewares = [])
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->callback = $callback;
        $this->middlewares = $middlewares;
    }

    /**
     * Define um nome para a rota, registrando-a no Router.
     */
    public function name(string $name): self
    {
        Router::getInstance()->registerRouteName($name, $this);
        return $this;
    }

    /**
     * Adiciona uma restrição de expressão regular para um parâmetro da rota.
     */
    public function where(string $param, string $regex): self
    {
        $this->where[$param] = $regex;
        return $this;
    }

    /**
     * Verifica se a rota corresponde ao método e ao caminho (path) da requisição.
     */
    public function matches(string $method, string $path): bool
    {
        if (strcasecmp($this->method, $method) !== 0) {
            return false;
        }

        $pattern = preg_replace_callback('/\{([a-zA-Z0-9_]+)\}/', function ($matches) {
            $paramName = $matches[1];
            // Usa a restrição definida em where() ou um padrão genérico
            return '(' . ($this->where[$paramName] ?? '[^/]+') . ')';
        }, $this->uri);

        if (preg_match('#^' . $pattern . '$#', $path, $matches)) {
            array_shift($matches);
            $this->params = $matches; // Armazena os parâmetros capturados
            return true;
        }

        return false;
    }

    // Getters para que a classe Router possa acessar as propriedades
    public function getUri(): string
    {
        return $this->uri;
    }
    public function getCallback()
    {
        return $this->callback;
    }
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
    public function getParams(): array
    {
        return $this->params;
    }
}
