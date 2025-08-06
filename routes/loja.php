<?php

use App\Controllers\Backend\Loja\CheckoutController;
use App\Controllers\Loja\HomeController;
use App\Controllers\Site\AuthController;

$router = \Core\Router\Router::getInstance();

/*
|--------------------------------------------------------------------------
| Rotas da Aplicação Web
|--------------------------------------------------------------------------
*/

// Rotas públicas (para todos os visitantes)
$router->group(['middleware' => ['locale', 'csrf']], function ($router) {
    $router->get('/', [HomeController::class, 'index'])->name('home');
});

// Rotas públicas da loja
$router->group(['prefix' => '/'], function ($router) {
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/produtos', [HomeController::class, 'produtos']);
    $router->get('/produto/{id}', [HomeController::class, 'produto']);
    $router->get('/categoria/{id}', [HomeController::class, 'produtos']);
    $router->get('/sobre', [HomeController::class, 'sobre']);
    $router->get('/contato', [HomeController::class, 'contato']);
    $router->get('/busca', [HomeController::class, 'produtos']);
});

// Rotas de autenticação da loja
$router->group(['prefix' => '/loja'], function ($router) {
    $router->get('/login', [AuthController::class, 'loginLoja']);
    $router->post('/login', [AuthController::class, 'loginLoja']);
    $router->get('/logout', [AuthController::class, 'logout']);
    $router->get('/esqueci-senha', [AuthController::class, 'esqueciSenha']);
    $router->post('/esqueci-senha', [AuthController::class, 'esqueciSenha']);
    $router->get('/reset-senha/{token}', [AuthController::class, 'resetSenha']);
    $router->post('/reset-senha/{token}', [AuthController::class, 'resetSenha']);
});

// Rotas protegidas da loja
$router->group(['prefix' => '/loja', 'middleware' => 'auth:loja'], function ($router) {
    $router->get('/perfil', [PerfilController::class, 'index']);
    $router->post('/perfil', [PerfilController::class, 'update']);
    $router->get('/meus-pedidos', [PerfilController::class, 'pedidos']);
    $router->get('/pedido/{id}', [PerfilController::class, 'pedido']);
});

// Rotas do carrinho
$router->group(['prefix' => '/carrinho'], function ($router) {
    $router->get('/', [CarrinhoController::class, 'index']);
    $router->post('/adicionar', [CarrinhoController::class, 'adicionar']);
    $router->post('/atualizar', [CarrinhoController::class, 'atualizar']);
    $router->post('/remover', [CarrinhoController::class, 'remover']);
    $router->post('/limpar', [CarrinhoController::class, 'limpar']);
});

// Rotas do checkout
$router->group(['prefix' => '/checkout'], function ($router) {
    
    $router->post('/processar', [CheckoutController::class, 'processar']);
    $router->get('/sucesso/{id}', [CheckoutController::class, 'sucesso']);
    $router->get('/cancelado', [CheckoutController::class, 'cancelado']);
});