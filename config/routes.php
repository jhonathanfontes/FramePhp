<?php

return [
    // Configurações gerais
    'default_guard' => 'loja',
    
    // Prefixos por módulo
    'prefixes' => [
        'loja' => '',
        'admin' => 'admin',
        'painel' => 'painel'
    ],
    
    // Middlewares padrão
    'middlewares' => [
        'auth:loja' => \App\Middleware\AuthMiddleware::class,
        'auth:admin' => \App\Middleware\AuthMiddleware::class,
        'auth:painel' => \App\Middleware\AuthMiddleware::class,
        'guest' => \App\Middleware\GuestMiddleware::class,
        'throttle' => \App\Middleware\ThrottleMiddleware::class,
    ],
    
    // Configurações de autenticação
    'auth' => [
        'guards' => [
            'loja' => [
                'driver' => 'session',
                'provider' => 'usuarios',
                'timeout' => 120, // 2 horas
            ],
            'admin' => [
                'driver' => 'session',
                'provider' => 'usuarios',
                'timeout' => 60, // 1 hora
            ],
            'painel' => [
                'driver' => 'session',
                'provider' => 'usuarios',
                'timeout' => 60, // 1 hora
            ],
        ],
        'providers' => [
            'usuarios' => [
                'driver' => 'eloquent',
                'model' => \App\Models\Usuario::class,
            ],
        ],
    ],
    
    // Configurações de sessão
    'session' => [
        'driver' => 'file',
        'lifetime' => 120,
        'expire_on_close' => false,
        'encrypt' => false,
        'files' => storage_path('framework/sessions'),
        'connection' => null,
        'table' => 'sessions',
        'store' => null,
        'lottery' => [2, 100],
        'cookie' => 'frame_session',
        'path' => '/',
        'domain' => null,
        'secure' => false,
        'http_only' => true,
        'same_site' => 'lax',
    ],
    
    // Configurações de cache
    'cache' => [
        'default' => 'file',
        'stores' => [
            'file' => [
                'driver' => 'file',
                'path' => storage_path('framework/cache/data'),
            ],
        ],
    ],
    
    // Configurações de upload
    'upload' => [
        'disk' => 'public',
        'max_size' => 10240, // 10MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'],
        'paths' => [
            'empresas' => 'uploads/empresas',
            'produtos' => 'uploads/produtos',
            'categorias' => 'uploads/categorias',
            'usuarios' => 'uploads/usuarios',
        ],
    ],
    
    // Configurações de paginação
    'pagination' => [
        'per_page' => 20,
        'per_page_options' => [10, 20, 50, 100],
    ],
    
    // Configurações de relatórios
    'reports' => [
        'date_format' => 'd/m/Y',
        'time_format' => 'H:i:s',
        'currency' => 'R$',
        'decimal_places' => 2,
    ],
]; 