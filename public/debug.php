<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Core\Config\Constants;
Constants::init();

echo "<h1>Depuração do FramePhp</h1>";
echo "<p>URL_BASE: " . URL_BASE . "</p>";
echo "<p>BASE_PATH: " . BASE_PATH . "</p>";
echo "<p>APP_NAME: " . APP_NAME . "</p>";
echo "<p>APP_VERSION: " . APP_VERSION . "</p>";

// Testar o Twig
try {
    $loader = new \Twig\Loader\FilesystemLoader(BASE_PATH . '/app/Views');
    $twig = new \Twig\Environment($loader, [
        'cache' => BASE_PATH . '/storage/cache/twig',
        'debug' => true,
        'auto_reload' => true
    ]);
    
    echo "<p style='color:green'>Twig carregado com sucesso!</p>";
    
    // Listar templates disponíveis
    echo "<h2>Templates disponíveis:</h2>";
    echo "<ul>";
    $templates = scandir(BASE_PATH . '/app/Views');
    foreach ($templates as $template) {
        if ($template != '.' && $template != '..') {
            echo "<li>$template</li>";
        }
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>Erro ao carregar o Twig: " . $e->getMessage() . "</p>";
}