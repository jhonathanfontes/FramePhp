<?php

namespace Core\Config;

class Constants
{
    /**
     * Inicializa as constantes do sistema
     */
    public static function init()
    {
        // Carrega o arquivo .env se existir
        $envFile = dirname(dirname(dirname(__FILE__))) . '/.env';
        $env = [];
        
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos(trim($line), '#') !== 0) {
                    list($key, $value) = explode('=', $line, 2);
                    $env[trim($key)] = trim($value);
                }
            }
        }
        
        // Caminhos do sistema
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', dirname(dirname(dirname(__FILE__))));
        }
        
        // URLs do sistema
        if (!defined('URL_BASE')) {
            define('URL_BASE', $env['URL_BASE'] ?? '/FramePhp/public');
        }
        
        // Outras constantes do sistema
        if (!defined('APP_NAME')) {
            define('APP_NAME', $env['APP_NAME'] ?? 'Frame Php');
        }
        
        if (!defined('APP_VERSION')) {
            define('APP_VERSION', $env['APP_VERSION'] ?? '1.0.0');
        }
        
        // Configurações de ambiente
        if (!defined('APP_ENV')) {
            define('APP_ENV', $env['APP_ENV'] ?? 'development');
        }
        
        if (!defined('APP_DEBUG')) {
            define('APP_DEBUG', $env['APP_DEBUG'] ?? true);
        }

        if (!defined('BASE_VIEW')) {
            define('BASE_VIEW', BASE_PATH . '/app/views');
        }
    }
}