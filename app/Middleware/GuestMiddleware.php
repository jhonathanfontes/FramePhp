<?php

namespace App\Middleware;

use Core\Auth\Auth;
use Core\Http\Request;
use Core\Http\Response;
use Core\Middleware\MiddlewareInterface;

class GuestMiddleware implements MiddlewareInterface
{
    /**
     * Processa a requisição e verifica se o usuário NÃO está autenticado
     *
     * @param Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle(Request $request, \Closure $next): Response
    {
              
        // Se o usuário estiver autenticado, redireciona para o dashboard
        if (Auth::check()) {
            return Response::redirectResponse(base_url('dashboard'));
        }
        
        // Se não estiver autenticado, continua normalmente
        $response = $next($request);
        
        // Verifica se o retorno é um objeto Response
        if (!($response instanceof Response)) {
            // Se não for, cria um novo objeto Response com o conteúdo retornado
            return new Response((string) $response);
        }
        
        return $response;
    }
}