<?php

namespace App\Controllers;

use Core\Controller\BaseController;

class AuthController extends BaseController
{
    public function loginForm()
    {
        return $this->render('auth/login');
    }

    public function login()
    {
        // Lógica de autenticação aqui
        $email = $_POST['email'] ?? '';
        $senha = $_POST['password'] ?? '';

        // Aqui você implementará a lógica de autenticação
        // Por enquanto, apenas retornaremos uma resposta JSON
        return $this->json([
            'status' => 'success',
            'message' => 'Login realizado com sucesso'
        ]);
    }

    public function logout()
    {
        // Lógica de logout
        session_start();
        session_destroy();
        
        header('Location: /login');
        exit;
    }
}