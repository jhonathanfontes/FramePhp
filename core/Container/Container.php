<?php

namespace Core\Container;

class Container
{
    private static $instance = null;
    private $bindings = [];
    private $middlewares = [];
    
    private function __construct()
    {
        // Singleton
    }
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self(); // Só acontece 1x
        }
        return self::$instance;
    }
    
    public function bind(string $abstract, $concrete)
    {
        $this->bindings[$abstract] = $concrete;
    }
    
    public function bindMiddleware(string $alias, $middleware)
    {
        $this->middlewares[$alias] = $middleware;
    }
    
    public function make(string $abstract)
    {
        if (!isset($this->bindings[$abstract])) {
            throw new \Exception("Binding não encontrado para: {$abstract}");
        }
        
        $concrete = $this->bindings[$abstract];
        
        if ($concrete instanceof \Closure) {
            return $concrete();
        }
        
        return $concrete;
    }
    
    public function resolveMiddleware(string $alias)
    {
        if (!isset($this->middlewares[$alias])) {
            throw new \Exception("Middleware não encontrado para alias: {$alias}");
        }
        
        $middleware = $this->middlewares[$alias];
        
        if ($middleware instanceof \Closure) {
            return $middleware();
        }
        
        return $middleware;
    }
}