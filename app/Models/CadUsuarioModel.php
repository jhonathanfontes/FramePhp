<?php

namespace App\Models;

use Core\Database\Model;
use Core\Database\Database;

class CadUsuarioModel extends Model
{
    protected $table = 'cad_usuario';
    protected $primaryKey = 'id_usuario';

    public function findByEmail(string $email): ?object
    {
        try {
            return $this->query()->where('use_email', '=', $email)->first();
        } catch (\Exception $e) {
            error_log("Erro ao buscar usuário por email: " . $e->getMessage());
            return null;
        }
    }

    public function findById(int $id): ?array
    {
        return $this->find($id);
    }


    public function findAllUsers(): array
    {
        return $this->all();
    }

    public function create(array $data): self
    {
        if (isset($data['use_password'])) {
            $data['use_password'] = password_hash($data['use_password'], PASSWORD_DEFAULT);
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_user_id'] = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        var_dump($data);
        die();

        return $this->save($data);
    }

    public function update(int $id, array $data): int
    {
        if (isset($data['use_password'])) {
            $data['use_password'] = password_hash($data['use_password'], PASSWORD_DEFAULT);
        }

        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_user_id'] = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        return $this->update($this->table, $data, 'id_usuario = ?', [$id]);
    }


    public function findByUsername(string $username): ?array
    {
        try {
            return $this->find($this->table, '*', 'use_username = ?', [$username]);
        } catch (\Exception $e) {
            error_log("Erro ao buscar usuário por username: " . $e->getMessage());
            return null;
        }
    }

    public function createPasswordReset(string $email, string $token): bool
    {
        // Primeiro, remove qualquer token existente para este e-mail
        $this->delete('password_resets', 'email = ?', [$email]);

        // Insere o novo token
        $this->insert('password_resets', [
            'email' => $email,
            'token' => $token
        ]);

        return true;
    }

    public function findPasswordReset(string $token): ?array
    {
        return $this->find('password_resets', '*', 'token = ?', [$token]);
    }

    public function deletePasswordReset(string $email): int
    {
        return $this->delete('password_resets', 'email = ?', [$email]);
    }
}