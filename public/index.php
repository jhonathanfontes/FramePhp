<?php

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Load Composer's autoloader
require BASE_PATH . '/vendor/autoload.php';

// Load environment variables
try {
    if (!file_exists(BASE_PATH . '/.env')) {
        if (file_exists(BASE_PATH . '/.env.example')) {
            copy(BASE_PATH . '/.env.example', BASE_PATH . '/.env');
            header('Content-Type: text/html; charset=utf-8');
            echo "Arquivo .env foi criado automaticamente. Por favor, configure as variáveis de ambiente e recarregue a página.";
            exit;
        } else {
            throw new Exception("Arquivo .env.example não encontrado. Por favor, crie o arquivo .env manualmente.");
        }
    }
    
    $dotenv = \Dotenv\Dotenv::createImmutable(BASE_PATH);
    $dotenv->load();

} catch (Exception $e) {
    header('Content-Type: text/html; charset=utf-8');
    die("Erro ao carregar variáveis de ambiente: " . $e->getMessage());
}

use Core\Config\Constants;
Constants::init();

// Configurar tratamento de erros
error_reporting(E_ALL);
ini_set('display_errors', env('APP_DEBUG', false) ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/storage/logs/php_errors.log');

// Registrar middlewares no container
$container = \Core\Container\Container::getInstance();

// Load bootstrap
require_once BASE_PATH . '/bootstrap/app.php';p';

