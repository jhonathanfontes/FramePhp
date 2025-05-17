<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Database\Database;

// Carregar variáveis de ambiente
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Definir constante BASE_PATH
define('BASE_PATH', __DIR__ . '/..');

// Conectar ao banco de dados
$db = Database::getInstance();

// Verificar se o usuário admin existe
$admin = $db->find('users', 'email = ?', ['admin@admin.com']);

if ($admin) {
    echo "Usuário admin encontrado:\n";
    echo "ID: " . $admin['id'] . "\n";
    echo "Nome: " . $admin['name'] . "\n";
    echo "Email: " . $admin['email'] . "\n";
    echo "Role: " . $admin['role'] . "\n";
    
    // Verificar se a senha está correta
    $testPassword = 'password';
    if (password_verify($testPassword, $admin['password'])) {
        echo "\nSenha está correta!\n";
    } else {
        echo "\nSenha está incorreta! Vou atualizar...\n";
        
        // Atualizar a senha
        $db->update('users', 
            ['password' => password_hash($testPassword, PASSWORD_DEFAULT)],
            'id = ?',
            [$admin['id']]
        );
        
        echo "Senha atualizada com sucesso!\n";
    }
} else {
    echo "Usuário admin não encontrado! Vou criar...\n";
    
    // Criar usuário admin
    $db->insert('users', [
        'name' => 'Administrador',
        'email' => 'admin@admin.com',
        'password' => password_hash('password', PASSWORD_DEFAULT),
        'role' => 'admin'
    ]);
    
    echo "Usuário admin criado com sucesso!\n";
} 