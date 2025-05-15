<?php

namespace App\Middleware;

use Core\Security\JWT;

class AdminAuthMiddleware
{
    public function handle()
    {
        session_start();
        
        if (!isset($_SESSION['admin_user'])) {
            header('Location: /admin/login');
            exit;
        }

        $token = $_SESSION['admin_token'] ?? '';
        
        if (!JWT::validate($token)) {
            session_destroy();
            header('Location: /admin/login');
            exit;
        }
    }
}