<?php

use Core\Database\Migration;
use Core\Database\Schema;
use Core\Database\Blueprint;

class CreateEmpresasTable extends Migration
{
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nome_fantasia', 100);
            $table->string('razao_social', 150);
            $table->string('cnpj', 18)->unique();
            $table->string('email', 100)->unique();
            $table->string('telefone', 20);
            $table->string('endereco', 200);
            $table->string('cidade', 50);
            $table->string('estado', 2);
            $table->string('cep', 10);
            $table->string('logo', 255)->nullable();
            $table->string('cor_primaria', 7)->default('#007bff');
            $table->string('cor_secundaria', 7)->default('#6c757d');
            $table->boolean('ativo')->default(true);
            $table->timestamp('data_cadastro')->useCurrent();
            $table->timestamp('data_atualizacao')->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
} 