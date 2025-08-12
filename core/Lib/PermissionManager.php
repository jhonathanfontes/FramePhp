<?php

namespace Core\Lib;

use Core\Lib\AlertManager;

/**
 * Sistema de Gerenciamento de Permissões para FramePhp
 * Baseado no sistema de permissões do SpeedPHP
 */
class PermissionManager
{
    private array $permissions = [];
    private array $userRoles = [];
    private AlertManager $alertManager;

    public function __construct()
    {
        $this->alertManager = new AlertManager();
    }

    /**
     * Define as permissões do usuário atual
     */
    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
    }

    /**
     * Define os papéis do usuário
     */
    public function setUserRoles(array $roles): void
    {
        $this->userRoles = $roles;
    }

    /**
     * Verifica se o usuário tem uma permissão específica
     */
    public function hasPermission(string $permission): bool
    {
        return isset($this->permissions[$permission]) && $this->permissions[$permission] == 1;
    }

    /**
     * Verifica se o usuário tem pelo menos uma das permissões especificadas
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica se o usuário tem todas as permissões especificadas
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Verifica se o usuário tem um papel específico
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->userRoles);
    }

    /**
     * Verifica se o usuário tem pelo menos um dos papéis especificados
     */
    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica permissão e redireciona se não tiver acesso
     */
    public function requirePermission(string $permission, string $redirect = '/access-denied'): bool
    {
        if (!$this->hasPermission($permission)) {
            $this->alertManager->danger('Sem privilégio de acesso!', $redirect);
            return false;
        }
        return true;
    }

    /**
     * Verifica papel e redireciona se não tiver acesso
     */
    public function requireRole(string $role, string $redirect = '/access-denied'): bool
    {
        if (!$this->hasRole($role)) {
            $this->alertManager->danger('Sem privilégio de acesso!', $redirect);
            return false;
        }
        return true;
    }

    /**
     * Verifica múltiplas permissões e redireciona se não tiver acesso
     */
    public function requireAnyPermission(array $permissions, string $redirect = '/access-denied'): bool
    {
        if (!$this->hasAnyPermission($permissions)) {
            $this->alertManager->danger('Sem privilégio de acesso!', $redirect);
            return false;
        }
        return true;
    }

    /**
     * Verifica múltiplos papéis e redireciona se não tiver acesso
     */
    public function requireAnyRole(array $roles, string $redirect = '/access-denied'): bool
    {
        if (!$this->hasAnyRole($roles)) {
            $this->alertManager->danger('Sem privilégio de acesso!', $redirect);
            return false;
        }
        return true;
    }

    /**
     * Retorna todas as permissões do usuário
     */
    public function getAllPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * Retorna todos os papéis do usuário
     */
    public function getAllRoles(): array
    {
        return $this->userRoles;
    }

    /**
     * Verifica se o usuário é administrador
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || $this->hasRole('administrator');
    }

    /**
     * Verifica se o usuário é moderador
     */
    public function isModerator(): bool
    {
        return $this->hasRole('moderator') || $this->hasRole('mod');
    }

    /**
     * Verifica se o usuário é usuário comum
     */
    public function isUser(): bool
    {
        return $this->hasRole('user') || $this->hasRole('member');
    }

    /**
     * Verifica se o usuário é convidado
     */
    public function isGuest(): bool
    {
        return empty($this->userRoles);
    }
}
