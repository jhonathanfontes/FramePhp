<?php

use App\Controllers\Admin\AdminController;
use App\Controllers\Admin\MenuController;
use App\Controllers\Auth\AuthController; // Para a rota de logout do admin, se necessário

/*
|--------------------------------------------------------------------------
| Rotas Administrativas
|--------------------------------------------------------------------------
|
| Aqui são definidas todas as rotas para a seção administrativa
| do aplicativo. Essas rotas são agrupadas e geralmente protegidas
| por middleware específico de administração.
|
*/

$router->middleware(['auth.admin']) // Middleware para proteger rotas de admin
    ->group(['prefix' => 'admin'], function ($router) {
        $router->get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        $router->get('/users', [AdminController::class, 'users'])->name('admin.users');
        $router->get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
        $router->get('/logout', [AuthController::class, 'logout'])->name('admin.logout'); // Rota de logout específica para admin

        // Rotas para menus
        $router->group(['prefix' => 'menus'], function ($router) {
            $router->get('/', [MenuController::class, 'index'])->name('admin.menus.index');
            $router->get('/create', [MenuController::class, 'create'])->name('admin.menus.create');
            $router->post('/store', [MenuController::class, 'store'])->name('admin.menus.store');
            $router->get('/{id}/edit', [MenuController::class, 'edit'])->name('admin.menus.edit');
            $router->post('/{id}/update', [MenuController::class, 'update'])->name('admin.menus.update');
            $router->get('/{id}/destroy', [MenuController::class, 'destroy'])->name('admin.menus.destroy');

            // Rotas para submenus
            $router->get('/{menuId}/submenus/create', [MenuController::class, 'createSubmenu'])->name('admin.menus.submenus.create');
            $router->post('/{menuId}/submenus/store', [MenuController::class, 'storeSubmenu'])->name('admin.menus.submenus.store');
            $router->get('/submenus/{id}/edit', [MenuController::class, 'editSubmenu'])->name('admin.menus.submenus.edit');
            $router->post('/submenus/{id}/update', [MenuController::class, 'updateSubmenu'])->name('admin.menus.submenus.update');
            $router->get('/submenus/{id}/destroy', [MenuController::class, 'destroySubmenu'])->name('admin.menus.submenus.destroy');
        });
    });
<?php

use App\Controllers\Admin\AdminController;
use App\Controllers\Admin\EmpresaController;
use App\Controllers\Admin\ReportController;
use App\Middleware\AuthenticationMiddleware;

// Aplicar middleware de autenticação para todas as rotas admin
$router->middleware([AuthenticationMiddleware::class])
    ->group(['prefix' => 'admin'], function ($router) {

        // Dashboard administrativo
        $router->get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        $router->get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard.alt');

        // Gestão de empresas
        $router->group(['prefix' => 'empresas'], function ($router) {
            $router->get('/', [EmpresaController::class, 'index'])->name('admin.empresas');
            $router->get('/create', [EmpresaController::class, 'create'])->name('admin.empresas.create');
            $router->post('/', [EmpresaController::class, 'store'])->name('admin.empresas.store');
            $router->get('/{id}/edit', [EmpresaController::class, 'edit'])->name('admin.empresas.edit');
            $router->put('/{id}', [EmpresaController::class, 'update'])->name('admin.empresas.update');
        });

        // Sistema de relatórios
        $router->group(['prefix' => 'reports'], function ($router) {
            $router->get('/', [ReportController::class, 'index'])->name('admin.reports');
            $router->get('/vendas', [ReportController::class, 'vendas'])->name('admin.reports.vendas');
            $router->get('/produtos', [ReportController::class, 'produtos'])->name('admin.reports.produtos');
            $router->get('/financeiro', [ReportController::class, 'financeiro'])->name('admin.reports.financeiro');
            $router->get('/empresas', [ReportController::class, 'empresas'])->name('admin.reports.empresas');
            $router->get('/vendas/pdf', [ReportController::class, 'exportVendasPdf'])->name('admin.reports.vendas.pdf');
            $router->get('/produtos/pdf', [ReportController::class, 'exportProdutosPdf'])->name('admin.reports.produtos.pdf');
        });
    });