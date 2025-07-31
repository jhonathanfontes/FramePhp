<?php

namespace App\Models;

use Core\Database\Model;
use Core\Database\Database;

class CadProdutoModel extends Model
{
    protected $table = 'cad_produto';
    protected $primaryKey = 'id_produto';

    protected $fillable = [
        'subcategoria_id',
        'fabricante_id',
        'pro_descricao',
        'pro_descricao_pvd',
        'pro_cod_fabricante',
        'pro_codigobarras',
        'status',
        'created_user_id', // Geralmente gerenciado automaticamente, mas incluído se necessário manual
        'created_at',      // Geralmente gerenciado automaticamente, mas incluído se necessário manual
        'updated_user_id', // Geralmente gerenciado automaticamente, mas incluído se necessário manual
        'updated_at',      // Geralmente gerenciado automaticamente, mas incluído se necessário manual
        'deleted_user_id', // Para soft delete, se aplicável
        'deleted_at'       // Para soft delete, se aplicável
    ];

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Busca um produto pelo seu ID.
     *
     * @param int $id O ID do produto.
     * @return array|null Retorna os dados do produto ou null se não encontrado.
     */
    public function findById(int $id): ?array
    {
        return $this->db->find($this->table, '*', 'id_produto = ?', [$id]);
    }

    /**
     * Busca todos os produtos ativos (não deletados).
     *
     * @return array Retorna um array de todos os produtos.
     */
    public function findAllProducts(): array
    {
        return $this->db->findAll($this->table, '*', 'deleted_at IS NULL', [], 'pro_descricao ASC');
    }

    /**
     * Cria um novo registro de produto.
     *
     * @param array $data Os dados do produto a serem inseridos.
     * @return int O ID do novo produto inserido.
     */
    public function create(array $data): int
    {
        // Campos de auditoria (created_at, created_user_id)
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_user_id'] = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        return $this->db->insert($this->table, $data);
    }

    /**
     * Atualiza um registro de produto existente.
     *
     * @param int $id O ID do produto a ser atualizado.
     * @param array $data Os dados atualizados do produto.
     * @return int O número de linhas afetadas pela atualização.
     */
    public function update(int $id, array $data): int
    {
        // Campos de auditoria (updated_at, updated_user_id)
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_user_id'] = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        return $this->db->update($this->table, $data, 'id_produto = ?', [$id]);
    }

    /**
     * Realiza um "soft delete" em um registro de produto, marcando-o como deletado.
     *
     * @param int $id O ID do produto a ser deletado.
     * @return int O número de linhas afetadas.
     */
    public function delete(int $id): int
    {
        $data = [
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null
        ];

        return $this->db->update($this->table, $data, 'id_produto = ?', [$id]);
    }

    /**
     * Busca um produto pelo seu código de barras.
     *
     * @param string $codigoBarras O código de barras do produto.
     * @return array|null Retorna os dados do produto ou null se não encontrado.
     */
    public function findByCodigoBarras(string $codigoBarras): ?array
    {
        try {
            return $this->db->find($this->table, '*', 'pro_codigobarras = ?', [$codigoBarras]);
        } catch (\Exception $e) {
            error_log("Erro ao buscar produto por código de barras: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Busca produtos por parte da descrição.
     *
     * @param string $descricao A descrição ou parte dela.
     * @return array Retorna um array de produtos correspondentes.
     */
    public function searchByDescription(string $descricao): array
    {
        return $this->db->findAll($this->table, '%', 'pro_descricao LIKE %?% AND deleted_at IS NULL', [$descricao], 'pro_descricao ASC');
    }
}
<?php

namespace App\Models;

use Core\Database\Model;

class CadProdutoModel extends Model
{
    protected $table = 'cad_produto';
    protected $primaryKey = 'id_produto';

    protected $fillable = [
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
        return $this->findAll('deleted_at IS NULL AND status = "ativo"', [], '*', 'pro_nome ASC');
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

        return $this->findAll($sql, $params, '*', $orderBy);
    }

    /**
     * Buscar produtos por termo
     */
    public function buscarProdutos(string $termo): array
    {
        $sql = '(pro_nome LIKE ? OR pro_descricao LIKE ? OR pro_tags LIKE ?) AND deleted_at IS NULL AND status = "ativo"';
        $termoBusca = '%' . $termo . '%';
        $params = [$termoBusca, $termoBusca, $termoBusca];

        return $this->findAll($sql, $params, '*', 'pro_nome ASC');
    }

    /**
     * Produtos em destaque
     */
    public function findEmDestaque(int $limit = 8): array
    {
        $sql = 'pro_destaque = 1 AND deleted_at IS NULL AND status = "ativo"';
        
        return $this->findAll($sql, [], '*', 'created_at DESC LIMIT ' . $limit);
    }

    /**
     * Produtos em promoção
     */
    public function findEmPromocao(int $limit = 8): array
    {
        $sql = 'pro_preco_promocional IS NOT NULL AND pro_preco_promocional > 0 AND deleted_at IS NULL AND status = "ativo"';
        
        return $this->findAll($sql, [], '*', 'created_at DESC LIMIT ' . $limit);
    }
}
