<?php

use App\Controllers\Painel\DashboardController;
use App\Controllers\Painel\EmpresasController;
use App\Controllers\Painel\UsuariosController;
use App\Controllers\Painel\RelatoriosController;
use App\Controllers\Auth\AuthController;

// Rotas de autenticação do painel
Route::group(['prefix' => '/painel'], function () {
    Route::get('/login', [AuthController::class, 'loginPainel']);
    Route::post('/login', [AuthController::class, 'loginPainel']);
    Route::get('/logout', [AuthController::class, 'logout']);
});

// Rotas protegidas do painel
Route::group(['prefix' => '/painel', 'middleware' => 'auth:painel'], function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Empresas
    Route::group(['prefix' => '/empresas'], function () {
        Route::get('/', [EmpresasController::class, 'index']);
        Route::get('/create', [EmpresasController::class, 'create']);
        Route::post('/', [EmpresasController::class, 'store']);
        Route::get('/{id}', [EmpresasController::class, 'show']);
        Route::get('/{id}/edit', [EmpresasController::class, 'edit']);
        Route::put('/{id}', [EmpresasController::class, 'update']);
        Route::delete('/{id}', [EmpresasController::class, 'destroy']);
        Route::post('/{id}/toggle-status', [EmpresasController::class, 'toggleStatus']);
        Route::get('/{id}/usuarios', [EmpresasController::class, 'usuarios']);
        Route::get('/{id}/estatisticas', [EmpresasController::class, 'estatisticas']);
    });

    // Usuários do painel
    Route::group(['prefix' => '/usuarios'], function () {
        Route::get('/', [UsuariosController::class, 'index']);
        Route::get('/create', [UsuariosController::class, 'create']);
        Route::post('/', [UsuariosController::class, 'store']);
        Route::get('/{id}/edit', [UsuariosController::class, 'edit']);
        Route::put('/{id}', [UsuariosController::class, 'update']);
        Route::delete('/{id}', [UsuariosController::class, 'destroy']);
        Route::post('/{id}/toggle-status', [UsuariosController::class, 'toggleStatus']);
        Route::post('/{id}/reset-senha', [UsuariosController::class, 'resetSenha']);
    });

    // Relatórios gerais
    Route::group(['prefix' => '/relatorios'], function () {
        Route::get('/', [RelatoriosController::class, 'index']);
        Route::get('/empresas', [RelatoriosController::class, 'empresas']);
        Route::get('/vendas-gerais', [RelatoriosController::class, 'vendasGerais']);
        Route::get('/usuarios', [RelatoriosController::class, 'usuarios']);
        Route::get('/financeiro', [RelatoriosController::class, 'financeiro']);
        Route::get('/exportar/{tipo}', [RelatoriosController::class, 'exportar']);
    });

    // Configurações do sistema
    Route::group(['prefix' => '/configuracoes'], function () {
        Route::get('/', [DashboardController::class, 'configuracoes']);
        Route::post('/', [DashboardController::class, 'configuracoes']);
        Route::get('/backup', [DashboardController::class, 'backup']);
        Route::post('/backup', [DashboardController::class, 'backup']);
        Route::get('/logs', [DashboardController::class, 'logs']);
    });
}); 