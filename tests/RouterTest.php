<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Core\Router\Router;

class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router();
    }

    public function testAddRoute()
    {
        $this->router->get('/test', function() {
            return 'test';
        });

        $this->assertTrue(true); // Substituir por verificação real
    }

    public function testMatchRoute()
    {
        $this->router->get('/users', function() {
            return 'users list';
        });

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/users';

        ob_start();
        $this->router->dispatch();
        $output = ob_get_clean();

        $this->assertEquals('users list', $output);
    }
}