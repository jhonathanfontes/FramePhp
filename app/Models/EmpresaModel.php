
<?php

namespace App\Models;

use Core\Database\Model;
use Core\Database\Database;

class EmpresaModel extends Model
{
    protected $table = 'empresas';
    protected $primaryKey = 'id_empresa';

    protected $fillable = [
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'inscricao_estadual',
        'email',
        'telefone',
        'endereco',
        'cidade',
        'estado',
        'cep',
        'logo',
        'status',
        'plano_id',
        'data_vencimento',
        'created_at',
        'updated_at'
    ];

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        return $this->db->find($this->table, '*', 'id_empresa = ?', [$id]);
    }

    public function findAllEmpresas(): array
    {
        return $this->db->findAll($this->table, '*', 'status = ?', ['ativo'], 'razao_social ASC');
    }

    public function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert($this->table, $data);
    }

    public function update(int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update($this->table, $data, 'id_empresa = ?', [$id]);
    }

    public function findByCnpj(string $cnpj): ?array
    {
        return $this->db->find($this->table, '*', 'cnpj = ?', [$cnpj]);
    }
}
