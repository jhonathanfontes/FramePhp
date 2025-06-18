<?php

namespace App\Policies;

use Core\Auth\Auth;
// Supondo que você tenha um modelo App\Models\User ou similar
// Se não, pode usar um array ou stdClass para representar o usuário do banco.
use App\Models\CadUsuarioModel; 

class UserPolicy
{
    /**
     * Determina se o usuário autenticado pode atualizar um perfil de usuário.
     *
     * @param array $currentUser O usuário da sessão (de Auth::user())
     * @param array $targetUser O usuário que está tentando ser atualizado
     * @return bool
     */
    public function update(array $currentUser, array $targetUser): bool
    {
        // Um usuário pode atualizar seu próprio perfil, ou um admin pode atualizar qualquer perfil.
        // Assumindo que o ID do usuário está em 'id' e o role em 'role'.
        // Ajuste as chaves conforme a estrutura do seu array de usuário.
        if (empty($currentUser) || empty($targetUser)) {
            return false;
        }

        $isAdmin = isset($currentUser['role']) && $currentUser['role'] === 'admin';
        $isSelf = isset($currentUser['id']) && isset($targetUser['id_usuario']) && $currentUser['id'] === $targetUser['id_usuario'];

        return $isSelf || $isAdmin;
    }

    /**
     * Determina se o usuário autenticado pode deletar um perfil de usuário.
     *
     * @param array $currentUser
     * @param array $targetUser
     * @return bool
     */
    public function delete(array $currentUser, array $targetUser): bool
    {
        // Apenas um admin pode deletar um usuário, e não pode deletar a si mesmo.
        if (empty($currentUser) || empty($targetUser)) {
            return false;
        }

        $isAdmin = isset($currentUser['role']) && $currentUser['role'] === 'admin';
        $isSelf = isset($currentUser['id']) && isset($targetUser['id_usuario']) && $currentUser['id'] === $targetUser['id_usuario'];
        
        return $isAdmin && !$isSelf;
    }
}