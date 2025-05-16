<?php

namespace App\Middleware;

use Core\Http\Request;
use Core\Http\Response;
use Core\Middleware\MiddlewareInterface;

class CsrfMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, \Closure $next): Response
    {
        // Iniciar sessão se ainda não estiver iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Gerar token CSRF se não existir
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        // Verificar token CSRF em requisições POST, PUT, DELETE
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE'])) {
            $token = $request->input('csrf_token');
            
            if (!$token || !hash_equals($_SESSION['csrf_token'], $token)) {
                // Redirecionar com mensagem de erro
                $_SESSION['error'] = __('csrf_token_invalid');
                return new Response('', 302, ['Location' => $request->getReferer() ?: base_url()]);
            }
            
            // Regenerar token após verificação bem-sucedida para aumentar a segurança
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        // Executar o próximo middleware e garantir que retorne um objeto Response
        $response = $next($request);
        
        // Se o próximo middleware não retornou um objeto Response, criar um
        if (!$response instanceof Response) {
            $response = new Response((string) $response);
        }
        
        return $response;
    }
}