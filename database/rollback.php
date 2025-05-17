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

use Core\Database\Database;

// Conectar ao banco de dados
$db = Database::getInstance();

// Diretório das migrações
$migrationsDir = BASE_PATH . '/database/migrations';

// Verificar se o diretório existe
if (!is_dir($migrationsDir)) {
    die("Diretório de migrações não encontrado: {$migrationsDir}\n");
}

// Obter todos os arquivos de migração
$migrationFiles = glob($migrationsDir . '/*.php');

// Ordenar os arquivos por nome em ordem reversa para garantir a ordem correta de reversão
rsort($migrationFiles);

// Executar cada reversão de migração
foreach ($migrationFiles as $file) {
    $tableName = basename($file, '.php');
    $tableName = str_replace('create_', '', $tableName);
    $tableName = str_replace('_table', '', $tableName);
    
    echo "Revertendo migração: " . basename($file) . "\n";
    
    try {
        // Remover a tabela
        $db->query("DROP TABLE IF EXISTS {$tableName}");
        echo "Migração revertida com sucesso: " . basename($file) . "\n";
    } catch (\Exception $e) {
        echo "Erro ao reverter migração " . basename($file) . ": " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "\nTodas as migrações foram revertidas com sucesso!\n"; 