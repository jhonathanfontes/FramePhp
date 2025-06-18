<?php

use App\Controllers\Site\HomeController;
use App\Controllers\Site\MailController;
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

// Rotas de erro
$router->get('/error/{id}', [ErrorController::class, 'show'])->name('error.show');
$router->get('/error/list', [ErrorController::class, 'list'])->name('error.list');

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
    });

// Rotas de autenticação (apenas para visitantes)
$router->middleware([GuestMiddleware::class])
    ->group([], function ($router) {

        $router->group(['prefix' => 'auth'], function ($router) {
            $router->get('/login', [AuthController::class, 'loginForm'])->name('AdminLogin');
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
    $router->get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    $router->get('/logout', [AuthController::class, 'logout'])->name('logout');

});

// Rotas protegidas para administradores
$router->middleware(['auth.admin'])
->group(['prefix' => 'admin'], function ($router) {
        $router->get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        $router->get('/users', [AdminController::class, 'users'])->name('admin.users');
        $router->get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
        $router->get('/logout', [AuthController::class, 'logout'])->name('admin_logout');


        // Rotas para menus
        $router->group(['prefix' => 'menus'], function ($router) {
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
$router->middleware(['auth.client'])->group([], function ($router) {
    $router->group(['prefix' => 'client'], function ($router) {
        $router->get('/dashboard', [ClientController::class, 'dashboard'])->name('client.dashboard');
        $router->get('/orders', [ClientController::class, 'orders'])->name('client.orders');
        $router->get('/profile', [ClientController::class, 'profile'])->name('client.profile');
    });
});

// Rotas de API protegidas
$router->middleware(['auth.api'])->group([], function ($router) {
    $router->get('/api/user', [ApiController::class, 'user'])->name('api.user');
    $router->get('/api/data', [ApiController::class, 'data'])->name('api.data');

    // Rotas com verificação de permissão adicional
    $router->middleware(['permission.admin'])->group([], function ($router) {
        $router->get('/api/admin/stats', [ApiController::class, 'adminStats'])->name('api.admin.stats');
    });
});

// Rota de teste deve vir ANTES da rota coringa
$router->get('/teste', function () {
    echo "Rota de teste funcionando!";
    exit;
});

// Configurar um ExceptionHandler para lidar com rotas não encontradas
$router->setFallback(function () {
    $error = [
        'type' => 'NotFoundError',
        'message' => 'A página solicitada não foi encontrada',
        'file' => __FILE__,
        'line' => __LINE__,
        'timestamp' => date('Y-m-d H:i:s')
    ];

    $errorHandler = \Core\Error\ErrorHandler::getInstance();
    $errorHandler->renderErrorPage($error);
});
