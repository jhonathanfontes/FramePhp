<?php

namespace App\Middleware;

use Core\Http\Request;
use Core\Http\Response;
use Core\Auth\Auth;

class AuthMiddleware
{
    public function handle(Request $request, callable $next, string $guard = 'loja'): Response
    {
        if (!Auth::check($guard)) {
            $redirectUrl = match($guard) {
                'admin' => '/admin/login',
                'painel' => '/painel/login',
                default => '/loja/login'
            };

            return Response::redirect($redirectUrl)
                ->with('error', 'Acesso negado. Faça login para continuar.');
        }

        $usuario = Auth::user($guard);
        
        // Verificar se o usuário está ativo
        if (!$usuario->isAtivo()) {
            Auth::logout($guard);
            return Response::redirect('/login')
                ->with('error', 'Sua conta foi desativada.');
        }

        // Verificar se a empresa está ativa (para admin_empresa)
        if ($guard === 'admin' && $usuario->isAdminEmpresa()) {
            if (!$usuario->empresa || !$usuario->empresa->ativo) {
                Auth::logout($guard);
                return Response::redirect('/admin/login')
                    ->with('error', 'Sua empresa foi desativada.');
            }
        }

        return $next($request);
    }
} 