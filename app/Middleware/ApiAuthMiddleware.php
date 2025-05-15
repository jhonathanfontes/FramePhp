<?php

namespace App\Middleware;

use Core\Security\JWT;

class ApiAuthMiddleware
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

        $token = str_replace('Bearer ', '', $token);

        if (!JWT::validate($token)) {
            http_response_code(401);
            echo json_encode(['error' => 'Token inválido']);
            exit;
        }
    }
}