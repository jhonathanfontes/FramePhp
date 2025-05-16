<?php

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inicializar o Translator
\Core\Translation\Translator::getInstance();