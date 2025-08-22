<?php

use App\Controllers\Painel\DashboardController as PainelDashboardController;
use App\Controllers\Painel\EmpresasController as PainelEmpresasController;
use App\Controllers\Painel\ProdutoController as PainelProdutoController;
use App\Controllers\Painel\UsuariosController as PainelUsuariosController;
use App\Controllers\Painel\RelatoriosController as PainelRelatoriosController;
use App\Controllers\Painel\AtividadesController as PainelAtividadesController;
use App\Controllers\Painel\EstabelecimentosController as PainelEstabelecimentosController;
use App\Controllers\Painel\ConfiguracoesController as PainelConfiguracoesController;
use App\Controllers\Painel\LogsController as PainelLogsController;
use App\Controllers\Painel\NotificacoesController as PainelNotificacoesController;
use App\Controllers\Painel\AjudaController as PainelAjudaController;
use App\Controllers\Painel\SuporteController as PainelSuporteController;
use App\Controllers\Painel\FinanceiroController as PainelFinanceiroController;
use App\Controllers\Painel\MarketingController as PainelMarketingController;
use App\Controllers\Painel\VendasController as PainelVendasController;
use App\Controllers\Painel\BackupController as PainelBackupController;

$router = \Core\Router\Router::getInstance();

/*
|--------------------------------------------------------------------------
| Rotas da Aplicação Web - Painel
|--------------------------------------------------------------------------
*/

// Adiciona as rotas de redirecionamento aqui.
$router->redirect('/painel', '/painel/dashboard');

$router->group([
    'prefix' => 'painel',
//    'middleware' => ['auth', 'permission:painel'] // Usa o alias 'permission' com o parâmetro 'painel'
], function ($router) {
     
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

    // Gerenciamento de Relatórios
    $router->get('/relatorios', [PainelRelatoriosController::class, 'index'])->name('painel.relatorios');

    // Gerenciamento de Atividades
    $router->get('/atividades', [PainelAtividadesController::class, 'index'])->name('painel.atividades');

    // Gerenciamento de Estabelecimentos
    $router->get('/estabelecimentos', [PainelEstabelecimentosController::class, 'index'])->name('painel.estabelecimentos');

    // Gerenciamento de Configurações
    $router->get('/configuracoes', [PainelConfiguracoesController::class, 'index'])->name('painel.configuracoes');

    // Gerenciamento de Logs
    $router->get('/log', [PainelLogsController::class, 'index'])->name('painel.log');

    // Gerenciamento de Notificações
    $router->get('/notificacoes', [PainelNotificacoesController::class, 'index'])->name('painel.notificacoes');

    // Gerenciamento de Ajuda
    $router->get('/ajuda', [PainelAjudaController::class, 'index'])->name('painel.ajuda');

    // Gerenciamento de Suporte
    $router->get('/suporte', [PainelSuporteController::class, 'index'])->name('painel.suporte');

    // Gerenciamento de Financeiro
    $router->get('/financeiro', [PainelFinanceiroController::class, 'index'])->name('painel.financeiro');

    // Gerenciamento de Marketing
    $router->get('/marketing', [PainelMarketingController::class, 'index'])->name('painel.marketing');

    // Gerenciamento de Vendas
    $router->get('/vendas', [PainelVendasController::class, 'index'])->name('painel.vendas');

    // Gerenciamento de Backup
    $router->get('/backup', [PainelBackupController::class, 'index'])->name('painel.backup');
    $router->post('/backup/executar', [PainelBackupController::class, 'executar'])->name('painel.backup.executar');
    $router->get('/backup/download/{nomeArquivo}', [PainelBackupController::class, 'download'])->name('painel.backup.download');
    $router->post('/backup/restaurar/{nomeArquivo}', [PainelBackupController::class, 'restaurar'])->name('painel.backup.restaurar');
    $router->post('/backup/excluir/{nomeArquivo}', [PainelBackupController::class, 'excluir'])->name('painel.backup.excluir');
});