<?php

namespace App\Models;

use Core\Database\Model;
use Core\Database\Database;

class CadBancoModel extends Model
{
    protected $table = 'cad_banco';
    protected $primaryKey = 'ban_codigo';

    protected $fillable = [
        'ban_codigo',
        'ban_descricao'
    ];

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Busca um banco pelo seu código (chave primária).
     *
     * @param string $codigo O código do banco.
     * @return array|null Retorna os dados do banco ou null se não encontrado.
     */
    public function findByCodigo(string $codigo): ?array
    {
        return $this->db->find($this->table, '*', 'ban_codigo = ?', [$codigo]);
    }

    /**
     * Busca todos os bancos.
     *
     * @return array Retorna um array de todos os bancos.
     */
    public function findAllBancos(): array
    {
        return $this->db->findAll($this->table, '*', '1', [], 'ban_descricao ASC');
    }

    /**
     * Cria um novo registro de banco.
     *
     * @param array $data Os dados do banco a serem inseridos.
     * @return int O ID (ou neste caso, 0 ou 1 para sucesso) da operação.
     */
    public function create(array $data): int
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Atualiza um registro de banco existente.
     *
     * @param string $codigo O código do banco a ser atualizado.
     * @param array $data Os dados atualizados do banco.
     * @return int O número de linhas afetadas pela atualização.
     */
    public function update(string $codigo, array $data): int
    {
        return $this->db->update($this->table, $data, 'ban_codigo = ?', [$codigo]);
    }

    /**
     * Exclui permanentemente um registro de banco.
     *
     * @param string $codigo O código do banco a ser deletado.
     * @return int O número de linhas afetadas.
     */
    public function delete(string $codigo): int
    {
        return $this->db->delete($this->table, 'ban_codigo = ?', [$codigo]);
    }

    /**
     * Busca um banco pela sua descrição.
     *
     * @param string $descricao A descrição do banco.
     * @return array|null Retorna os dados do banco ou null se não encontrado.
     */
    public function findByDescricao(string $descricao): ?array
    {
        try {
            return $this->db->find($this->table, '*', 'ban_descricao = ?', [$descricao]);
        } catch (\Exception $e) {
            error_log("Erro ao buscar banco por descrição: " . $e->getMessage());
            return null;
        }
    }
}
