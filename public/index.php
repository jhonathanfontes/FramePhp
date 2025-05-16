<?php

// Definir caminho base
define('BASE_PATH', dirname(__DIR__));

// Carregar o autoloader do Composer
require BASE_PATH . '/vendor/autoload.php';

// Carregar variáveis de ambiente
$dotenv = \Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

// Carregar bootstrap
require_once BASE_PATH . '/bootstrap/app.php';

// Iniciar a aplicação
$router = new \Core\Router\Router();
require_once BASE_PATH . '/routes/web.php'; // Just include the file to set up routes

$router->dispatch();