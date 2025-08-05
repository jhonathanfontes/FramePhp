<?php

use Core\Database\Migration;
use Core\Database\Schema;
use Core\Database\Blueprint;

class CreateProdutosTable extends Migration
{
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->string('codigo', 50)->unique();
            $table->string('nome', 150);
            $table->text('descricao')->nullable();
            $table->decimal('preco', 10, 2);
            $table->decimal('preco_promocional', 10, 2)->nullable();
            $table->integer('estoque')->default(0);
            $table->integer('estoque_minimo')->default(0);
            $table->string('imagem', 255)->nullable();
            $table->boolean('ativo')->default(true);
            $table->boolean('destaque')->default(false);
            $table->decimal('peso', 8, 3)->nullable();
            $table->decimal('altura', 8, 2)->nullable();
            $table->decimal('largura', 8, 2)->nullable();
            $table->decimal('comprimento', 8, 2)->nullable();
            $table->timestamp('data_cadastro')->useCurrent();
            $table->timestamp('data_atualizacao')->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
} 