<?php

// Definir caminho base
define('BASE_PATH', dirname(__DIR__));

// Definir caminho base para as views
define('BASE_VIEW', BASE_PATH . '/app/Views');

// Carregar o autoloader do Composer
require BASE_PATH . '/vendor/autoload.php';

// Carregar variáveis de ambiente
$dotenv = \Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

// Carregar bootstrap
require_once BASE_PATH . '/bootstrap/app.php';

// Inicializar o Router
$router = new \Core\Router\Router();

// Carregar as rotas
require_once BASE_PATH . '/routes/web.php';

// Despachar a rota
$router->dispatch();