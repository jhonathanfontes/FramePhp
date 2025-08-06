<?php

use Core\Database\Migration;
use Core\Database\Schema;
use Core\Database\Blueprint;

class CreatePessoasTable extends Migration
{
    public function up(): void
    {
        Schema::create('pessoas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('nome', 100);
            $table->string('email', 100)->nullable();
            $table->string('cpf_cnpj', 18)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('endereco', 200)->nullable();
            $table->string('cidade', 50)->nullable();
            $table->string('estado', 2)->nullable();
            $table->string('cep', 10)->nullable();
            $table->enum('tipo', ['fisica', 'juridica'])->default('fisica');
            $table->boolean('ativo')->default(true);
            $table->timestamp('data_cadastro')->useCurrent();
            $table->timestamp('data_atualizacao')->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pessoas');
    }
} 