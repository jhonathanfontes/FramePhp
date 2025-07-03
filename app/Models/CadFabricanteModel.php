<?php

namespace App\Models;

use Core\Database\Model;
use Core\Database\Database; // Certifique-se de que o namespace para Database está correto

class CadFabricanteModel extends Model
{
    protected $table = 'cad_fabricante';
    protected $primaryKey = 'id_fabricante';

    protected $fillable = [
        'fab_descricao',
        'status',
        'created_user_id',
        'created_at',
        'updated_user_id',
        'updated_at',
        'deleted_user_id',
        'deleted_at'
    ];

    private $db; // Instância do banco de dados

    public function __construct()
    {
        // Instancia diretamente o Database, conforme o modelo fornecido.
        $this->db = Database::getInstance();
    }

    /**
     * Busca um fabricante pelo seu ID.
     *
     * @param int $id O ID do fabricante.
     * @return array|null Retorna os dados do fabricante ou null se não encontrado.
     */
    public function findById(int $id): ?array
    {
        // Utiliza diretamente o método find da instância Database.
        return $this->db->find($this->table, '*', 'id_fabricante = ?', [$id]);
    }

    /**
     * Busca todos os fabricantes ativos (não deletados).
     *
     * @return array Retorna um array de todos os fabricantes.
     */
    public function findAllFabricantes(): array
    {
        // Utiliza diretamente o método findAll da instância Database.
        // O segundo parâmetro '*' é para as colunas, o terceiro 'deleted_at IS NULL' é a condição WHERE,
        // o quarto '[]' são os parâmetros da condição, e o quinto 'fab_descricao ASC' é a ordenação.
        return $this->db->findAll($this->table, '*', 'deleted_at IS NULL', [], 'fab_descricao ASC');
    }

    /**
     * Cria um novo registro de fabricante.
     *
     * Os campos de auditoria (created_at, created_user_id) são preenchidos manualmente aqui,
     * seguindo o padrão do CadContaBancariaModel.
     *
     * @param array $data Os dados do fabricante a serem inseridos.
     * @return int O ID do novo fabricante inserido.
     */
    public function create(array $data): int
    {
        // Campos de auditoria (created_at, created_user_id)
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_user_id'] = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        return $this->db->insert($this->table, $data);
    }

    /**
     * Atualiza um registro de fabricante existente.
     *
     * Os campos de auditoria (updated_at, updated_user_id) são preenchidos manualmente aqui,
     * seguindo o padrão do CadContaBancariaModel.
     *
     * @param int $id O ID do fabricante a ser atualizado.
     * @param array $data Os dados atualizados do fabricante.
     * @return int O número de linhas afetadas pela atualização.
     */
    public function update(int $id, array $data): int
    {
        // Campos de auditoria (updated_at, updated_user_id)
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_user_id'] = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        return $this->db->update($this->table, $data, 'id_fabricante = ?', [$id]);
    }

    /**
     * Realiza um "soft delete" em um registro de fabricante, marcando-o como deletado.
     *
     * Os campos de auditoria de exclusão (deleted_at, deleted_user_id) são preenchidos manualmente aqui,
     * seguindo o padrão do CadContaBancariaModel.
     *
     * @param int $id O ID do fabricante a ser deletado.
     * @return int O número de linhas afetadas.
     */
    public function delete(int $id): int
    {
        $data = [
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null
        ];

        return $this->db->update($this->table, $data, 'id_fabricante = ?', [$id]);
    }

    /**
     * Busca um fabricante pela sua descrição.
     *
     * @param string $descricao A descrição do fabricante.
     * @return array|null Retorna os dados do fabricante ou null se não encontrado.
     */
    public function findByDescricao(string $descricao): ?array
    {
        try {
            // Utiliza diretamente o método find da instância Database.
            return $this->db->find($this->table, '*', 'fab_descricao = ?', [$descricao]);
        } catch (\Exception $e) {
            error_log("Erro ao buscar fabricante por descrição: " . $e->getMessage());
            return null;
        }
    }
}
