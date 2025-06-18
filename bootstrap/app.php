<?php

// Iniciar sessão antes de qualquer saída
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$request = new \Core\Http\Request();
$router = \Core\Router\Router::getInstance();

// Load routes
require_once BASE_PATH . '/routes/router.php';

// Registrar manipulador de erros
\Core\Error\ErrorHandler::register();

// Inicializar o Translator
\Core\Translation\Translator::getInstance();

// Dispatch route
$router->dispatch();