<?php

use Core\Router\Router;
use App\Controllers\ExemploController;
use App\Controllers\AuthController;

return function(Router $router) {
    // Rotas básicas
    $router->get('/', function() {
        echo "Bem-vindo ao Framework PHP!";
    });

    // Rotas do controller de exemplo
    $router->get('/exemplo', [ExemploController::class, 'index']);
    $router->get('/exemplo/criar', [ExemploController::class, 'create']);
    $router->post('/exemplo/salvar', [ExemploController::class, 'store']);

    // Você pode agrupar rotas por funcionalidade
    // Rotas de autenticação
    $router->get('/login', [AuthController::class, 'loginForm']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->get('/logout', [AuthController::class, 'logout']);
    
};