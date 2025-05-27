<?php

require_once __DIR__ . '/../bootstrap/app.php';

// Create a Request object
$request = new \Core\Http\Request();

// Obter todas as rotas registradas
$router = new \Core\Router\Router($request);

// Carregar as rotas
require_once BASE_PATH . '/routes/web.php';

// Obter as rotas compiladas
$routes = $router->getRoutes();

// Criar diretório de cache se não existir
$cacheDir = BASE_PATH . '/bootstrap/cache';
if (!is_dir($cacheDir)) {
    if (!mkdir($cacheDir, 0755, true)) {
        error_log("Falha ao criar diretório de cache: {$cacheDir}");
        exit(1);
    }
}

// Salvar as rotas em um arquivo de cache
$cacheFile = $cacheDir . '/routes.php';
if (file_put_contents($cacheFile, '<?php return ' . var_export($routes, true) . ';') === false) {
    error_log("Falha ao escrever no arquivo de cache: {$cacheFile}");
    exit(1);
}

error_log("Rotas em cache geradas com sucesso em: {$cacheFile}");