<?php

use Core\Database\Migration;

class CreateConfiguracoesTable extends Migration
{
    public function up(): void
    {
        $this->createTable('configuracoes', [
            'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
            'chave' => 'VARCHAR(255) UNIQUE NOT NULL',
            'valor' => 'TEXT',
            'tipo' => 'VARCHAR(50) DEFAULT "string"',
            'descricao' => 'TEXT',
            'categoria' => 'VARCHAR(100) DEFAULT "geral"',
            'editavel' => 'BOOLEAN DEFAULT TRUE',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'INDEX idx_chave (chave)',
            'INDEX idx_categoria (categoria)',
            'INDEX idx_editavel (editavel)'
        ]);
    }

    public function down(): void
    {
        $this->dropTable('configuracoes');
    }
} 