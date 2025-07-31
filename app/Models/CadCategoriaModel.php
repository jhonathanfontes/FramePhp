<?php

namespace App\Models;

use Core\Database\Model;

class CadCategoriaModel extends Model
{
    protected $table = 'cad_categoria';
    protected $primaryKey = 'id_categoria';

    protected $fillable = [
        'cat_nome',
        'cat_descricao',
        'cat_imagem',
        'cat_slug',
        'status',
        'created_user_id',
        'created_at',
        'updated_user_id',
        'updated_at',
        'deleted_user_id',
        'deleted_at'
    ];

    /**
     * Busca todas as categorias ativas
     */
    public function findAllCategorias(): array
    {
        return $this->findAll('deleted_at IS NULL AND status = "ativo"', [], '*', 'cat_nome ASC');
    }

    /**
     * Busca categoria por slug
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->find('cat_slug = ? AND deleted_at IS NULL', [$slug]);
    }
}
