<?php

namespace App\Controllers\Site\Auth;

use Core\Controller\BaseController;
use Core\Security\JWT;

class AuthController extends BaseController
{
    public function login()
    {
        $email = $_POST['email'] ?? '';
        $senha = $_POST['password'] ?? '';

        // Aqui você faria a validação do usuário no banco de dados
        // Este é apenas um exemplo
        if ($email === 'user@example.com' && $senha === 'senha123') {
            $payload = [
                'user_id' => 1,
                'email' => $email,
                'exp' => time() + (60 * 60) // Token expira em 1 hora
            ];

            $token = JWT::encode($payload);

            return $this->json([
                'status' => 'success',
                'token' => $token
            ]);
        }

        return $this->json([
            'status' => 'error',
            'message' => 'Credenciais inválidas'
        ], 401);
    }

    public function me()
    {
        // Esta rota deve estar protegida pelo JWTAuthMiddleware
        $user = $_REQUEST['user'];
        return $this->json($user);
    }
}