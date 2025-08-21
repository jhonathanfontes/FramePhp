<?php

use Core\Database\Migration;

class CreateEstabelecimentosTable extends Migration
{
    public function up()
    {
        $this->createTable('estabelecimentos', [
            'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
            'empresa_id' => 'INT NOT NULL',
            'nome' => 'VARCHAR(255) NOT NULL',
            'cnpj' => 'VARCHAR(18) UNIQUE',
            'endereco' => 'TEXT',
            'cidade' => 'VARCHAR(100)',
            'estado' => 'VARCHAR(2)',
            'cep' => 'VARCHAR(10)',
            'telefone' => 'VARCHAR(20)',
            'email' => 'VARCHAR(255)',
            'responsavel' => 'VARCHAR(255)',
            'status' => 'ENUM("ativo", "inativo") DEFAULT "ativo"',
            'tipo_estabelecimento' => 'VARCHAR(100)',
            'data_abertura' => 'DATE',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'INDEX idx_empresa_id (empresa_id)',
            'INDEX idx_status (status)',
            'INDEX idx_estado (estado)',
            'INDEX idx_cidade (cidade)',
            'FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE'
        ]);
    }

    public function down()
    {
        $this->dropTable('estabelecimentos');
    }
} 