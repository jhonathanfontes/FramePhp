<?php

namespace Core\Container;

use ReflectionClass;
use ReflectionParameter;

class Container
{
    private static $instance = null;
    private array $bindings = [];

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Registra uma dependência no contêiner.
     */
    public function bind(string $abstract, $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    /**
     * Resolve (cria) uma instância de uma classe.
     */
    public function make(string $class)
    {
        // Se houver um 'binding' customizado, usa ele.
        if (isset($this->bindings[$class])) {
            $concrete = $this->bindings[$class];
            return ($concrete instanceof \Closure) ? $concrete($this) : $concrete;
        }

        // Caso contrário, tenta resolver automaticamente.
        return $this->resolve($class);
    }

    /**
     * Usa Reflection para construir a classe e suas dependências.
     */
    protected function resolve(string $class)
    {
        $reflector = new ReflectionClass($class);

        if (!$reflector->isInstantiable()) {
            throw new \Exception("A classe [{$class}] não é instanciável.");
        }

        $constructor = $reflector->getConstructor();

        // Se não há construtor, basta criar a classe.
        if (is_null($constructor)) {
            return new $class;
        }

        // Se há um construtor, resolve suas dependências recursivamente.
        $dependencies = $this->resolveDependencies($constructor->getParameters());

        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * Resolve os parâmetros do construtor.
     */
    protected function resolveDependencies(array $parameters): array
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if ($type && !$type->isBuiltin()) {
                // Se o parâmetro é uma classe, resolve-a com o container.
                $dependencies[] = $this->make($type->getName());
            } elseif ($parameter->isDefaultValueAvailable()) {
                // Se tem um valor padrão, usa ele.
                $dependencies[] = $parameter->getDefaultValue();
            }
        }

        return $dependencies;
    }
}