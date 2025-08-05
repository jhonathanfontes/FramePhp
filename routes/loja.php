<?php

use App\Controllers\Loja\HomeController;
use App\Controllers\Loja\CarrinhoController;
use App\Controllers\Loja\CheckoutController;
use App\Controllers\Loja\PerfilController;
use App\Controllers\Auth\AuthController;

// Rotas públicas da loja
Route::group(['prefix' => '/'], function () {
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/produtos', [HomeController::class, 'produtos']);
    Route::get('/produto/{id}', [HomeController::class, 'produto']);
    Route::get('/categoria/{id}', [HomeController::class, 'produtos']);
    Route::get('/sobre', [HomeController::class, 'sobre']);
    Route::get('/contato', [HomeController::class, 'contato']);
    Route::get('/busca', [HomeController::class, 'produtos']);
});

// Rotas de autenticação da loja
Route::group(['prefix' => '/loja'], function () {
    Route::get('/login', [AuthController::class, 'loginLoja']);
    Route::post('/login', [AuthController::class, 'loginLoja']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/esqueci-senha', [AuthController::class, 'esqueciSenha']);
    Route::post('/esqueci-senha', [AuthController::class, 'esqueciSenha']);
    Route::get('/reset-senha/{token}', [AuthController::class, 'resetSenha']);
    Route::post('/reset-senha/{token}', [AuthController::class, 'resetSenha']);
});

// Rotas protegidas da loja
Route::group(['prefix' => '/loja', 'middleware' => 'auth:loja'], function () {
    Route::get('/perfil', [PerfilController::class, 'index']);
    Route::post('/perfil', [PerfilController::class, 'update']);
    Route::get('/meus-pedidos', [PerfilController::class, 'pedidos']);
    Route::get('/pedido/{id}', [PerfilController::class, 'pedido']);
});

// Rotas do carrinho
Route::group(['prefix' => '/carrinho'], function () {
    Route::get('/', [CarrinhoController::class, 'index']);
    Route::post('/adicionar', [CarrinhoController::class, 'adicionar']);
    Route::post('/atualizar', [CarrinhoController::class, 'atualizar']);
    Route::post('/remover', [CarrinhoController::class, 'remover']);
    Route::post('/limpar', [CarrinhoController::class, 'limpar']);
});

// Rotas do checkout
Route::group(['prefix' => '/checkout'], function () {
    Route::get('/', [CheckoutController::class, 'index']);
    Route::post('/processar', [CheckoutController::class, 'processar']);
    Route::get('/sucesso/{id}', [CheckoutController::class, 'sucesso']);
    Route::get('/cancelado', [CheckoutController::class, 'cancelado']);
}); 