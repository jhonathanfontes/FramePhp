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

// Diretório das migrações
$migrationsDir = BASE_PATH . '/database/migrations';

// Verificar se o diretório existe
if (!is_dir($migrationsDir)) {
    die("Diretório de migrações não encontrado: {$migrationsDir}\n");
}

// Obter todos os arquivos de migração
$migrationFiles = glob($migrationsDir . '/*.php');

// Ordenar os arquivos por nome para garantir a ordem correta de execução
sort($migrationFiles);

// Executar cada migração
foreach ($migrationFiles as $file) {
    echo "Executando migração: " . basename($file) . "\n";
    
    try {
        require $file;
        echo "Migração concluída com sucesso: " . basename($file) . "\n";
    } catch (\Exception $e) {
        echo "Erro ao executar migração " . basename($file) . ": " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "\nTodas as migrações foram executadas com sucesso!\n"; 