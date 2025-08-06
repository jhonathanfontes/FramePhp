<?php

namespace App\Models;

use Core\Database\Model;

class CadCategoriaModel extends Model
{
    protected string $table = 'cad_categoria';
    protected string $primaryKey = 'id_categoria';
    protected bool $softDelete = false;
    
    protected array $fillable = [
        'cat_nome',
        'cat_descricao',
        'cat_imagem',
        'status',
        'created_at',
        'updated_at'
    ];

    /**
     * Busca todas as categorias ativas
     */
    public function findAllCategorias(): array
    {
        return $this->query()
            ->where('status', 'ativo')
            ->orderBy('cat_nome', 'ASC')
            ->get();
    }

    /**
     * Busca categoria por ID
     */
    public function findById($id): ?array
    {
        return $this->find($id);
    }
}
