<?php

// Initialize Router
$router = new \Core\Router\Router();

// Load routes
require_once BASE_PATH . '/routes/web.php';

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