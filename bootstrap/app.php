<?php

// Registrar manipulador de erros
\Core\Error\ErrorHandler::register();

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inicializar o Translator
\Core\Translation\Translator::getInstance();