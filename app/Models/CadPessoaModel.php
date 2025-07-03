<?php

namespace App\Models;

use Core\Database\Model;
use Core\Database\Database; // Mantenha se houver métodos específicos do Database que não estejam no Model base

class CadPessoaModel extends Model
{
    protected $table = 'cad_pessoa';
    protected $primaryKey = 'id_pessoa';

    // Os campos fillable devem incluir todos os campos que você permite serem preenchidos
    // via 'create' ou 'update', incluindo os campos de auditoria para que o Model base
    // saiba quais automatizar.
    protected $fillable = [
        'tipo_cliente',
        'pes_nome',
        'pes_apelido',
        'pes_cpf',
        'pes_rg',
        'pes_cnpj',
        'pes_tiponatureza',
        'pes_datanascimento',
        'pes_email',
        'pes_telefone',
        'pes_celular',
        'pes_endereco',
        'pes_numero',
        'pes_setor',
        'pes_complemento',
        'pes_cidade',
        'pes_estado',
        'pes_cep',
        'dadosbancarios_id',
        'profissao_id',
        'status',
        'pes_padrao',
        'created_user_id',
        'created_at',
        'updated_user_id',
        'updated_at',
        'deleted_user_id',
        'deleted_at'
    ];

    // O construtor é opcional aqui, pois o construtor do Model base já se encarrega
    // de instanciar o Database.
    // public function __construct()
    // {
    //     parent::__construct();
    // }

    /**
     * Busca uma pessoa pelo seu ID.
     *
     * @param int $id O ID da pessoa.
     * @return array|null Retorna os dados da pessoa ou null se não encontrada.
     */
    public function findById(int $id): ?array
    {
        return $this->find('id_pessoa = ?', [$id]);
    }

    /**
     * Busca todas as pessoas ativas (não deletadas).
     *
     * @return array Retorna um array de todas as pessoas.
     */
    public function findAllPessoas(): array
    {
        return $this->findAll('deleted_at IS NULL', [], '*', 'pes_nome ASC');
    }

    /**
     * Cria um novo registro de pessoa.
     *
     * Os campos de auditoria serão preenchidos automaticamente pelo Model base.
     *
     * @param array $data Os dados da pessoa a serem inseridos.
     * @return int O ID da nova pessoa inserida.
     */
    public function create(array $data): int
    {
        return parent::insert($data);
    }

    /**
     * Atualiza um registro de pessoa existente.
     *
     * Os campos de auditoria serão preenchidos automaticamente pelo Model base.
     *
     * @param int $id O ID da pessoa a ser atualizada.
     * @param array $data Os dados atualizados da pessoa.
     * @return int O número de linhas afetadas pela atualização.
     */
    public function update(int $id, array $data): int
    {
        return parent::update($id, $data);
    }

    /**
     * Realiza um "soft delete" em um registro de pessoa, marcando-o como deletado.
     *
     * Os campos de auditoria de exclusão serão preenchidos automaticamente pelo Model base.
     *
     * @param int $id O ID da pessoa a ser deletada.
     * @return int O número de linhas afetadas.
     */
    public function delete(int $id): int
    {
        return parent::softDelete($id);
    }

    /**
     * Busca uma pessoa pelo seu e-mail.
     *
     * @param string $email O e-mail da pessoa.
     * @return array|null Retorna os dados da pessoa ou null se não encontrada.
     */
    public function findByEmail(string $email): ?array
    {
        try {
            return $this->find('pes_email = ?', [$email]);
        } catch (\Exception $e) {
            error_log("Erro ao buscar pessoa por email: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Busca uma pessoa pelo seu CPF.
     *
     * @param string $cpf O CPF da pessoa.
     * @return array|null Retorna os dados da pessoa ou null se não encontrada.
     */
    public function findByCpf(string $cpf): ?array
    {
        try {
            return $this->find('pes_cpf = ?', [$cpf]);
        } catch (\Exception $e) {
            error_log("Erro ao buscar pessoa por CPF: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Busca uma pessoa pelo seu CNPJ.
     *
     * @param string $cnpj O CNPJ da pessoa.
     * @return array|null Retorna os dados da pessoa ou null se não encontrada.
     */
    public function findByCnpj(string $cnpj): ?array
    {
        try {
            return $this->find('pes_cnpj = ?', [$cnpj]);
        } catch (\Exception $e) {
            error_log("Erro ao buscar pessoa por CNPJ: " . $e->getMessage());
            return null;
        }
    }
}