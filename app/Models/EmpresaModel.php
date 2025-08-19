<?php

namespace App\Models;

use Core\Database\Model;

class EmpresaModel extends Model
{
    protected string $table = 'empresas';
    
    protected array $fillable = [
        'nome',
        'cnpj',
        'status',
        'created_at',
        'updated_at'
    ];

    public function beforeCreate(array &$data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['status'] = $data['status'] ?? 'ativo';
    }

    public function beforeUpdate(array &$data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
    }
}
