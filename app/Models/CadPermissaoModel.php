<?php

namespace App\Models;

use Core\Database\Model;

class CadPermissaoModel extends Model
{
    protected $table = 'cad_permissao';
    protected $primaryKey = 'id_permissao';

    protected $fillable = [
        'per_nome',
        'per_descricao',
        'status',
        'created_user_id',
        'created_at',
        'updated_user_id',
        'updated_at',
        'deleted_user_id',
        'deleted_at'
    ];

    /**
     * Busca uma permissão pelo seu ID.
     *
     * @param int $id O ID da permissão.
     * @return array|null Retorna os dados da permissão ou null se não encontrada.
     */
    public function findById(int $id): ?array
    {
        return $this->find('id_permissao = ?', [$id]);
    }

    /**
     * Busca todas as permissões ativas (não deletadas).
     *
     * @return array Retorna um array de todas as permissões.
     */
    public function findAllPermissoes(): array
    {
        return $this->findAll('deleted_at IS NULL', [], '*', 'per_nome ASC');
    }

    /**
     * Cria um novo registro de permissão.
     *
     * Os campos de auditoria serão preenchidos automaticamente pelo Model base.
     *
     * @param array $data Os dados da permissão a serem inseridos.
     * @return int O ID da nova permissão inserida.
     */
    public function create(array $data): int
    {
        return parent::insert($data);
    }

    /**
     * Atualiza um registro de permissão existente.
     *
     * Os campos de auditoria serão preenchidos automaticamente pelo Model base.
     *
     * @param int $id O ID da permissão a ser atualizada.
     * @param array $data Os dados atualizados da permissão.
     * @return int O número de linhas afetadas pela atualização.
     */
    public function update(int $id, array $data): int
    {
        return parent::update($id, $data);
    }

    /**
     * Realiza um "soft delete" em um registro de permissão.
     *
     * Os campos de auditoria de exclusão serão preenchidos automaticamente pelo Model base.
     *
     * @param int $id O ID da permissão a ser deletada.
     * @return int O número de linhas afetadas.
     */
    public function delete(int $id): int
    {
        return parent::softDelete($id);
    }

    /**
     * Busca uma permissão pelo seu nome.
     *
     * @param string $nome O nome da permissão.
     * @return array|null Retorna os dados da permissão ou null se não encontrada.
     */
    public function findByNome(string $nome): ?array
    {
        try {
            return $this->find('per_nome = ?', [$nome]);
        } catch (\Exception $e) {
            error_log("Erro ao buscar permissão por nome: " . $e->getMessage());
            return null;
        }
    }
}