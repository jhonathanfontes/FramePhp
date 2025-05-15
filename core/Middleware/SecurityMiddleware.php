<?php

namespace Core\Middleware;

class SecurityMiddleware
{
    public function handle()
    {
        // Headers de segurança
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        header('Content-Security-Policy: default-src \'self\'');
        
        // Prevenir exposição da versão do PHP
        header_remove('X-Powered-By');
        
        // Verificar CSRF para requisições POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $security = \Core\Security\Security::getInstance();
            
            if (!isset($_POST['csrf_token']) || 
                !$security->validateToken($_POST['csrf_token'])) {
                http_response_code(403);
                die('Token CSRF inválido');
            }
        }
    }
}