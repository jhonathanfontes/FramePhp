<?php

use App\Controllers\Auth\AuthController;
use App\Controllers\Admin\AdminController;
use App\Controllers\Site\ClientController;
use App\Controllers\Site\HomeController;
use App\Controllers\UserController;
use Core\Error\ErrorHandler;

$router = \Core\Router\Router::getInstance();

/*
|--------------------------------------------------------------------------
| Rotas da Aplicação Web
|--------------------------------------------------------------------------
*/

// Rotas públicas (para todos os visitantes)
$router->group(['middleware' => ['locale', 'csrf']], function ($router) {
    $router->get('/', [HomeController::class, 'index'])->name('home');

    // Páginas institucionais
        
        $router->get('/contato', [ClientController::class, 'contato'])->name('client.contato');
        $router->post('/contato/enviar', [ClientController::class, 'enviarContato'])->name('client.contato.enviar');
        $router->get('/loja', [ClientController::class, 'loja'])->name('client.loja');
});

$router->get('/sobre', [ClientController::class, 'sobre'])->name('client.sobre');

// Rotas de autenticação (apenas para convidados/não logados)
$router->group(['middleware' => ['guest', 'csrf']], function ($router) {
    $router->get('/home', [HomeController::class, 'index'])->name('home');
});

$router->group(['prefix' => 'auth', 'middleware' => ['guest', 'csrf']], function ($router) {
    // A rota para '/login' se torna '/auth/login'
    $router->get('/login', [AuthController::class, 'loginForm'])->name('AdminLogin');
    $router->post('/login', [AuthController::class, 'login']);
    $router->get('/register', [AuthController::class, 'registerForm'])->name('register');
    $router->post('/register', [AuthController::class, 'register']);
    $router->get('/forgot-password', [AuthController::class, 'forgotPasswordForm'])->name('forgot-password');
    $router->post('/forgot-password', [AuthController::class, 'forgotPassword']);
    $router->get('/reset-password/{token}', [AuthController::class, 'resetPasswordForm'])->name('esqueci-senha');
    $router->post('/reset-password', [AuthController::class, 'resetPassword']);
});

/*
|--------------------------------------------------------------------------
| Rotas da Área Administrativa
|--------------------------------------------------------------------------
*/

// Agora, ele usa 'auth' para garantir que o usuário está logado E
// 'permission:admin' para garantir que ele tem a permissão de administrador.
$router->group([
    'prefix' => 'admin',
    'middleware' => ['auth', 'permission:admin', 'csrf']
], function ($router) {
    $router->get('/logout', [AuthController::class, 'logout'])->name('logout');
    $router->get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    $router->get('/users', [AdminController::class, 'users'])->name('users.dashboard');

    // Adicione outras rotas de administrador aqui
});

// Exemplo de um grupo de rotas para CRUD de usuários com policies granulares
// A verificação da policy seria feita dentro de cada método do UserController
$router->group(['prefix' => 'users', 'middleware' => ['auth', 'csrf']], function ($router) {
    // Rota com restrição de parâmetro: {id} deve ser numérico
    $router->get('/{id}', [UserController::class, 'show'])->where('id', '[0-9]+')->name('users.show');

    // Supondo que em UserController@update, você chamaria a UserPolicy
    $router->put('/{id}', [UserController::class, 'update'])->where('id', '[0-9]+')->name('users.update');

    // Supondo que em UserController@destroy, você chamaria a UserPolicy
    $router->delete('/{id}', [UserController::class, 'destroy'])->where('id', '[0-9]+')->name('users.destroy');
});


/*
|--------------------------------------------------------------------------
| Rotas da Área Administrativa
|--------------------------------------------------------------------------
*/
$router->group([
    'prefix' => 'admin',
    'middleware' => ['auth', 'permission:admin'] // Usa o alias 'permission' com o parâmetro 'admin'
], function ($router) {
   // $router->get('/', $router->redirect('dashboard'));
    $router->get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    $router->get('/users', [AdminController::class, 'users'])->name('admin.users');
    $router->get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    $router->get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    $router->get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    $router->get('/products', [AdminController::class, 'products'])->name('admin.products');
    $router->get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
});


$router->group([
    'prefix' => 'user',
    'middleware' => ['auth', 'permission:admin,user'] // Usa o alias 'permission' com o parâmetro 'admin'
], function ($router) {
  //  $router->get('/', $router->redirect('dashboard'));
    $router->get('/dashboard', [AdminController::class, 'dashboard'])->name('user.dashboard');
    $router->get('/users', [AdminController::class, 'users'])->name('user.users');
    $router->get('/settings', [AdminController::class, 'settings'])->name('user.settings');
    $router->get('/orders', [AdminController::class, 'orders'])->name('user.orders');
    $router->get('/reports', [AdminController::class, 'reports'])->name('user.reports');
    $router->get('/products', [AdminController::class, 'products'])->name('user.products');
    $router->get('/profile', [AdminController::class, 'profile'])->name('user.profile');
});

/*
|--------------------------------------------------------------------------
| Rotas da API
|--------------------------------------------------------------------------
*/
$router->group(['prefix' => 'api'], function ($router) {
    // Rotas de API que exigem um token JWT válido
    $router->group(['middleware' => ['jwt']], function ($router) {
        $router->get('/profile', [App\Controllers\Api\AuthController::class, 'me'])->name('api.me');
    });
});

/*
|--------------------------------------------------------------------------
| Rota de Fallback (404)
|--------------------------------------------------------------------------
*/
$router->setFallback(function () {
    $error = [
        'type' => 'NotFoundError',
        'message' => 'A página solicitada não foi encontrada.',
        'file' => __FILE__,
        'line' => __LINE__,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    ErrorHandler::getInstance()->renderErrorPage($error);
});