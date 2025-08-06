<?php

use App\Controllers\Backend\Loja\CarrinhoController as BackendCarrinhoController;
use App\Controllers\Backend\Loja\CheckoutController as BackendCheckoutController;
use App\Controllers\Backend\Loja\UsuarioController as BackendUsuarioController;
use App\Controllers\Loja\CarrinhoController as LojaCarrinhoController;
use App\Controllers\Loja\HomeController as LojaHomeController;
use App\Controllers\Loja\UsuarioController as LojaUsuarioController;
use App\Controllers\Site\AuthController as SiteAuthController;

$router = \Core\Router\Router::getInstance();

/*
|--------------------------------------------------------------------------
| Rotas da Aplicação Web
|--------------------------------------------------------------------------
*/

// Rotas públicas (para todos os visitantes)
$router->group(['middleware' => ['locale', 'csrf']], function ($router) {
    $router->get('/', [LojaHomeController::class, 'index'])->name('home');
});

// Rotas públicas da loja
$router->group(['prefix' => '/'], function ($router) {
    $router->get('/', [LojaHomeController::class, 'index']);
    $router->get('/produtos', [LojaHomeController::class, 'produtos']);
    $router->get('/produto/{id}', [LojaHomeController::class, 'produto']);
    $router->get('/categoria/{id}', [LojaHomeController::class, 'produtos']);
    $router->get('/sobre', [LojaHomeController::class, 'sobre']);
    $router->get('/contato', [LojaHomeController::class, 'contato']);
    $router->get('/busca', [LojaHomeController::class, 'produtos']);
});

// Rotas de autenticação da loja
$router->group(['prefix' => '/loja'], function ($router) {
    $router->get('/login', [SiteAuthController::class, 'loginLoja']);
    $router->post('/login', [SiteAuthController::class, 'loginLoja']);
    $router->get('/logout', [SiteAuthController::class, 'logout']);
    $router->get('/esqueci-senha', [SiteAuthController::class, 'esqueciSenha']);
    $router->post('/esqueci-senha', [SiteAuthController::class, 'esqueciSenha']);
    $router->get('/reset-senha/{token}', [SiteAuthController::class, 'resetSenha']);
    $router->post('/reset-senha/{token}', [SiteAuthController::class, 'resetSenha']);
});

// Rotas protegidas da loja
$router->group(['prefix' => '/loja', 'middleware' => 'auth:loja'], function ($router) {
    $router->get('/perfil', [LojaUsuarioController::class, 'index']);
    $router->post('/perfil', [BackendUsuarioController::class, 'update']);
    $router->get('/meus-pedidos', [LojaUsuarioController::class, 'pedidos']);
    $router->get('/pedido/{id}', [LojaUsuarioController::class, 'pedido']);
});

// Rotas do carrinho
$router->group(['prefix' => '/carrinho'], function ($router) {
    $router->get('/', [LojaCarrinhoController::class, 'index']);
    $router->post('/adicionar', [BackendCarrinhoController::class, 'adicionar']);
    $router->post('/atualizar', [BackendCarrinhoController::class, 'atualizar']);
    $router->post('/remover', [BackendCarrinhoController::class, 'remover']);
    $router->post('/limpar', [BackendCarrinhoController::class, 'limpar']);
});

// Rotas do checkout
$router->group(['prefix' => '/checkout'], function ($router) {

    $router->post('/processar', [BackendCheckoutController::class, 'processar']);
    $router->get('/sucesso/{id}', [BackendCheckoutController::class, 'sucesso']);
    $router->get('/cancelado', [BackendCheckoutController::class, 'cancelado']);
});