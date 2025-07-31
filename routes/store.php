
<?php

use App\Controllers\Store\StoreController;

/*
|--------------------------------------------------------------------------
| Rotas da Loja Online
|--------------------------------------------------------------------------
|
| Rotas para o frontend da loja online, acessíveis publicamente
|
*/

$router->group([], function ($router) {
    // Página inicial da loja
    $router->get('/', [StoreController::class, 'index'])->name('store.home');
    
    // Produtos
    $router->get('/produto/{id}', [StoreController::class, 'produto'])->name('store.product');
    $router->get('/categoria/{slug}', [StoreController::class, 'categoria'])->name('store.category');
    $router->get('/produtos', [StoreController::class, 'produtos'])->name('store.products');
    $router->get('/buscar', [StoreController::class, 'buscar'])->name('store.search');
    
    // Carrinho e checkout
    $router->get('/carrinho', [StoreController::class, 'carrinho'])->name('store.cart');
    $router->get('/checkout', [StoreController::class, 'checkout'])->name('store.checkout');
    $router->post('/checkout/processar', [StoreController::class, 'processarCheckout'])->name('store.checkout.process');
    
    // Páginas institucionais
    $router->get('/sobre', [StoreController::class, 'sobre'])->name('store.about');
    $router->get('/contato', [StoreController::class, 'contato'])->name('store.contact');
    $router->get('/termos', [StoreController::class, 'termos'])->name('store.terms');
    $router->get('/privacidade', [StoreController::class, 'privacidade'])->name('store.privacy');
});
