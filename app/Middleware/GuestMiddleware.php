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
            // Se estiver autenticado, redireciona para o dashboard
            return Response::redirect(base_url('dashboard'));
        }
        
        // Executa o próximo middleware apenas se for um visitante
        $response = $next($request);
        
        // Se o próximo middleware não retornou um Response, cria um novo
        if (!$response instanceof Response) {
            $response = new Response((string) $response);
        }
        
        return $response;
    }
}