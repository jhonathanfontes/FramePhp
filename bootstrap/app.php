<?php

$request = new \Core\Http\Request();
$router = \Core\Router\Router::getInstance(); // Usar getInstance para popular Router::$instance

// Load routes
require_once BASE_PATH . '/routes/router.php';

// Dispatch route
$router->dispatch();

// Registrar manipulador de erros
\Core\Error\ErrorHandler::register();

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inicializar o Translator
\Core\Translation\Translator::getInstance();