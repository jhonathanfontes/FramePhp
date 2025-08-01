<?php

use App\Controllers\Site\HomeController;
use App\Controllers\Site\MailController;
use App\Controllers\Site\ShopController;
use App\Controllers\Site\CartController;
use App\Controllers\Auth\AuthController;
use App\Controllers\Admin\AdminController;
use App\Controllers\Api\ApiController;
use App\Controllers\Client\ClientController;
use App\Controllers\Error\ErrorController;
use App\Controllers\Admin\MenuController;
use App\Middleware\AuthenticationMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\LocaleMiddleware;

/*
|--------------------------------------------------------------------------
| Todas as rotas publicas do aplicativo
|   deve ser definidas antes das rotas protegidas
|   e depois das rotas de API protegidas
|   para garantir que as rotas corretas sejam
|   chamadas.
|--------------------------------------------------------------------------
|
|   $router->get('/rota', [Controller::class, 'metodo'])->name('nome-da-rota');
|--------------------------------------------------------------------------
*/

// Rotas públicas com middleware de localização e CSRF
$router->middleware([LocaleMiddleware::class, CsrfMiddleware::class])
    ->group([], function ($router) {
        // Rotas públicas
        $router->get('/mail', [MailController::class, 'index']);
        $router->post('/mail/send', [MailController::class, 'send']);
        $router->get('/', [HomeController::class, 'index']);
        $router->get('/home', [HomeController::class, 'index']);
        $router->get('/about', [HomeController::class, 'about'])->name('about');
        $router->get('/contact', [HomeController::class, 'contact'])->name('contact');
        
        // Rotas da Loja
        $router->get('/shop', [ShopController::class, 'index'])->name('shop');
        $router->get('/shop/categoria/{id}', [ShopController::class, 'categoria'])->name('shop.categoria');
        $router->get('/shop/produto/{id}', [ShopController::class, 'produto'])->name('shop.produto');
        $router->get('/shop/buscar', [ShopController::class, 'buscar'])->name('shop.buscar');
        
        // Rotas do Carrinho
        $router->get('/carrinho', [CartController::class, 'index'])->name('carrinho');
        $router->post('/carrinho/adicionar', [CartController::class, 'adicionar'])->name('carrinho.adicionar');
        $router->post('/carrinho/atualizar', [CartController::class, 'atualizar'])->name('carrinho.atualizar');
        $router->get('/carrinho/remover/{id}', [CartController::class, 'remover'])->name('carrinho.remover');
        $router->get('/carrinho/limpar', [CartController::class, 'limpar'])->name('carrinho.limpar');
    });

// Rotas de autenticação (apenas para visitantes)
$router->middleware([GuestMiddleware::class])
    ->group([], function ($router) {

        $router->group(['prefix' => 'auth'], function ($router) {
            $router->get('/login', [AuthController::class, 'loginForm'])->name('login');
            $router->post('/login', [AuthController::class, 'login']);
        });

        $router->get('/register', [AuthController::class, 'registerForm'])->name('register');
        $router->post('/register', [AuthController::class, 'register']);
        $router->get('/forgot-password', [AuthController::class, 'forgotPasswordForm'])->name('forgot-password');
        $router->post('/forgot-password', [AuthController::class, 'forgotPassword']);
        $router->get('/reset-password/{token}', [AuthController::class, 'resetPasswordForm'])->name('esqueci-senha');
        $router->post('/reset-password', [AuthController::class, 'resetPassword']);
    });

// Rotas protegidas para usuários web
$router->middleware([AuthenticationMiddleware::class])->group([], function ($router) {
    $router->get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    $router->get('/logout', [AuthController::class, 'logout'])->name('logout');

});