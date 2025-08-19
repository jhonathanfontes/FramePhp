<?php

use App\Controllers\Painel\DashboardController;
use App\Controllers\Painel\EmpresasController;
use App\Controllers\Painel\UsuariosController;
use App\Controllers\Painel\RelatoriosController;
use App\Controllers\Painel\AuthController;

$router = \Core\Router\Router::getInstance();

// Rotas de autenticação do painel
$router->group(['prefix' => 'painel'], function ($router) {
    $router->get('/login', [AuthController::class, 'loginForm'])->name('painel.login');
    $router->post('/login', [AuthController::class, 'login'])->name('painel.login.post');
    $router->get('/logout', [AuthController::class, 'logout'])->name('painel.logout');
});

// Rotas protegidas do painel
$router->group([
    'prefix' => 'painel',
    'middleware' => ['superadmin', 'csrf']
], function ($router) {
    // Dashboard
    $router->get('/', [DashboardController::class, 'index'])->name('painel.dashboard');
    
    // Empresas
    $router->get('/empresas', [EmpresasController::class, 'index'])->name('painel.empresas');
    $router->get('/empresas/criar', [EmpresasController::class, 'create'])->name('painel.empresas.create');
    $router->post('/empresas', [EmpresasController::class, 'store'])->name('painel.empresas.store');
    $router->get('/empresas/{id}/editar', [EmpresasController::class, 'edit'])->name('painel.empresas.edit');
    $router->put('/empresas/{id}', [EmpresasController::class, 'update'])->name('painel.empresas.update');
    $router->delete('/empresas/{id}', [EmpresasController::class, 'destroy'])->name('painel.empresas.destroy');
    
    // Usuários
    $router->get('/usuarios', [UsuariosController::class, 'index'])->name('painel.usuarios');
    $router->get('/usuarios/criar', [UsuariosController::class, 'create'])->name('painel.usuarios.create');
    $router->post('/usuarios', [UsuariosController::class, 'store'])->name('painel.usuarios.store');
    $router->get('/usuarios/{id}/editar', [UsuariosController::class, 'edit'])->name('painel.usuarios.edit');
    $router->put('/usuarios/{id}', [UsuariosController::class, 'update'])->name('painel.usuarios.update');
    $router->delete('/usuarios/{id}', [UsuariosController::class, 'destroy'])->name('painel.usuarios.destroy');
    
    // Relatórios
    $router->get('/relatorios', [RelatoriosController::class, 'index'])->name('painel.relatorios');
    $router->get('/relatorios/empresas', [RelatoriosController::class, 'empresas'])->name('painel.relatorios.empresas');
    $router->get('/relatorios/usuarios', [RelatoriosController::class, 'usuarios'])->name('painel.relatorios.usuarios');
});