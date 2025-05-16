<?php

namespace App\Middleware;

use Core\Http\Request;
use Core\Http\Response;
use Core\Middleware\MiddlewareInterface;

class PermissionMiddleware implements MiddlewareInterface
{
    private $requiredRole;

    public function __construct(string $role)
    {
        $this->requiredRole = $role;
    }

    /**
     * Processa a requisição e verifica se o usuário tem a permissão necessária
     *
     * @param Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle(Request $request, \Closure $next): Response
    {
        $user = $request->getAttribute('user');
        
        if (!$user || !$this->hasPermission($user, $this->requiredRole)) {
            return new Response(
                json_encode(['error' => 'Acesso não autorizado']),
                403,
                ['Content-Type' => 'application/json']
            );
        }

        return $next($request);
    }

    private function hasPermission($user, $role): bool
    {
        return in_array($role, $user->roles ?? []);
    }
}