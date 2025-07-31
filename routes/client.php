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
<?php

use App\Controllers\Site\ClientController;
use App\Middleware\LocaleMiddleware;
use App\Middleware\CsrfMiddleware;

/*
|--------------------------------------------------------------------------
| Rotas para Clientes
|--------------------------------------------------------------------------
|
| Rotas públicas para clientes navegarem pelos produtos e realizarem compras
|
*/

// Rotas públicas para clientes
$router->middleware([LocaleMiddleware::class, CsrfMiddleware::class])
    ->group(['prefix' => 'client'], function ($router) {
        
        // Página inicial da loja
        $router->get('/', [ClientController::class, 'index'])->name('client.home');
        
        // Páginas institucionais
        $router->get('/sobre', [ClientController::class, 'sobre'])->name('client.sobre');
        $router->get('/contato', [ClientController::class, 'contato'])->name('client.contato');
        $router->post('/contato/enviar', [ClientController::class, 'enviarContato'])->name('client.contato.enviar');
        $router->get('/loja', [ClientController::class, 'loja'])->name('client.loja');
        
        // Catálogo de produtos
        $router->get('/catalogo', [ClientController::class, 'catalogo'])->name('client.catalogo');
        $router->get('/produto/{id}', [ClientController::class, 'produto'])->name('client.produto');
        
        // Carrinho de compras
        $router->get('/carrinho', [ClientController::class, 'carrinho'])->name('client.carrinho');
        $router->post('/carrinho/adicionar', [ClientController::class, 'adicionarCarrinho'])->name('client.carrinho.adicionar');
        $router->post('/carrinho/atualizar', [ClientController::class, 'atualizarCarrinho'])->name('client.carrinho.atualizar');
        $router->get('/carrinho/remover/{id}', [ClientController::class, 'removerCarrinho'])->name('client.carrinho.remover');
        
        // Checkout
        $router->get('/checkout', [ClientController::class, 'checkout'])->name('client.checkout');
        $router->post('/checkout/processar', [ClientController::class, 'processarCheckout'])->name('client.checkout.processar');
    });

// Rotas que requerem autenticação
$router->middleware(['auth', LocaleMiddleware::class])
    ->group(['prefix' => 'client'], function ($router) {
        
        // Área da conta do cliente
        $router->get('/conta', [ClientController::class, 'conta'])->name('client.conta');
        $router->get('/conta/pedidos', [ClientController::class, 'pedidos'])->name('client.pedidos');
        $router->get('/conta/pedido/{id}', [ClientController::class, 'pedido'])->name('client.pedido');
    });
