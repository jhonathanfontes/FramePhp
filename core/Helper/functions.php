<?php
use Core\Helper\UrlHelper;

if (!function_exists('base_url')) {
    function base_url(?string $path = null): string
    {
        $base = URL_BASE;
        
        if ($path) {
            return $base . '/' . trim($path, '/');
        }
        
        return $base;
    }
}

if (!function_exists('app_name')) {
    function app_name(): string
    {
        return APP_NAME ?? 'FramePhp';
    }
}

if (!function_exists('app_version')) {
    function app_version(): string
    {
        return APP_VERSION ?? '1.0.0';
    }
}

if (!function_exists('app_debug')) {
    function app_debug(): bool
    {
        return UrlHelper::app_debug();
    }
}
