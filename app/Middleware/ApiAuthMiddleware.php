<?php

namespace App\Middleware;

use Core\Security\JWT;
use Core\Http\Request;
use Core\Http\Response;
use Core\Middleware\MiddlewareInterface;

class ApiAuthMiddleware implements MiddlewareInterface
{
    /**
     * Processa a requisição e verifica se a API está autenticada
     *
     * @param Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle(Request $request, \Closure $next): Response
    {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';

        if (empty($token)) {
            return new Response(
                json_encode(['error' => 'Token não fornecido']),
                401,
                ['Content-Type' => 'application/json']
            );
        }

        $token = str_replace('Bearer ', '', $token);

        if (!JWT::validate($token)) {
            return new Response(
                json_encode(['error' => 'Token inválido']),
                401,
                ['Content-Type' => 'application/json']
            );
        }
        
        // Decodifica o token e adiciona o usuário ao request
        try {
            $payload = JWT::decode($token);
            $request->setAttribute('user', $payload);
        } catch (\Exception $e) {
            return new Response(
                json_encode(['error' => $e->getMessage()]),
                401,
                ['Content-Type' => 'application/json']
            );
        }
        
        // Continua o fluxo da aplicação
        return $next($request);
    }
}