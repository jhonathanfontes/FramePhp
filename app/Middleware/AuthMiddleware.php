<?php

namespace App\Middleware;

use Core\Auth\Auth;
use Core\Http\Request;
use Core\Http\Response;
use Core\Middleware\MiddlewareInterface;

class AuthMiddleware implements MiddlewareInterface
{
    /**
     * Processa a requisição e verifica se o usuário está autenticado
     *
     * @param Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle(Request $request, \Closure $next): Response
    {
        // Log para debug
        error_log("AuthMiddleware - Auth::check(): " . (Auth::check() ? "true" : "false"));
        error_log("AuthMiddleware - Sessão: " . json_encode($_SESSION));
        
        // Verifica se o usuário está autenticado
        if (!Auth::check()) {
            // Usa o método estático redirectResponse que já retorna um objeto Response
            return Response::redirectResponse(base_url('auth/login'));
        }

        // Continua o fluxo da aplicação
        $response = $next($request);
        
        // Verifica se o retorno é um objeto Response
        if (!($response instanceof Response)) {
            // Se não for, cria um novo objeto Response com o conteúdo retornado
            return new Response((string) $response);
        }
        
        return $response;
    }
}