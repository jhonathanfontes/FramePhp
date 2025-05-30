<?php

return [
    'name' => env('APP_NAME', 'FramePhp'),
    'version' => env('APP_VERSION', '1.0.0'),
    'debug' => env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => env('APP_TIMEZONE', 'America/Sao_Paulo'),
    'locale' => env('APP_LOCALE', 'pt_BR'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'supported_locales' => ['pt_BR', 'en'],
    'key' => env('APP_KEY', ''),
    'cipher' => 'AES-256-CBC',
    'base_path' => '/FramePhp/public'
];