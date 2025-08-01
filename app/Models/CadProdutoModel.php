<?php

namespace App\Models;

use Core\Database\Model;

class CadProdutoModel extends Model
{
    protected string $table = 'cad_produto';
    protected string $primaryKey = 'id_produto';

    protected array $fillable = [
        'pro_nome',
        'pro_descricao',
        'pro_preco',
        'pro_preco_promocional',
        'pro_estoque',
        'pro_sku',
        'pro_imagem',
        'categoria_id',
        'fabricante_id',
        'status',
        'pro_peso',
        'pro_dimensoes',
        'pro_tags',
        'created_user_id',
        'created_at',
        'updated_user_id',
        'updated_at',
        'deleted_user_id',
        'deleted_at'
    ];

    /**
     * Busca todos os produtos ativos
     */
    public function findAllProdutos(): array
    {
        return $this->db->findAll('deleted_at IS NULL AND status = "ativo"', [], '*', 'pro_nome ASC');
    }

    /**
     * Busca produtos por categoria
     */
    public function findByCategoria(int $categoriaId, int $limit = null): array
    {
        $sql = 'categoria_id = ? AND deleted_at IS NULL AND status = "ativo"';
        $params = [$categoriaId];
        $orderBy = 'pro_nome ASC';

        if ($limit) {
            $orderBy .= ' LIMIT ' . $limit;
        }

        return $this->db->findAll($sql, '*', $params, $orderBy);
    }

    /**
     * Buscar produtos por termo
     */
    public function buscarProdutos(string $termo): array
    {
        $sql = '(pro_nome LIKE ? OR pro_descricao LIKE ? OR pro_tags LIKE ?) AND deleted_at IS NULL AND status = "ativo"';
        $termoBusca = '%' . $termo . '%';
        $params = [$termoBusca, $termoBusca, $termoBusca];

        return $this->db->findAll($sql, $params, '*', 'pro_nome ASC');
    }

    /**
     * Produtos em destaque
     */
    public function findEmDestaque(int $limit = 8): array
    {
        $sql = 'pro_destaque = 1 AND deleted_at IS NULL AND status = "ativo"';

        return $this->db->findAll($sql, [], '*', 'created_at DESC LIMIT ' . $limit);
    }

    /**
     * Produtos em promoção
     */
    public function findEmPromocao(int $limit = 8): array
    {
        $sql = 'pro_preco_promocional IS NOT NULL AND pro_preco_promocional > 0 AND deleted_at IS NULL AND status = "ativo"';

        return $this->db->findAll($sql, [], '*', 'created_at DESC LIMIT ' . $limit);
    }

    /**
     * Busca produto por ID
     */
    public function findById($id): ?array
    {
        return $this->find($id);
    }
}
