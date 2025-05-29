<?php

use App\Controllers\Client\ClientController;

/*
|--------------------------------------------------------------------------
| Rotas do Cliente
|--------------------------------------------------------------------------
|
| Aqui são definidas todas as rotas para a seção do cliente
| do aplicativo. Essas rotas são agrupadas e protegidas
| por middleware específico de cliente.
|
*/

$router->middleware(['auth.client'])->group(['prefix' => 'client'], function ($router) {
    $router->get('/dashboard', [ClientController::class, 'dashboard'])->name('client.dashboard');
    $router->get('/orders', [ClientController::class, 'orders'])->name('client.orders');
    $router->get('/profile', [ClientController::class, 'profile'])->name('client.profile');
});