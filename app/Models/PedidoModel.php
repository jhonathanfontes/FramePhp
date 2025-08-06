<?php

namespace App\Models;

use Core\Database\Model;
use Core\Database\Database;

class PedidoModel extends Model
{
    protected string $table = 'pedidos';
    protected string $primaryKey = 'id_pedido';

    protected array $fillable = [
        'loja_id',
        'cliente_id',
        'numero_pedido',
        'status',
        'valor_produtos',
        'valor_frete',
        'valor_desconto',
        'valor_total',
        'forma_pagamento',
        'status_pagamento',
        'observacoes',
        'endereco_entrega_json',
        'created_at',
        'updated_at'
    ];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }


    public function findByUserId(int $userId): array
    {
        return $this->db->findAll($this->table, '*', 'cliente_id = ?', [$userId], 'created_at DESC');
    }

    public function findById(int $id): ?array
    {
        return $this->db->find($this->table, '*', 'id_pedido = ?', [$id]);
    }

    public function findByLoja(int $lojaId): array
    {
        return $this->db->findAll($this->table, '*', 'loja_id = ?', [$lojaId], 'created_at DESC');
    }

    public function create(array $data): int
    {
        $data['numero_pedido'] = $this->generateOrderNumber();
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert($this->table, $data);
    }

    public function update(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update($this->table, $data, 'id_pedido = ?', [$id]);
    }

    private function generateOrderNumber(): string
    {
        return date('Y') . date('m') . date('d') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
}
