<?php

use App\Controllers\Painel\DashboardController;
use App\Controllers\Painel\EmpresasController;
use App\Controllers\Painel\UsuariosController;
use App\Controllers\Painel\RelatoriosController;
use App\Controllers\Auth\AuthController;

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


$router->group([
    'prefix' => 'loja',
    'middleware' => ['csrf']
], function ($router) {
    // Páginas institucionais
    $router->get('/sobre', [DashboardController::class, 'sobre'])->name('loja.sobre');
    $router->get('/contato', [DashboardController::class, 'contato'])->name('loja.contato');
    $router->post('/contato/enviar', [DashboardController::class, 'enviarContato'])->name('loja.contato.enviar');
    $router->get('/loja', [DashboardController::class, 'loja'])->name('loja.loja');
    // Catálogo de produtos
    $router->get('/catalogo', [DashboardController::class, 'catalogo'])->name('loja.catalogo');
    $router->get('/produto/{id}', [DashboardController::class, 'produto'])->name('loja.produto');
    // Rotas de autenticação (apenas para convidados/não logados)
});

// Rotas de autenticação do painel
$router->group(['prefix' => '/painel'], function () {
    $router->get('/login', [AuthController::class, 'loginPainel']);
    $router->post('/login', [AuthController::class, 'loginPainel']);
    $router->get('/logout', [AuthController::class, 'logout']);
});

// Rotas protegidas do painel
$router->group(['prefix' => '/painel', 'middleware' => 'auth:painel'], function () {
    // Dashboard
    $router->get('/', [DashboardController::class, 'index']);
    $router->get('/dashboard', [DashboardController::class, 'index']);

    // Empresas
    $router->group(['prefix' => '/empresas'], function () {
        $router->get('/', [EmpresasController::class, 'index']);
        $router->get('/create', [EmpresasController::class, 'create']);
        $router->post('/', [EmpresasController::class, 'store']);
        $router->get('/{id}', [EmpresasController::class, 'show']);
        $router->get('/{id}/edit', [EmpresasController::class, 'edit']);
        $router->put('/{id}', [EmpresasController::class, 'update']);
        $router->delete('/{id}', [EmpresasController::class, 'destroy']);
        $router->post('/{id}/toggle-status', [EmpresasController::class, 'toggleStatus']);
        $router->get('/{id}/usuarios', [EmpresasController::class, 'usuarios']);
        $router->get('/{id}/estatisticas', [EmpresasController::class, 'estatisticas']);
    });

    // Usuários do painel
    $router->group(['prefix' => '/usuarios'], function () {
        $router->get('/', [UsuariosController::class, 'index']);
        $router->get('/create', [UsuariosController::class, 'create']);
        $router->post('/', [UsuariosController::class, 'store']);
        $router->get('/{id}/edit', [UsuariosController::class, 'edit']);
        $router->put('/{id}', [UsuariosController::class, 'update']);
        $router->delete('/{id}', [UsuariosController::class, 'destroy']);
        $router->post('/{id}/toggle-status', [UsuariosController::class, 'toggleStatus']);
        $router->post('/{id}/reset-senha', [UsuariosController::class, 'resetSenha']);
    });

    // Relatórios gerais
    $router->group(['prefix' => '/relatorios'], function () {
        $router->get('/', [RelatoriosController::class, 'index']);
        $router->get('/empresas', [RelatoriosController::class, 'empresas']);
        $router->get('/vendas-gerais', [RelatoriosController::class, 'vendasGerais']);
        $router->get('/usuarios', [RelatoriosController::class, 'usuarios']);
        $router->get('/financeiro', [RelatoriosController::class, 'financeiro']);
        $router->get('/exportar/{tipo}', [RelatoriosController::class, 'exportar']);
    });

    // Configurações do sistema
    $router->group(['prefix' => '/configuracoes'], function () {
        $router->get('/', [DashboardController::class, 'configuracoes']);
        $router->post('/', [DashboardController::class, 'configuracoes']);
        $router->get('/backup', [DashboardController::class, 'backup']);
        $router->post('/backup', [DashboardController::class, 'backup']);
        $router->get('/logs', [DashboardController::class, 'logs']);
    });
}); 