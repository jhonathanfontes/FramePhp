<?php

namespace App\Middleware;

use Core\Security\JWT;

class ClientAuthMiddleware
{
    public function handle()
    {
        session_start();
        
        if (!isset($_SESSION['client_user'])) {
            header('Location: /client/login');
            exit;
        }

        $token = $_SESSION['client_token'] ?? '';
        
        if (!JWT::validate($token)) {
            session_destroy();
            header('Location: /client/login');
            exit;
        }
    }
}