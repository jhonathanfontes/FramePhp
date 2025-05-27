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
            echo "Arquivo .env foi criado automaticamente a partir do .env.example. Por favor, configure as variáveis de ambiente apropriadamente.\n";
        } else {
            throw new Exception("Arquivo .env.example não encontrado. Por favor, crie o arquivo .env manualmente.");
        }
    }
    
    $dotenv = \Dotenv\Dotenv::createImmutable(BASE_PATH);
    $dotenv->load();

} catch (Exception $e) {
    die("Erro ao carregar variáveis de ambiente: " . $e->getMessage());
}

use Core\Config\Constants;
Constants::init();

// Registrar middlewares no container
$container = \Core\Container\Container::getInstance();

// Load bootstrap
require_once BASE_PATH . '/bootstrap/app.php';