<?php

namespace App\Middleware;

use Core\Http\Request;
use Core\Http\Response;
use Core\Interface\MiddlewareInterface;
use Core\Security\JWT;

class JWTAuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, \Closure $next): Response
    {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';

        if (empty($token)) {
            return new Response(json_encode(['error' => 'Token não fornecido']), 401, [
                'Content-Type' => 'application/json'
            ]);
        }

        // Remove "Bearer " do token
        $token = str_replace('Bearer ', '', $token);

        if (!JWT::validate($token)) {
            return new Response(json_encode(['error' => 'Token inválido']), 401, [
                'Content-Type' => 'application/json'
            ]);
        }

        // Decodifica o token e adiciona o usuário ao request
        try {
            $payload = JWT::decode($token);
            $request->setAttribute('user', $payload);
            
            // Continua o fluxo de middlewares
            return $next($request);
        } catch (\Exception $e) {
            return new Response(json_encode(['error' => $e->getMessage()]), 401, [
                'Content-Type' => 'application/json'
            ]);
        }
    }
}