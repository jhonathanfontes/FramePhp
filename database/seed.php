<?php

// Definir caminho base
define('BASE_PATH', dirname(__DIR__));

// Carregar o autoloader do Composer
require BASE_PATH . '/vendor/autoload.php';

// Carregar variáveis de ambiente
$dotenv = \Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

// Carregar bootstrap
require_once BASE_PATH . '/bootstrap/app.php';

// Diretório dos seeders
$seedsDir = BASE_PATH . '/database/seeds';

// Verificar se o diretório existe
if (!is_dir($seedsDir)) {
    die("Diretório de seeders não encontrado: {$seedsDir}\n");
}

// Obter todos os arquivos de seeder
$seedFiles = glob($seedsDir . '/*.php');

// Ordenar os arquivos por nome para garantir a ordem correta de execução
sort($seedFiles);

// Executar cada seeder
foreach ($seedFiles as $file) {
    echo "Executando seeder: " . basename($file) . "\n";
    
    try {
        require $file;
        echo "Seeder concluído com sucesso: " . basename($file) . "\n";
    } catch (\Exception $e) {
        echo "Erro ao executar seeder " . basename($file) . ": " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "\nTodos os seeders foram executados com sucesso!\n"; 