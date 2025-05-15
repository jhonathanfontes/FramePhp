<?php

// Autoload do Composer
require dirname(__DIR__) . '/vendor/autoload.php';

// Inicializa as constantes
use Core\Config\Constants;
Constants::init();

// Carrega as variáveis de ambiente
use Core\Config\Environment;
Environment::load(BASE_PATH . '/.env');

// Registra o manipulador de erros
use Core\Error\ErrorHandler;
ErrorHandler::register();

// Configurações de erro
error_reporting(E_ALL);
ini_set('display_errors', Environment::get('APP_DEBUG', false));

// Middleware de Segurança
$security = new \Core\Middleware\SecurityMiddleware();
$security->handle();

// Inicializa o Router
$router = new \Core\Router\Router();

// Carrega as rotas
$routeCallback = require BASE_PATH . '/routes/web.php';

// Executa o callback das rotas
if (is_callable($routeCallback)) {
    $routeCallback($router);
    $router->dispatch();
}