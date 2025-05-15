<?php

use App\Controllers\Site\HomeController;
use App\Controllers\Auth\AuthController;
use App\Middleware\AuthMiddleware;

return function($router) {
    // Rotas públicas
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/home', [HomeController::class, 'index']);
    $router->get('/about', [HomeController::class, 'about'])->name('about');
    $router->get('/contact', [HomeController::class, 'contact'])->name('contact');

    // Rotas de autenticação
    $router->get('/login', [AuthController::class, 'loginForm'])->name('login');
    $router->post('/login', [AuthController::class, 'login']);
    $router->get('/register', [AuthController::class, 'registerForm'])->name('register');
    $router->post('/register', [AuthController::class, 'register']);
    $router->get('/forgot-password', [AuthController::class, 'forgotPasswordForm'])->name('forgot-password');
    $router->post('/forgot-password', [AuthController::class, 'forgotPassword']);
    $router->get('/reset-password/{token}', [AuthController::class, 'resetPasswordForm'])->name('reset-password');
    $router->post('/reset-password', [AuthController::class, 'resetPassword']);
    $router->get('/logout', [AuthController::class, 'logout'])->name('logout');
    $router->get('/unauthorized', [AuthController::class, 'unauthorized'])->name('unauthorized');

    // Rotas protegidas
    $router->prefix('dashboard')
        ->middleware([AuthMiddleware::class])
        ->group([], function($router) {
            $router->get('/', [HomeController::class, 'dashboard'])->name('dashboard');
            $router->get('/profile', [HomeController::class, 'profile'])->name('profile');
        });
};