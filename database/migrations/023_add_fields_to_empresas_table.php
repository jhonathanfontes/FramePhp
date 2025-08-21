<?php

use Core\Database\Migration;

class AddFieldsToEmpresasTable extends Migration
{
    public function up(): void
    {
        $this->addColumns('empresas', [
            'porte' => 'VARCHAR(50)',
            'responsavel' => 'VARCHAR(255)',
            'data_abertura' => 'DATE',
            'status' => 'ENUM("ativo", "inativo") DEFAULT "ativo"',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]);
        
        // Adicionar índices
        $this->addIndex('empresas', 'idx_status', ['status']);
        $this->addIndex('empresas', 'idx_porte', ['porte']);
        $this->addIndex('empresas', 'idx_estado', ['estado']);
        $this->addIndex('empresas', 'idx_cidade', ['cidade']);
    }

    public function down(): void
    {
        $this->dropColumns('empresas', [
            'porte',
            'responsavel', 
            'data_abertura',
            'status',
            'created_at',
            'updated_at'
        ]);
        
        // Remover índices
        $this->dropIndex('empresas', 'idx_status');
        $this->dropIndex('empresas', 'idx_porte');
        $this->dropIndex('empresas', 'idx_estado');
        $this->dropIndex('empresas', 'idx_cidade');
    }
} 