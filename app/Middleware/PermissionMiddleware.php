<?php

namespace App\Middleware;

use Core\Auth\Auth;
use Core\Http\Request;
use Core\Http\Response;
use Core\Interface\MiddlewareInterface;

class PermissionMiddleware implements MiddlewareInterface
{
    protected array $requiredRoles;

    /**
     * O construtor aceita as permissões necessárias definidas na rota.
     * Ex: 'permission:admin,manager' fará com que $roles seja ['admin', 'manager'].
     */
    public function __construct(...$roles)
    {
        $this->requiredRoles = $roles;
    }

    public function handle(Request $request, \Closure $next): Response
    {
        $user = Auth::user();

        // Verifica se o usuário tem a permissão necessária.
        if (!$user || empty($user['role']) || !$this->hasPermission($user)) {
            // Se for uma requisição de API, retorna um erro JSON.
            if ($request->isAjax() || str_contains($request->getUri(), '/api/')) {
                 return Response::jsonResponse(['error' => 'Acesso não autorizado.'], 403);
            }
            
            // Para requisições web, redireciona para uma página de dashboard
            // com uma mensagem de erro, impedindo o acesso indevido.
            return Response::redirectResponse(base_url('admin/dashboard'))->with('error', 'Você não tem permissão para acessar esta área.');
        }

        // Permissão concedida, a requisição continua para o controlador.
        return $next($request);
    }

    /**
     * Verifica se a role do usuário está na lista de roles exigidas pela rota.
     */
    private function hasPermission($user): bool
    {
        return in_array($user['role'], $this->requiredRoles);
    }
}