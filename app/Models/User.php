<?php

namespace App\Models;

use Core\Database\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    public function findByEmail(string $email): ?array
    {
        try {
            $user = $this->query()->where('email', '=', $email)->get();
            return $user;
        } catch (\Exception $e) {
            error_log("Erro ao buscar usuário por email: " . $e->getMessage());
            return null;
        }
    }

    public function findAll(): array
    {
        return $this->all();
    }


    public function findById(int $id): ?array
    {
        return $this->find($id);
    }

    public function create(array $data): self 
    {
        // Hash da senha
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        return $this->insert($data);
    }

    public function update(int $id, array $data): int
    {
        // Se estiver atualizando a senha, faz o hash
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        return $this->update($id, $data);
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
        return $this->find('password_resets', 'token = ?', [$token]);
    }

    public function deletePasswordReset(string $email): int
    {
        return $this->delete('password_resets', 'email = ?', [$email]);
    }
}