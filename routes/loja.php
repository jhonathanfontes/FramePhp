<?php

use App\Controllers\Backend\Loja\CarrinhoController as BackendCarrinhoController;
use App\Controllers\Backend\Loja\CheckoutController as BackendCheckoutController;
use App\Controllers\Backend\Loja\UsuarioController as BackendUsuarioController;
use App\Controllers\Loja\CarrinhoController as LojaCarrinhoController;
use App\Controllers\Loja\HomeController as LojaHomeController;
use App\Controllers\Loja\UsuarioController as LojaUsuarioController;
use App\Controllers\Loja\ProdutoController as LojaProdutoController;
use App\Controllers\Loja\CategoriaController as LojaCategoriaController;
use App\Controllers\Loja\BuscaController as LojaBuscaController;
use App\Controllers\Loja\SobreController as LojaSobreController;
use App\Controllers\Loja\ContatoController as LojaContatoController;
use App\Controllers\Loja\FavoritoController as LojaFavoritoController;
use App\Controllers\Loja\NewsletterController as LojaNewsletterController;
use App\Controllers\Site\AuthController as SiteAuthController;

$router = \Core\Router\Router::getInstance();

/*
|--------------------------------------------------------------------------
| Rotas da Loja - Estrutura Robusta e Organizada
|--------------------------------------------------------------------------
*/

// ===== ROTAS PÚBLICAS =====
$router->group(['middleware' => ['locale', 'csrf']], function ($router) {
    // Home
    $router->get('/', [LojaHomeController::class, 'index'])->name('loja.home');
    
    // Produtos
    $router->get('/produtos', [LojaProdutoController::class, 'index'])->name('loja.produtos');
    $router->get('/produto/{id}', [LojaProdutoController::class, 'show'])->name('loja.produto');
    $router->get('/produto/{id}/quick-view', [LojaProdutoController::class, 'quickView'])->name('loja.produto.quick-view');
    
    // Categorias
    $router->get('/categoria/{id}', [LojaCategoriaController::class, 'show'])->name('loja.categoria');
    $router->get('/categorias', [LojaCategoriaController::class, 'index'])->name('loja.categorias');
    
    // Busca
    $router->get('/busca', [LojaBuscaController::class, 'index'])->name('loja.busca');
    $router->get('/busca/autocomplete', [LojaBuscaController::class, 'autocomplete'])->name('loja.busca.autocomplete');
    
    // Páginas estáticas
    $router->get('/sobre', [LojaSobreController::class, 'index'])->name('loja.sobre');
    $router->get('/contato', [LojaContatoController::class, 'index'])->name('loja.contato');
    $router->post('/contato', [LojaContatoController::class, 'enviar'])->name('loja.contato.enviar');
    
    // Newsletter
    $router->post('/newsletter', [LojaNewsletterController::class, 'cadastrar'])->name('loja.newsletter');
    
    // Páginas legais
    $router->get('/termos', [LojaSobreController::class, 'termos'])->name('loja.termos');
    $router->get('/privacidade', [LojaSobreController::class, 'privacidade'])->name('loja.privacidade');
    $router->get('/cookies', [LojaSobreController::class, 'cookies'])->name('loja.cookies');
    $router->get('/trocas', [LojaSobreController::class, 'trocas'])->name('loja.trocas');
    $router->get('/frete', [LojaSobreController::class, 'frete'])->name('loja.frete');
    $router->get('/pagamento', [LojaSobreController::class, 'pagamento'])->name('loja.pagamento');
    $router->get('/faq', [LojaSobreController::class, 'faq'])->name('loja.faq');
    
    // Depoimentos
    $router->get('/depoimentos', [LojaSobreController::class, 'depoimentos'])->name('loja.depoimentos');
    $router->post('/depoimentos', [LojaSobreController::class, 'adicionarDepoimento'])->name('loja.depoimentos.adicionar');
    
    // Marcas
    $router->get('/marcas', [LojaProdutoController::class, 'marcas'])->name('loja.marcas');
    $router->get('/marca/{id}', [LojaProdutoController::class, 'produtosPorMarca'])->name('loja.marca');
});

// ===== ROTAS DE AUTENTICAÇÃO =====
$router->group(['prefix' => '/loja'], function ($router) {
    // Login
    $router->get('/login', [SiteAuthController::class, 'loginLoja'])->name('loja.login');
    $router->post('/login', [SiteAuthController::class, 'loginLoja']);
    
    // Logout
    $router->get('/logout', [SiteAuthController::class, 'logout'])->name('loja.logout');
    
    // Cadastro
    $router->get('/cadastro', [SiteAuthController::class, 'cadastroLoja'])->name('loja.cadastro');
    $router->post('/cadastro', [SiteAuthController::class, 'cadastroLoja']);
    
    // Recuperação de senha
    $router->get('/esqueci-senha', [SiteAuthController::class, 'esqueciSenha'])->name('loja.esqueci-senha');
    $router->post('/esqueci-senha', [SiteAuthController::class, 'esqueciSenha']);
    $router->get('/reset-senha/{token}', [SiteAuthController::class, 'resetSenha'])->name('loja.reset-senha');
    $router->post('/reset-senha/{token}', [SiteAuthController::class, 'resetSenha']);
    
    // Verificação de e-mail
    $router->get('/verificar-email/{token}', [SiteAuthController::class, 'verificarEmail'])->name('loja.verificar-email');
    $router->post('/reenviar-verificacao', [SiteAuthController::class, 'reenviarVerificacao'])->name('loja.reenviar-verificacao');
});

// ===== ROTAS PROTEGIDAS =====
$router->group(['prefix' => '/loja', 'middleware' => 'auth:loja'], function ($router) {
    // Perfil do usuário
    $router->get('/perfil', [LojaUsuarioController::class, 'perfil'])->name('loja.perfil');
    $router->post('/perfil', [BackendUsuarioController::class, 'update'])->name('loja.perfil.update');
    $router->post('/perfil/senha', [BackendUsuarioController::class, 'updateSenha'])->name('loja.perfil.senha');
    $router->post('/perfil/avatar', [BackendUsuarioController::class, 'updateAvatar'])->name('loja.perfil.avatar');
    
    // Pedidos
    $router->get('/pedidos', [LojaUsuarioController::class, 'pedidos'])->name('loja.pedidos');
    $router->get('/pedido/{id}', [LojaUsuarioController::class, 'pedido'])->name('loja.pedido');
    $router->post('/pedido/{id}/cancelar', [LojaUsuarioController::class, 'cancelarPedido'])->name('loja.pedido.cancelar');
    $router->post('/pedido/{id}/avaliar', [LojaUsuarioController::class, 'avaliarPedido'])->name('loja.pedido.avaliar');
    
    // Favoritos
    $router->get('/favoritos', [LojaFavoritoController::class, 'index'])->name('loja.favoritos');
    $router->post('/favoritos/toggle', [LojaFavoritoController::class, 'toggle'])->name('loja.favoritos.toggle');
    $router->delete('/favoritos/{id}', [LojaFavoritoController::class, 'remove'])->name('loja.favoritos.remove');
    
    // Endereços
    $router->get('/enderecos', [LojaUsuarioController::class, 'enderecos'])->name('loja.enderecos');
    $router->post('/enderecos', [LojaUsuarioController::class, 'adicionarEndereco'])->name('loja.enderecos.adicionar');
    $router->put('/enderecos/{id}', [LojaUsuarioController::class, 'editarEndereco'])->name('loja.enderecos.editar');
    $router->delete('/enderecos/{id}', [LojaUsuarioController::class, 'removerEndereco'])->name('loja.enderecos.remover');
    
    // Cartões
    $router->get('/cartoes', [LojaUsuarioController::class, 'cartoes'])->name('loja.cartoes');
    $router->post('/cartoes', [LojaUsuarioController::class, 'adicionarCartao'])->name('loja.cartoes.adicionar');
    $router->delete('/cartoes/{id}', [LojaUsuarioController::class, 'removerCartao'])->name('loja.cartoes.remover');
    
    // Notificações
    $router->get('/notificacoes', [LojaUsuarioController::class, 'notificacoes'])->name('loja.notificacoes');
    $router->post('/notificacoes/{id}/ler', [LojaUsuarioController::class, 'marcarNotificacaoLida'])->name('loja.notificacoes.ler');
    $router->post('/notificacoes/ler-todas', [LojaUsuarioController::class, 'marcarTodasNotificacoesLidas'])->name('loja.notificacoes.ler-todas');
});

// ===== ROTAS DO CARRINHO =====
$router->group(['prefix' => '/carrinho'], function ($router) {
    // Visualizar carrinho
    $router->get('/', [LojaCarrinhoController::class, 'index'])->name('loja.carrinho');
    
    // Ações do carrinho
    $router->post('/adicionar', [BackendCarrinhoController::class, 'adicionar'])->name('loja.carrinho.adicionar');
    $router->post('/atualizar', [BackendCarrinhoController::class, 'atualizar'])->name('loja.carrinho.atualizar');
    $router->post('/remover', [BackendCarrinhoController::class, 'remover'])->name('loja.carrinho.remover');
    $router->post('/limpar', [BackendCarrinhoController::class, 'limpar'])->name('loja.carrinho.limpar');
    
    // Aplicar cupom
    $router->post('/cupom', [BackendCarrinhoController::class, 'aplicarCupom'])->name('loja.carrinho.cupom');
    $router->delete('/cupom', [BackendCarrinhoController::class, 'removerCupom'])->name('loja.carrinho.cupom.remover');
    
    // Calcular frete
    $router->post('/calcular-frete', [BackendCarrinhoController::class, 'calcularFrete'])->name('loja.carrinho.frete');
});

// ===== ROTAS DO CHECKOUT =====
$router->group(['prefix' => '/checkout'], function ($router) {
    // Página do checkout
    $router->get('/', [BackendCheckoutController::class, 'index'])->name('loja.checkout');
    
    // Processar pedido
    $router->post('/processar', [BackendCheckoutController::class, 'processar'])->name('loja.checkout.processar');
    
    // Páginas de resultado
    $router->get('/sucesso/{id}', [BackendCheckoutController::class, 'sucesso'])->name('loja.checkout.sucesso');
    $router->get('/cancelado', [BackendCheckoutController::class, 'cancelado'])->name('loja.checkout.cancelado');
    $router->get('/erro', [BackendCheckoutController::class, 'erro'])->name('loja.checkout.erro');
    
    // Webhooks de pagamento
    $router->post('/webhook/pagseguro', [BackendCheckoutController::class, 'webhookPagSeguro'])->name('loja.checkout.webhook.pagseguro');
    $router->post('/webhook/mercadopago', [BackendCheckoutController::class, 'webhookMercadoPago'])->name('loja.checkout.webhook.mercadopago');
    $router->post('/webhook/paypal', [BackendCheckoutController::class, 'webhookPayPal'])->name('loja.checkout.webhook.paypal');
});

// ===== ROTAS DE API =====
$router->group(['prefix' => '/api/loja'], function ($router) {
    // Produtos
    $router->get('/produtos', [LojaProdutoController::class, 'apiIndex'])->name('api.loja.produtos');
    $router->get('/produtos/{id}', [LojaProdutoController::class, 'apiShow'])->name('api.loja.produtos.show');
    
    // Categorias
    $router->get('/categorias', [LojaCategoriaController::class, 'apiIndex'])->name('api.loja.categorias');
    
    // Busca
    $router->get('/busca', [LojaBuscaController::class, 'apiBusca'])->name('api.loja.busca');
    
    // CEP
    $router->get('/cep/{cep}', [LojaUsuarioController::class, 'consultarCep'])->name('api.loja.cep');
    
    // Validações
    $router->post('/validar/cpf', [LojaUsuarioController::class, 'validarCpf'])->name('api.loja.validar.cpf');
    $router->post('/validar/cnpj', [LojaUsuarioController::class, 'validarCnpj'])->name('api.loja.validar.cnpj');
    $router->post('/validar/email', [LojaUsuarioController::class, 'validarEmail'])->name('api.loja.validar.email');
});

// ===== ROTAS DE ADMINISTRAÇÃO DA LOJA =====
$router->group(['prefix' => '/admin/loja', 'middleware' => 'auth:admin'], function ($router) {
    // Dashboard
    $router->get('/', [LojaHomeController::class, 'admin'])->name('admin.loja.dashboard');
    
    // Produtos
    $router->get('/produtos', [LojaProdutoController::class, 'adminIndex'])->name('admin.loja.produtos');
    $router->get('/produtos/criar', [LojaProdutoController::class, 'adminCreate'])->name('admin.loja.produtos.criar');
    $router->post('/produtos', [LojaProdutoController::class, 'adminStore'])->name('admin.loja.produtos.store');
    $router->get('/produtos/{id}/editar', [LojaProdutoController::class, 'adminEdit'])->name('admin.loja.produtos.editar');
    $router->put('/produtos/{id}', [LojaProdutoController::class, 'adminUpdate'])->name('admin.loja.produtos.update');
    $router->delete('/produtos/{id}', [LojaProdutoController::class, 'adminDestroy'])->name('admin.loja.produtos.destroy');
    
    // Pedidos
    $router->get('/pedidos', [LojaUsuarioController::class, 'adminPedidos'])->name('admin.loja.pedidos');
    $router->get('/pedidos/{id}', [LojaUsuarioController::class, 'adminPedido'])->name('admin.loja.pedidos.show');
    $router->put('/pedidos/{id}/status', [LojaUsuarioController::class, 'adminUpdateStatusPedido'])->name('admin.loja.pedidos.status');
    
    // Relatórios
    $router->get('/relatorios/vendas', [LojaHomeController::class, 'relatorioVendas'])->name('admin.loja.relatorios.vendas');
    $router->get('/relatorios/produtos', [LojaHomeController::class, 'relatorioProdutos'])->name('admin.loja.relatorios.produtos');
    $router->get('/relatorios/clientes', [LojaHomeController::class, 'relatorioClientes'])->name('admin.loja.relatorios.clientes');
});