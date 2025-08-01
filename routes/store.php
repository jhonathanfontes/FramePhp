
<?php

use App\Controllers\Store\StoreController;
use App\Middleware\EmpresaMiddleware;
use App\Middleware\LocaleMiddleware;
use App\Middleware\CorsMiddleware;

/*
|--------------------------------------------------------------------------
| Rotas das Lojas Multi-empresas
|--------------------------------------------------------------------------
|
| Rotas para acesso às lojas individuais de cada empresa
|
*/

// Rotas públicas das lojas com middlewares básicos
$router->middleware([LocaleMiddleware::class, EmpresaMiddleware::class])
    ->group(['prefix' => 'loja'], function ($router) {
        
        // Página inicial da loja
        $router->get('/{slug}', [StoreController::class, 'index'])->name('store.home');
        
        // Produtos da loja
        $router->get('/{slug}/produtos', [StoreController::class, 'produtos'])->name('store.produtos');
        $router->get('/{slug}/produto/{id}', [StoreController::class, 'produto'])->name('store.produto');
        
        // Categorias
        $router->get('/{slug}/categoria/{categoria}', [StoreController::class, 'categoria'])->name('store.categoria');
        
        // Busca
        $router->get('/{slug}/buscar', [StoreController::class, 'buscar'])->name('store.buscar');
        
        // Carrinho
        $router->get('/{slug}/carrinho', [StoreController::class, 'carrinho'])->name('store.carrinho');
        $router->post('/{slug}/carrinho/adicionar', [StoreController::class, 'adicionarCarrinho'])->name('store.carrinho.adicionar');
        $router->put('/{slug}/carrinho/atualizar', [StoreController::class, 'atualizarCarrinho'])->name('store.carrinho.atualizar');
        $router->delete('/{slug}/carrinho/remover/{item}', [StoreController::class, 'removerCarrinho'])->name('store.carrinho.remover');
        
        // Checkout
        $router->get('/{slug}/checkout', [StoreController::class, 'checkout'])->name('store.checkout');
        $router->post('/{slug}/checkout/processar', [StoreController::class, 'processarCheckout'])->name('store.checkout.processar');
        
        // Conta do cliente (requer autenticação)
        $router->middleware(['auth'])
            ->group(['prefix' => '{slug}/conta'], function ($router) {
                $router->get('/', [StoreController::class, 'conta'])->name('store.conta');
                $router->get('/pedidos', [StoreController::class, 'pedidos'])->name('store.pedidos');
                $router->get('/pedido/{id}', [StoreController::class, 'pedido'])->name('store.pedido');
            });
    });

// API da loja com rate limiting e CORS
$router->middleware([CorsMiddleware::class, 'api.rate', EmpresaMiddleware::class])
    ->group(['prefix' => 'api/loja'], function ($router) {
        
        // Produtos via API
        $router->get('/{slug}/produtos', [StoreController::class, 'apiProdutos'])->name('api.store.produtos');
        $router->get('/{slug}/produto/{id}', [StoreController::class, 'apiProduto'])->name('api.store.produto');
        
        // Carrinho via API
        $router->post('/{slug}/carrinho', [StoreController::class, 'apiAdicionarCarrinho'])->name('api.store.carrinho.adicionar');
        $router->get('/{slug}/carrinho', [StoreController::class, 'apiCarrinho'])->name('api.store.carrinho');
        
        // Busca via API
        $router->get('/{slug}/buscar', [StoreController::class, 'apiBuscar'])->name('api.store.buscar');
    });
