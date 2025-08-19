<?php

use App\Controllers\Painel\DashboardController as PainelDashboardController;
use App\Controllers\Painel\EmpresasController as PainelEmpresasController;
use App\Controllers\Painel\ProdutoController as PainelProdutoController;
use App\Controllers\Painel\UsuariosController as PainelUsuariosController;

$router = \Core\Router\Router::getInstance();

/*
|--------------------------------------------------------------------------
| Rotas da Aplicação Web - Painel
|--------------------------------------------------------------------------
*/

$router->group([
    'prefix' => 'painel',
//    'middleware' => ['auth', 'permission:painel'] // Usa o alias 'permission' com o parâmetro 'painel'
], function ($router) {
    // $router->get('/', $router->redirect('dashboard'));
    $router->get('/dashboard', [PainelDashboardController::class, 'index'])->name('painel.dashboard');
    $router->get('/produtos', [PainelProdutoController::class, 'index'])->name('painel.produtos');
    $router->get('/empresas', [PainelEmpresasController::class, 'index'])->name('painel.empresas');
    $router->get('/usuarios', [PainelUsuariosController::class, 'index'])->name('painel.usuarios');
});