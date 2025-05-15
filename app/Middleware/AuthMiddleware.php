<?php

namespace App\Middleware;

class AuthMiddleware
{
    public function handle()
    {
        session_start();

        if (!isset($_SESSION['user'])) {
            header('Location: ' . URL_BASE . '/login');
            exit;
        }

        // Verifica se o usuário tem permissão de admin
        if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: ' . URL_BASE . '/unauthorized');
            exit;
        }

        return true;
    }
}