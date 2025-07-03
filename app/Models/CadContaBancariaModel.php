<?php

namespace App\Models;

use Core\Database\Model;
use Core\Database\Database; // Certifique-se de que o namespace para Database está correto

class CadContaBancariaModel extends Model
{
    protected $table = 'cad_contabancaria';
    protected $primaryKey = 'id_conta';

    protected $fillable = [
        'con_tipo',
        'con_descricao',
        'ban_codigo',
        'con_agencia',
        'con_conta',
        'tipo_titular',
        'con_titular',
        'con_documento',
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
     * Busca uma conta bancária pelo seu ID.
     *
     * @param int $id O ID da conta bancária.
     * @return array|null Retorna os dados da conta bancária ou null se não encontrada.
     */
    public function findById(int $id): ?array
    {
        // Utiliza diretamente o método find da instância Database.
        return $this->db->find($this->table, '*', 'id_conta = ?', [$id]);
    }

    /**
     * Busca todas as contas bancárias ativas (não deletadas).
     *
     * @return array Retorna um array de todas as contas bancárias.
     */
    public function findAllContasBancarias(): array
    {
        // Utiliza diretamente o método findAll da instância Database.
        // O segundo parâmetro '*' é para as colunas, o terceiro 'deleted_at IS NULL' é a condição WHERE,
        // o quarto '[]' são os parâmetros da condição, e o quinto 'con_descricao ASC' é a ordenação.
        return $this->db->findAll($this->table, '*', 'deleted_at IS NULL', [], 'con_descricao ASC');
    }

    /**
     * Cria um novo registro de conta bancária.
     *
     * Os campos de auditoria (created_at, created_user_id) são preenchidos manualmente aqui,
     * seguindo o padrão do CadCategoriaModel.
     *
     * @param array $data Os dados da conta bancária a serem inseridos.
     * @return int O ID da nova conta bancária inserida.
     */
    public function create(array $data): int
    {
        // Campos de auditoria (created_at, created_user_id)
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_user_id'] = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        return $this->db->insert($this->table, $data);
    }

    /**
     * Atualiza um registro de conta bancária existente.
     *
     * Os campos de auditoria (updated_at, updated_user_id) são preenchidos manualmente aqui,
     * seguindo o padrão do CadCategoriaModel.
     *
     * @param int $id O ID da conta bancária a ser atualizada.
     * @param array $data Os dados atualizados da conta bancária.
     * @return int O número de linhas afetadas pela atualização.
     */
    public function update(int $id, array $data): int
    {
        // Campos de auditoria (updated_at, updated_user_id)
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_user_id'] = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        return $this->db->update($this->table, $data, 'id_conta = ?', [$id]);
    }

    /**
     * Realiza um "soft delete" em um registro de conta bancária, marcando-o como deletado.
     *
     * Os campos de auditoria de exclusão (deleted_at, deleted_user_id) são preenchidos manualmente aqui,
     * seguindo o padrão do CadCategoriaModel.
     *
     * @param int $id O ID da conta bancária a ser deletada.
     * @return int O número de linhas afetadas.
     */
    public function delete(int $id): int
    {
        $data = [
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null
        ];

        return $this->db->update($this->table, $data, 'id_conta = ?', [$id]);
    }

    /**
     * Busca uma conta bancária pela sua descrição.
     *
     * @param string $descricao A descrição da conta bancária.
     * @return array|null Retorna os dados da conta bancária ou null se não encontrada.
     */
    public function findByDescricao(string $descricao): ?array
    {
        try {
            // Utiliza diretamente o método find da instância Database.
            return $this->db->find($this->table, '*', 'con_descricao = ?', [$descricao]);
        } catch (\Exception $e) {
            error_log("Erro ao buscar conta bancária por descrição: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Busca contas bancárias por código do banco.
     *
     * @param string $banCodigo O código do banco.
     * @return array Retorna um array de contas bancárias associadas ao banco.
     */
    public function findByBancoCodigo(string $banCodigo): array
    {
        return $this->db->findAll($this->table, '*', 'ban_codigo = ? AND deleted_at IS NULL', [$banCodigo], 'con_descricao ASC');
    }
}
