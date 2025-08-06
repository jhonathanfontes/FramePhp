<?php

use Core\Database\Migration;
use Core\Database\Schema;
use Core\Database\Blueprint;

class CreateVendasTable extends Migration
{
    public function up(): void
    {
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('pessoa_id')->nullable()->constrained('pessoas')->onDelete('set null');
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->string('numero_pedido', 50)->unique();
            $table->enum('status', ['pendente', 'aprovado', 'em_preparo', 'enviado', 'entregue', 'cancelado'])->default('pendente');
            $table->enum('forma_pagamento', ['dinheiro', 'cartao_credito', 'cartao_debito', 'pix', 'transferencia'])->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('desconto', 10, 2)->default(0);
            $table->decimal('frete', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->text('observacoes')->nullable();
            $table->string('endereco_entrega', 200)->nullable();
            $table->string('cidade_entrega', 50)->nullable();
            $table->string('estado_entrega', 2)->nullable();
            $table->string('cep_entrega', 10)->nullable();
            $table->timestamp('data_venda')->useCurrent();
            $table->timestamp('data_atualizacao')->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendas');
    }
} 