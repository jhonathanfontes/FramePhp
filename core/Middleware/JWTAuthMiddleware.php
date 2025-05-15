<?php

namespace Core\Middleware;

use Core\Security\JWT;

class JWTAuthMiddleware
{
    public function handle()
    {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';

        if (empty($token)) {
            http_response_code(401);
            echo json_encode(['error' => 'Token não fornecido']);
            exit;
        }

        // Remove "Bearer " do token
        $token = str_replace('Bearer ', '', $token);

        if (!JWT::validate($token)) {
            http_response_code(401);
            echo json_encode(['error' => 'Token inválido']);
            exit;
        }

        // Decodifica o token e adiciona o usuário ao request
        try {
            $payload = JWT::decode($token);
            $_REQUEST['user'] = $payload;
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }
}