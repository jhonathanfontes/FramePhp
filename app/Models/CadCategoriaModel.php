<?php

namespace App\Models;

use Core\Database\Model;
use Core\Database\Database; // Certifique-se de que o namespace para Database está correto

class CadCategoriaModel extends Model
{
    protected $table = 'cad_categoria';
    protected $primaryKey = 'id_categoria';

    protected $fillable = [
        'cat_descricao',
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
     * Busca uma categoria pelo seu ID.
     *
     * @param int $id O ID da categoria.
     * @return array|null Retorna os dados da categoria ou null se não encontrada.
     */
    public function findById(int $id): ?array
    {
        // Utiliza diretamente o método find da instância Database.
        return $this->db->find($this->table, '*', 'id_categoria = ?', [$id]);
    }

    /**
     * Busca todas as categorias ativas (não deletadas).
     *
     * @return array Retorna um array de todas as categorias.
     */
    public function findAllCategorias(): array
    {
        // Utiliza diretamente o método findAll da instância Database.
        // O segundo parâmetro '*' é para as colunas, o terceiro 'deleted_at IS NULL' é a condição WHERE,
        // o quarto '[]' são os parâmetros da condição, e o quinto 'cat_descricao ASC' é a ordenação.
        return $this->db->findAll($this->table, '*', 'deleted_at IS NULL', [], 'cat_descricao ASC');
    }

    /**
     * Cria um novo registro de categoria.
     *
     * Os campos de auditoria (created_at, created_user_id) são preenchidos manualmente aqui,
     * seguindo o padrão do CadProdutoModel fornecido.
     *
     * @param array $data Os dados da categoria a serem inseridos.
     * @return int O ID da nova categoria inserida.
     */
    public function create(array $data): int
    {
        // Campos de auditoria (created_at, created_user_id)
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_user_id'] = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        return $this->db->insert($this->table, $data);
    }

    /**
     * Atualiza um registro de categoria existente.
     *
     * Os campos de auditoria (updated_at, updated_user_id) são preenchidos manualmente aqui,
     * seguindo o padrão do CadProdutoModel fornecido.
     *
     * @param int $id O ID da categoria a ser atualizada.
     * @param array $data Os dados atualizados da categoria.
     * @return int O número de linhas afetadas pela atualização.
     */
    public function update(int $id, array $data): int
    {
        // Campos de auditoria (updated_at, updated_user_id)
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_user_id'] = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        return $this->db->update($this->table, $data, 'id_categoria = ?', [$id]);
    }

    /**
     * Realiza um "soft delete" em um registro de categoria, marcando-o como deletado.
     *
     * Os campos de auditoria de exclusão (deleted_at, deleted_user_id) são preenchidos manualmente aqui,
     * seguindo o padrão do CadProdutoModel fornecido.
     *
     * @param int $id O ID da categoria a ser deletada.
     * @return int O número de linhas afetadas.
     */
    public function delete(int $id): int
    {
        $data = [
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null
        ];

        return $this->db->update($this->table, $data, 'id_categoria = ?', [$id]);
    }

    /**
     * Busca uma categoria pela sua descrição.
     *
     * @param string $descricao A descrição da categoria.
     * @return array|null Retorna os dados da categoria ou null se não encontrada.
     */
    public function findByDescricao(string $descricao): ?array
    {
        try {
            // Utiliza diretamente o método find da instância Database.
            return $this->db->find($this->table, '*', 'cat_descricao = ?', [$descricao]);
        } catch (\Exception $e) {
            error_log("Erro ao buscar categoria por descrição: " . $e->getMessage());
            return null;
        }
    }
}
