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
    
    // Gerenciamento de Empresas
    $router->get('/empresas', [PainelEmpresasController::class, 'index'])->name('painel.empresas');
    $router->get('/empresa/create', [PainelEmpresasController::class, 'create'])->name('painel.empresa.create');
    $router->get('/empresa/{id}', [PainelEmpresasController::class, 'gerenciar'])->name('painel.empresa.gerenciar');    
   
    // Gerenciamento de Usuários
    $router->get('/usuarios', [PainelUsuariosController::class, 'index'])->name('painel.usuarios');
    $router->get('/usuario/create', [PainelUsuariosController::class, 'create'])->name('painel.usuario.create');
    $router->get('/usuario/{id}', [PainelUsuariosController::class, 'gerenciar'])->name('painel.usuario.gerenciar');
});