<?php
namespace App\Policies;

use Core\Http\Request;
use Core\Http\Response;
use Core\Interface\PolicyInterface;
use Core\Security\JWT;

class ApiPolicy implements PolicyInterface
{
    public static function authorize(): bool
    {
       return true; // ou faça a verificação do token JWT aqui, se necessário
    }
    
    public static function check(Request $request): ?Response
    {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';

        if (empty($token) || !str_starts_with($token, 'Bearer ')) {
            return new Response(json_encode(['error' => 'Token inválido']), 401, ['Content-Type' => 'application/json']);
        }

        $token = substr($token, 7);

        try {
            if (!JWT::validate($token)) {
                throw new \Exception('Token inválido');
            }

            $payload = JWT::decode($token);
            if (!isset($payload['exp']) || $payload['exp'] < time()) {
                throw new \Exception('Token expirado');
            }

            $request->setAttribute('user', $payload);
        } catch (\Exception $e) {
            return new Response(json_encode(['error' => $e->getMessage()]), 401, ['Content-Type' => 'application/json']);
        }

        return null;
    }
}
