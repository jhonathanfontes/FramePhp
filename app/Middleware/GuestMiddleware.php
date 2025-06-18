<?php

namespace App\Middleware;

use Core\Auth\Auth;
use Core\Http\Request;
use Core\Http\Response;
use Core\Interface\MiddlewareInterface;

class GuestMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, \Closure $next): Response
    {
        // Verifica se o usuário está autenticado
        if (Auth::check()) {
            // --- CORREÇÃO APLICADA AQUI ---
            // Redireciona para a rota correta do painel administrativo.
            // Opcionalmente, você poderia verificar a 'role' do usuário
            // e redirecionar para painéis diferentes (admin, cliente, etc.)
            
            $user = Auth::user();
            if (isset($user['role']) && $user['role'] === '1') {
                return Response::redirectResponse(base_url('admin/dashboard'));
            }

            // Um redirecionamento padrão para outros tipos de usuários logados, se houver.
            // Se só existir admin, este pode apontar para a home.
            return Response::redirectResponse(base_url('/'));
        }
        
        // Se não estiver logado, permite que a requisição continue para o controlador
        // (ex: para exibir o formulário de login).
        $response = $next($request);
        
        if (!$response instanceof Response) {
            $response = new Response((string) $response);
        }
        
        return $response;
    }
}