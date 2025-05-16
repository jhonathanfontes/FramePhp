<?php

namespace App\Middleware;

use Core\Security\JWT;
use Core\Http\Request;
use Core\Http\Response;
use Core\Middleware\MiddlewareInterface;

class ClientAuthMiddleware implements MiddlewareInterface
{
    /**
     * Processa a requisição e verifica se o usuário cliente está autenticado
     *
     * @param Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle(Request $request, \Closure $next): Response
    {
        // Iniciar sessão se ainda não estiver iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['client_user'])) {
            return Response::redirectResponse('/client/login');
        }

        $token = $_SESSION['client_token'] ?? '';
        
        if (!JWT::validate($token)) {
            session_destroy();
            return Response::redirectResponse('/client/login');
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