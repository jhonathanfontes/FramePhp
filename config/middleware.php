<?php

/**
 * --------------------------------------------------------------------------
 * Registro de Aliases de Middleware
 * --------------------------------------------------------------------------
 *
 * Mapeia aliases (strings curtas) para suas respectivas classes de middleware.
 * Isso desacopla a definição da rota da implementação do middleware,
 * facilitando a manutenção e a leitura do código.
 *
 */

use App\Middleware\AuthenticationMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\JWTAuthMiddleware;
use App\Middleware\LocaleMiddleware;
use App\Middleware\PermissionMiddleware;

return [
    'auth'       => AuthenticationMiddleware::class,
    'guest'      => GuestMiddleware::class,
    'csrf'       => CsrfMiddleware::class,
    'locale'     => LocaleMiddleware::class,
    'jwt'        => JWTAuthMiddleware::class,
    'permission' => PermissionMiddleware::class,
];