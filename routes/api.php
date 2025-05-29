<?php

use App\Controllers\Api\ApiController;

/*
|--------------------------------------------------------------------------
| Rotas da API
|--------------------------------------------------------------------------
|
| Aqui são definidas todas as rotas para a API do aplicativo.
| Essas rotas são geralmente prefixadas com /api e protegidas
| por middleware de autenticação de API.
|
*/

$router->middleware(['auth.api'])->group(['prefix' => 'api'], function ($router) {
    $router->get('/user', [ApiController::class, 'user'])->name('api.user');
    $router->get('/data', [ApiController::class, 'data'])->name('api.data');

    // Rotas com verificação de permissão adicional
    $router->middleware(['permission.admin'])->group([], function ($router) { // O prefixo /api já foi aplicado
        $router->get('/admin/stats', [ApiController::class, 'adminStats'])->name('api.admin.stats');
    });
});