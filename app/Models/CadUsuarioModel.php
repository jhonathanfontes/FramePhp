<?php

namespace App\Models;

use Core\Database\Model;

class CadUsuarioModel extends Model
{
    protected string $table = 'cad_usuario';
    protected string $primaryKey = 'id_usuario'; // Alterado de $primaryKey para string type hint
    protected bool $softDelete = true; // Habilita soft delete para a tabela 'users'
    protected string $deletedAtColumn = 'deleted_at'; // Coluna de soft delete
    protected array $fillable = [ // Adicionado type hint array
        'use_nome',
        'use_apelido',
        'use_username',
        'use_password',
        'use_email',
        'use_telefone',
        'use_avatar',
        'use_sexo',
        'status',
        'permissao_id'
    ];

    public function findByEmail(string $email): ?object 
    {
        try {
            return $this->query()
                ->where('use_email', $email)
                ->first();            
        } catch (\Exception $e) {
            // Erros são tratados pelo ErrorHandler global, mas logs específicos ainda podem ser úteis.
            error_log("Erro ao buscar usuário por email: " . $e->getMessage());
            return null;
        }
    }

    public function findById(int $id): ?object // Retorna ?object para consistência
    {
        try {
            // O método find da classe pai Model já faz isso.
            // Para ter os atributos como objeto, o método find do Model já retorna ?self (que é um objeto).
            return $this->find($id);
        } catch (\Exception $e) {
            error_log("Erro ao buscar usuário por ID: " . $e->getMessage());
            return null;
        }
    }

       public function findByUsername(string $username): ?object // Retorna ?object
    {
        try {
            // Usa o QueryBuilder da classe pai (Model)
            return $this->query()
                ->where('use_username', '=', $username)
                ->first();
        } catch (\Exception $e) {
            error_log("Erro ao buscar usuário por username: " . $e->getMessage());
            return null;
        }
    }

    public function createPasswordReset(string $email, string $token): bool
    {
        $database = \Core\Database\Database::getInstance(); // Ainda precisa de Database para tabelas diferentes

        // Primeiro, remove qualquer token existente para este e-mail
        $database->delete('password_resets', 'email = ?', [$email]);

        // Insere o novo token
        $database->insert('password_resets', [
            'email' => $email,
            'token' => $token
        ]);

        return true;
    }

}