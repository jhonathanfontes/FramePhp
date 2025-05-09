<?php
define('BASE_PATH', dirname(__DIR__));

// Autoloader
require_once BASE_PATH . '/vendor/autoload.php';

try {
    // Carregar variáveis de ambiente
    Core\Config\Environment::load(BASE_PATH . '/.env');

    // Inicializar o Router
    $router = new Core\Router\Router();

    // Carregar as rotas do arquivo
    $routes = require BASE_PATH . '/routes/web.php';
    $routes($router);

    // Despachar a rota
    $router->dispatch();
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}

// Inicializar o Framework
try {
    // Aqui vamos adicionar a inicialização do Framework
    echo "Framework PHP iniciado com sucesso!";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}