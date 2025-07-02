<?php

namespace App\Models;

use Core\Database\Database;

class User
{
    private $db;
    private $table = 'users';
    private $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();

    }

    public function findByEmail(string $email): ?array
    {
        try {
            // Log para debug
            error_log("Buscando usuário pelo email: " . $email);

            $user = $this->db->find('users', 'email = ?', [$email]);

            // Log para debug
            error_log("Resultado da busca: " . ($user ? "Usuário encontrado" : "Usuário não encontrado"));

            return $user;
        } catch (\Exception $e) {
            // Log do erro
            error_log("Erro ao buscar usuário por email: " . $e->getMessage());
            return null;
        }
    }

    public function findAll(): array
    {
        return $this->db->findAll($this->table, '*');
    }


    public function findById(int $id): ?array
    {
        return $this->db->find('users','*', 'id = ?', [$id]);
    }

    public function create(array $data): int
    {
        // Hash da senha
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        return $this->db->insert('users', $data);
    }

    public function update(int $id, array $data): int
    {
        // Se estiver atualizando a senha, faz o hash
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        return $this->db->update('users', $data, 'id = ?', [$id]);
    }

    public function delete(int $id): int
    {
        return $this->db->delete('users', 'id = ?', [$id]);
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