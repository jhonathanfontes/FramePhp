<?php

// bootstrap/app.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inicia o container de DI
\Core\Container\Container::getInstance();

// Inicia o Error Handler e o Translator
\Core\Error\ErrorHandler::register();
\Core\Translation\Translator::getInstance();

// Carrega as definições de rota
require_once BASE_PATH . '/routes/router.php';
require_once BASE_PATH . '/routes/loja.php';

// Obtém a instância do roteador e dispara a rota
$router = \Core\Router\Router::getInstance();
$router->dispatch();