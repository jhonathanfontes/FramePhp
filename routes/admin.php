<?php

use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\ProdutosController;
use App\Controllers\Admin\CategoriasController;
use App\Controllers\Admin\VendasController;
use App\Controllers\Admin\PessoasController;
use App\Controllers\Admin\UsuariosController;
use App\Controllers\Admin\EstoqueController;
use App\Controllers\Admin\RelatoriosController;
use App\Controllers\Auth\AuthController;

// Rotas de autenticação do admin
Route::group(['prefix' => '/admin'], function () {
    Route::get('/login', [AuthController::class, 'loginAdmin']);
    Route::post('/login', [AuthController::class, 'loginAdmin']);
    Route::get('/logout', [AuthController::class, 'logout']);
});

// Rotas protegidas do admin
Route::group(['prefix' => '/admin', 'middleware' => 'auth:admin'], function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/relatorios', [DashboardController::class, 'relatorios']);
    Route::get('/configuracoes', [DashboardController::class, 'configuracoes']);
    Route::post('/configuracoes', [DashboardController::class, 'configuracoes']);

    // Produtos
    Route::group(['prefix' => '/produtos'], function () {
        Route::get('/', [ProdutosController::class, 'index']);
        Route::get('/create', [ProdutosController::class, 'create']);
        Route::post('/', [ProdutosController::class, 'store']);
        Route::get('/{id}', [ProdutosController::class, 'show']);
        Route::get('/{id}/edit', [ProdutosController::class, 'edit']);
        Route::put('/{id}', [ProdutosController::class, 'update']);
        Route::delete('/{id}', [ProdutosController::class, 'destroy']);
        Route::post('/{id}/toggle-status', [ProdutosController::class, 'toggleStatus']);
        Route::post('/importar', [ProdutosController::class, 'importar']);
        Route::get('/exportar', [ProdutosController::class, 'exportar']);
    });

    // Categorias
    Route::group(['prefix' => '/categorias'], function () {
        Route::get('/', [CategoriasController::class, 'index']);
        Route::get('/create', [CategoriasController::class, 'create']);
        Route::post('/', [CategoriasController::class, 'store']);
        Route::get('/{id}/edit', [CategoriasController::class, 'edit']);
        Route::put('/{id}', [CategoriasController::class, 'update']);
        Route::delete('/{id}', [CategoriasController::class, 'destroy']);
        Route::post('/{id}/toggle-status', [CategoriasController::class, 'toggleStatus']);
    });

    // Vendas
    Route::group(['prefix' => '/vendas'], function () {
        Route::get('/', [VendasController::class, 'index']);
        Route::get('/create', [VendasController::class, 'create']);
        Route::post('/', [VendasController::class, 'store']);
        Route::get('/{id}', [VendasController::class, 'show']);
        Route::get('/{id}/edit', [VendasController::class, 'edit']);
        Route::put('/{id}', [VendasController::class, 'update']);
        Route::delete('/{id}', [VendasController::class, 'destroy']);
        Route::post('/{id}/atualizar-status', [VendasController::class, 'atualizarStatus']);
        Route::get('/{id}/imprimir', [VendasController::class, 'imprimir']);
        Route::get('/{id}/email', [VendasController::class, 'enviarEmail']);
    });

    // Pessoas (Clientes)
    Route::group(['prefix' => '/pessoas'], function () {
        Route::get('/', [PessoasController::class, 'index']);
        Route::get('/create', [PessoasController::class, 'create']);
        Route::post('/', [PessoasController::class, 'store']);
        Route::get('/{id}', [PessoasController::class, 'show']);
        Route::get('/{id}/edit', [PessoasController::class, 'edit']);
        Route::put('/{id}', [PessoasController::class, 'update']);
        Route::delete('/{id}', [PessoasController::class, 'destroy']);
        Route::post('/{id}/toggle-status', [PessoasController::class, 'toggleStatus']);
        Route::get('/exportar', [PessoasController::class, 'exportar']);
    });

    // Usuários (Admin da empresa)
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

    // Estoque
    Route::group(['prefix' => '/estoque'], function () {
        Route::get('/', [EstoqueController::class, 'index']);
        Route::get('/baixo-estoque', [EstoqueController::class, 'baixoEstoque']);
        Route::post('/ajustar', [EstoqueController::class, 'ajustar']);
        Route::post('/entrada', [EstoqueController::class, 'entrada']);
        Route::post('/saida', [EstoqueController::class, 'saida']);
        Route::get('/movimentacoes', [EstoqueController::class, 'movimentacoes']);
        Route::get('/relatorio', [EstoqueController::class, 'relatorio']);
    });

    // Relatórios
    Route::group(['prefix' => '/relatorios'], function () {
        Route::get('/vendas', [RelatoriosController::class, 'vendas']);
        Route::get('/produtos', [RelatoriosController::class, 'produtos']);
        Route::get('/clientes', [RelatoriosController::class, 'clientes']);
        Route::get('/financeiro', [RelatoriosController::class, 'financeiro']);
        Route::get('/exportar/{tipo}', [RelatoriosController::class, 'exportar']);
    });
});