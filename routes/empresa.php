
<?php

use App\Controllers\Admin\EmpresaController;
use App\Middleware\AuthenticationMiddleware;

/*
|--------------------------------------------------------------------------
| Rotas de Gerenciamento de Empresas
|--------------------------------------------------------------------------
|
| Rotas para administração de empresas no sistema multi-tenant
|
*/

$router->middleware([AuthenticationMiddleware::class])
    ->group(['prefix' => 'admin/empresas'], function ($router) {
        
        // Listagem e CRUD de empresas
        $router->get('/', [EmpresaController::class, 'index'])->name('admin.empresas.index');
        $router->get('/create', [EmpresaController::class, 'create'])->name('admin.empresas.create');
        $router->post('/store', [EmpresaController::class, 'store'])->name('admin.empresas.store');
        $router->get('/{id}/edit', [EmpresaController::class, 'edit'])->name('admin.empresas.edit');
        $router->post('/{id}/update', [EmpresaController::class, 'update'])->name('admin.empresas.update');
        $router->delete('/{id}/delete', [EmpresaController::class, 'delete'])->name('admin.empresas.delete');
        
        // Gerenciamento de lojas da empresa
        $router->get('/{id}/lojas', [EmpresaController::class, 'lojas'])->name('admin.empresas.lojas');
        $router->get('/{id}/lojas/create', [EmpresaController::class, 'createLoja'])->name('admin.empresas.lojas.create');
        $router->post('/{id}/lojas/store', [EmpresaController::class, 'storeLoja'])->name('admin.empresas.lojas.store');
});
