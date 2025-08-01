
<?php

use App\Controllers\Api\ApiController;
use App\Controllers\Api\AuthController;
use App\Middleware\JWTAuthMiddleware;
use App\Middleware\ApiRateLimitMiddleware;
use App\Middleware\CorsMiddleware;

/*
|--------------------------------------------------------------------------
| Rotas da API
|--------------------------------------------------------------------------
|
| Todas as rotas da API com controle de rate limiting e CORS
|
*/

// Middlewares globais para API
$router->middleware([CorsMiddleware::class, ApiRateLimitMiddleware::class])
    ->group(['prefix' => 'api/v1'], function ($router) {
        
        // Rotas públicas da API
        $router->group([], function ($router) {
            
            // Autenticação
            $router->post('/auth/login', [AuthController::class, 'login'])->name('api.auth.login');
            $router->post('/auth/register', [AuthController::class, 'register'])->name('api.auth.register');
            $router->post('/auth/forgot-password', [AuthController::class, 'forgotPassword'])->name('api.auth.forgot-password');
            $router->post('/auth/reset-password', [AuthController::class, 'resetPassword'])->name('api.auth.reset-password');
            
            // Informações públicas
            $router->get('/status', [ApiController::class, 'status'])->name('api.status');
            $router->get('/health', [ApiController::class, 'health'])->name('api.health');
        });
        
        // Rotas protegidas da API (requer JWT)
        $router->middleware([JWTAuthMiddleware::class])
            ->group([], function ($router) {
                
                // Perfil do usuário
                $router->get('/profile', [ApiController::class, 'profile'])->name('api.profile');
                $router->put('/profile', [ApiController::class, 'updateProfile'])->name('api.profile.update');
                
                // Logout
                $router->post('/auth/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
                $router->post('/auth/refresh', [AuthController::class, 'refresh'])->name('api.auth.refresh');
                
                // Empresas (com controle de permissão)
                $router->middleware(['permission:admin,empresa_admin'])
                    ->group(['prefix' => 'empresas'], function ($router) {
                        $router->get('/', [ApiController::class, 'empresas'])->name('api.empresas');
                        $router->get('/{id}', [ApiController::class, 'empresa'])->name('api.empresa');
                    });
                
                // Produtos
                $router->group(['prefix' => 'produtos'], function ($router) {
                    $router->get('/', [ApiController::class, 'produtos'])->name('api.produtos');
                    $router->get('/{id}', [ApiController::class, 'produto'])->name('api.produto');
                    
                    // Operações que requerem permissão
                    $router->middleware(['permission:admin,empresa_admin'])
                        ->group([], function ($router) {
                            $router->post('/', [ApiController::class, 'createProduto'])->name('api.produto.create');
                            $router->put('/{id}', [ApiController::class, 'updateProduto'])->name('api.produto.update');
                            $router->delete('/{id}', [ApiController::class, 'deleteProduto'])->name('api.produto.delete');
                        });
                });
                
                // Pedidos
                $router->group(['prefix' => 'pedidos'], function ($router) {
                    $router->get('/', [ApiController::class, 'pedidos'])->name('api.pedidos');
                    $router->get('/{id}', [ApiController::class, 'pedido'])->name('api.pedido');
                    $router->post('/', [ApiController::class, 'createPedido'])->name('api.pedido.create');
                    $router->put('/{id}/status', [ApiController::class, 'updatePedidoStatus'])->name('api.pedido.status');
                });
            });
    });

// Webhook endpoints (sem rate limiting para webhooks legítimos)
$router->middleware([CorsMiddleware::class])
    ->group(['prefix' => 'webhooks'], function ($router) {
        $router->post('/payment', [ApiController::class, 'paymentWebhook'])->name('api.webhook.payment');
        $router->post('/notification', [ApiController::class, 'notificationWebhook'])->name('api.webhook.notification');
    });
