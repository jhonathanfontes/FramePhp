<?php

use App\Controllers\Site\HomeController;
use App\Controllers\Auth\AuthController;
use App\Controllers\Admin\AdminController;
use App\Controllers\Api\ApiController;
use App\Controllers\Client\ClientController;
use App\Controllers\Error\ErrorController;
use App\Middleware\AuthenticationMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\LocaleMiddleware;
use App\Middleware\PermissionMiddleware;
use App\Middleware\GuestMiddleware;
use App\Controllers\Admin\MenuController;

// Rotas de erro
$router->get('/error/{id}', [ErrorController::class, 'show'])->name('error.show');
$router->get('/error/list', [ErrorController::class, 'list'])->name('error.list');

// Rotas públicas com middleware de localização e CSRF
$router->middleware([LocaleMiddleware::class, CsrfMiddleware::class])->group([], function ($router) {
    // Rotas públicas
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/home', [HomeController::class, 'index']);
    $router->get('/about', [HomeController::class, 'about'])->name('about');
    $router->get('/contact', [HomeController::class, 'contact'])->name('contact');

    // Rotas de autenticação (apenas para visitantes)
    $router->middleware([GuestMiddleware::class])->group([], function ($router) {
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
});

// Rotas protegidas para usuários web
$router->middleware([new AuthenticationMiddleware('web', '/login')])->group([], function ($router) {
    $router->get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    $router->get('/profile', [HomeController::class, 'profile'])->name('profile');
    $router->post('/profile/update', [HomeController::class, 'updateProfile'])->name('profile.update');

    $router->get('/logout', [AuthController::class, 'logout'])->name('logout');
    $router->get('/unauthorized', [AuthController::class, 'unauthorized'])->name('unauthorized');
});

// Rotas protegidas para administradores
$router->middleware([new AuthenticationMiddleware('admin', '/admin/login')])->group([], function ($router) {
    $router->group(['prefix' => 'admin'], function ($router) {
        $router->get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    });
    $router->get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    $router->get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');

    $router->group(['prefix' => 'admin/menus'], function ($router) {
        $router->get('/', [MenuController::class, 'index'])->name('admin.menus.index');
        $router->get('/create', [MenuController::class, 'create'])->name('admin.menus.create');
        $router->post('/store', [MenuController::class, 'store'])->name('admin.menus.store');
        $router->get('/{id}/edit', [MenuController::class, 'edit'])->name('admin.menus.edit');
        $router->post('/{id}/update', [MenuController::class, 'update'])->name('admin.menus.update');
        $router->get('/{id}/destroy', [MenuController::class, 'destroy'])->name('admin.menus.destroy');
        
        // Rotas para submenus
        $router->get('/{menuId}/submenus/create', [MenuController::class, 'createSubmenu'])->name('admin.menus.submenus.create');
        $router->post('/{menuId}/submenus/store', [MenuController::class, 'storeSubmenu'])->name('admin.menus.submenus.store');
        $router->get('/submenus/{id}/edit', [MenuController::class, 'editSubmenu'])->name('admin.menus.submenus.edit');
        $router->post('/submenus/{id}/update', [MenuController::class, 'updateSubmenu'])->name('admin.menus.submenus.update');
        $router->get('/submenus/{id}/destroy', [MenuController::class, 'destroySubmenu'])->name('admin.menus.submenus.destroy');
    });
});

// Rotas protegidas para clientes
$router->middleware([new AuthenticationMiddleware('client', '/client/login')])->group([], function ($router) {
    $router->get('/client/dashboard', [ClientController::class, 'dashboard'])->name('client.dashboard');
    $router->get('/client/orders', [ClientController::class, 'orders'])->name('client.orders');
    $router->get('/client/profile', [ClientController::class, 'profile'])->name('client.profile');
});

// Rotas de API protegidas
$router->middleware([new AuthenticationMiddleware('api')])->group([], function ($router) {
    $router->get('/api/user', [ApiController::class, 'user'])->name('api.user');
    $router->get('/api/data', [ApiController::class, 'data'])->name('api.data');

    // Rotas com verificação de permissão adicional
    $router->middleware([new PermissionMiddleware('admin')])->group([], function ($router) {
        $router->get('/api/admin/stats', [ApiController::class, 'adminStats'])->name('api.admin.stats');
    });
});
