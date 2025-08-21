<?php

use Core\Database\Migration;

class CreateAtividadesTable extends Migration
{
    public function up(): void
    {
        $this->createTable('atividades', [
            'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
            'usuario_id' => 'INT NOT NULL',
            'empresa_id' => 'INT NOT NULL',
            'tipo' => 'VARCHAR(100) NOT NULL',
            'acao' => 'VARCHAR(100) NOT NULL',
            'descricao' => 'TEXT',
            'dados_anteriores' => 'JSON',
            'dados_novos' => 'JSON',
            'ip_address' => 'VARCHAR(45)',
            'user_agent' => 'TEXT',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'INDEX idx_usuario_id (usuario_id)',
            'INDEX idx_empresa_id (empresa_id)',
            'INDEX idx_tipo (tipo)',
            'INDEX idx_acao (acao)',
            'INDEX idx_created_at (created_at)',
            'FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE',
            'FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE'
        ]);
    }

    public function down(): void
    {
        $this->dropTable('atividades');
    }
} 