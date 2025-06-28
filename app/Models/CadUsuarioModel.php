<?php

namespace App\Models;

use Core\Database\Model;
use Core\Database\Database;

class CadUsuarioModel extends Model
{
    protected $table = 'cad_usuario';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
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

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByEmail(string $email): ?array
    {
        try {
            return $this->db->find($this->table, 'use_email = ?', [$email]);
        } catch (\Exception $e) {
            error_log("Erro ao buscar usuário por email: " . $e->getMessage());
            return null;
        }
    }

    public function findById(int $id): ?array
    {
        return $this->db->find($this->table, 'id_usuario = ?', [$id]);
    }


    public function findAllUsers(): array
    {
        return $this->db->findAll($this->table, 'deleted_at IS NULL', [], '*', 'use_nome ASC');
    }

    public function create(array $data): int
    {
        if (isset($data['use_password'])) {
            $data['use_password'] = password_hash($data['use_password'], PASSWORD_DEFAULT);
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_user_id'] = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        return $this->db->insert($this->table, $data);
    }

    public function update(int $id, array $data): int
    {
        if (isset($data['use_password'])) {
            $data['use_password'] = password_hash($data['use_password'], PASSWORD_DEFAULT);
        }

        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_user_id'] = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        return $this->db->update($this->table, $data, 'id_usuario = ?', [$id]);
    }

    public function delete(int $id): int
    {
        $data = [
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null
        ];

        return $this->db->update($this->table, $data, 'id_usuario = ?', [$id]);
    }

    public function findByUsername(string $username): ?array
    {
        try {
            return $this->db->find($this->table, 'use_username = ?', [$username]);
        } catch (\Exception $e) {
            error_log("Erro ao buscar usuário por username: " . $e->getMessage());
            return null;
        }
    }

    public function createPasswordReset(string $email, string $token): bool
    {
        // Primeiro, remove qualquer token existente para este e-mail
        $this->db->delete('password_resets', 'email = ?', [$email]);

        // Insere o novo token
        $this->db->insert('password_resets', [
            'email' => $email,
            'token' => $token
        ]);

        return true;
    }

    public function findPasswordReset(string $token): ?array
    {
        return $this->db->find('password_resets', 'token = ?', [$token]);
    }

    public function deletePasswordReset(string $email): int
    {
        return $this->db->delete('password_resets', 'email = ?', [$email]);
    }
}