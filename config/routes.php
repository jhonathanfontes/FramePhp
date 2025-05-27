<?php
return [
    'middleware' => [
        'auth' => [
            'web' => ['class' => \App\Middleware\AuthenticationMiddleware::class, 'guard' => 'web', 'redirect' => '/auth/login'],
            'admin' => ['class' => \App\Middleware\AuthenticationMiddleware::class, 'guard' => 'admin', 'redirect' => '/admin/login'],
            'client' => ['class' => \App\Middleware\AuthenticationMiddleware::class, 'guard' => 'client', 'redirect' => '/client/login'],
            'api' => ['class' => \App\Middleware\AuthenticationMiddleware::class, 'guard' => 'api', 'redirect' => null],
        ],
        'csrf' => \App\Middleware\CsrfMiddleware::class,
        'locale' => \App\Middleware\LocaleMiddleware::class,
        'guest' => \App\Middleware\GuestMiddleware::class,
        'permission' => [
            'admin' => \App\Middleware\PermissionMiddleware::class,
        ],
    ],
    'route_prefixes' => [
        'admin' => 'admin',
        'client' => 'client',
        'auth' => 'auth',
    ],
    'redirects' => [
        'login' => '/auth/login',
        'admin_login' => '/admin/login',
        'client_login' => '/client/login',
    ]
];
