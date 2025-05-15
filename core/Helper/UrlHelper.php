<?php

namespace Core\Helper;

class UrlHelper
{
    public static function base_url(?string $path = null): string
    {
        $base = URL_BASE;
        
        if ($path) {
            return $base . '/' . trim($path, '/');
        }
        
        return $base;
    }

    public static function app_name(): string
    {
        return APP_NAME;
    }

    public static function app_version(): string
    {
        return APP_VERSION;
    }

    public static function app_debug(): bool
    {
        return APP_DEBUG;
    }   
}