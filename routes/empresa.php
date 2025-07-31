
<?php

use App\Controllers\Admin\EmpresaController;
use App\Middleware\AuthenticationMiddleware;
use App\Middleware\PermissionMiddleware;
use App\Middleware\CsrfMiddleware;

/*
|--------------------------------------------------------------------------
| Rotas de Administração de Empresas
|--------------------------------------------------------------------------
|
| Rotas protegidas para gerenciamento de empresas
|
*/

// Rotas administrativas de empresas (requer autenticação e permissão de admin)
$router->middleware([AuthenticationMiddleware::class, CsrfMiddleware::class])
    ->group(['prefix' => 'admin'], function ($router) {
        
        // Rotas que requerem permissão de super admin
        $router->middleware(['permission:super_admin'])
            ->group(['prefix' => 'empresas'], function ($router) {
                
                // CRUD de empresas
                $router->get('/', [EmpresaController::class, 'index'])->name('admin.empresas.index');
                $router->get('/create', [EmpresaController::class, 'create'])->name('admin.empresas.create');
                $router->post('/store', [EmpresaController::class, 'store'])->name('admin.empresas.store');
                $router->get('/{id}/edit', [EmpresaController::class, 'edit'])->name('admin.empresas.edit');
                $router->put('/{id}/update', [EmpresaController::class, 'update'])->name('admin.empresas.update');
                $router->delete('/{id}/delete', [EmpresaController::class, 'delete'])->name('admin.empresas.delete');
                
                // Ativar/Desativar empresa
                $router->patch('/{id}/toggle-status', [EmpresaController::class, 'toggleStatus'])->name('admin.empresas.toggle-status');
                
                // Relatórios de empresa
                $router->get('/{id}/relatorio', [EmpresaController::class, 'relatorio'])->name('admin.empresas.relatorio');
            });
        
        // Rotas que requerem permissão de admin da empresa
        $router->middleware(['permission:admin,empresa_admin'])
            ->group(['prefix' => 'empresa'], function ($router) {
                
                // Configurações da empresa atual
                $router->get('/configuracoes', [EmpresaController::class, 'configuracoes'])->name('admin.empresa.configuracoes');
                $router->put('/configuracoes/update', [EmpresaController::class, 'updateConfiguracoes'])->name('admin.empresa.configuracoes.update');
                
                // Lojas da empresa
                $router->get('/lojas', [EmpresaController::class, 'lojas'])->name('admin.empresa.lojas');
                $router->post('/lojas/create', [EmpresaController::class, 'createLoja'])->name('admin.empresa.lojas.create');
                $router->put('/lojas/{id}/update', [EmpresaController::class, 'updateLoja'])->name('admin.empresa.lojas.update');
                
                // Usuários da empresa
                $router->get('/usuarios', [EmpresaController::class, 'usuarios'])->name('admin.empresa.usuarios');
                $router->post('/usuarios/convidar', [EmpresaController::class, 'convidarUsuario'])->name('admin.empresa.usuarios.convidar');
            });
    });

// API para empresas com autenticação JWT
$router->middleware(['jwt', 'api.rate'])
    ->group(['prefix' => 'api/empresas'], function ($router) {
        
        // Listar empresas (apenas para super admin)
        $router->get('/', [EmpresaController::class, 'apiIndex'])->middleware(['permission:super_admin'])->name('api.empresas.index');
        
        // Dados da empresa atual
        $router->get('/current', [EmpresaController::class, 'apiCurrent'])->name('api.empresas.current');
        
        // Estatísticas da empresa
        $router->get('/{id}/stats', [EmpresaController::class, 'apiStats'])->name('api.empresas.stats');
    });
