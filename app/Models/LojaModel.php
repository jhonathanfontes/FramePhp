<?php

namespace App\Models;

use Core\Database\Model;
use Core\Database\Database;

class LojaModel extends Model
{
    protected $table = 'lojas';
    protected $primaryKey = 'id_loja';

    protected $fillable = [
        'empresa_id',
        'nome_loja',
        'dominio',
        'slug',
        'descricao',
        'logo',
        'banner',
        'tema_id',
        'configuracoes_json',
        'status',
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
        return $this->db->find($this->table, '*', 'id_loja = ?', [$id]);
    }

    public function findByEmpresa(int $empresaId): array
    {
        return $this->db->findAll($this->table, '*', 'empresa_id = ? AND status = ?', [$empresaId, 'ativo']);
    }

    public function findByDominio(string $dominio): ?array
    {
        return $this->db->find($this->table, '*', 'dominio = ? OR slug = ?', [$dominio, $dominio]);
    }

    public function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert($this->table, $data);
    }

    public function update(int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update($this->table, $data, 'id_loja = ?', [$id]);
    }

    public function createLoja($data)
    {
        return $this->create($data);
    }

    /**
     * Busca uma loja pelo slug
     */
    public function findBySlug($slug)
    {
        try {
            return $this->find('slug = ?', [$slug]);
        } catch (\Exception $e) {
            error_log("Erro ao buscar loja por slug: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Busca lojas ativas de uma empresa
     */
    public function findActiveByEmpresa($empresaId)
    {
        try {
            return $this->findAll('empresa_id = ? AND status = ?', [$empresaId, 'ativo']);
        } catch (\Exception $e) {
            error_log("Erro ao buscar lojas ativas: " . $e->getMessage());
            return [];
        }
    }
}