<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PermissionMiddleware implements MiddlewareInterface
{
    private $requiredRole;

    public function __construct(string $role)
    {
        $this->requiredRole = $role;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $user = $request->getAttribute('user');
        
        if (!$user || !$this->hasPermission($user, $this->requiredRole)) {
            throw new \Exception('Acesso não autorizado');
        }

        return $handler->handle($request);
    }

    private function hasPermission($user, $role): bool
    {
        return in_array($role, $user->roles ?? []);
    }
}