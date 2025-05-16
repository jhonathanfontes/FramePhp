<?php

namespace App\Middleware;

use Core\Http\Request;
use Core\Http\Response;
use Core\Middleware\MiddlewareInterface;
use Core\Translation\Translator;

class LocaleMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, \Closure $next): Response
    {
        // Iniciar sessão se ainda não estiver iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar se o idioma está definido na URL
        $locale = $request->getQueryParam('lang');
        
        if ($locale) {
            // Verificar se o idioma é suportado
            $supportedLocales = ['pt_BR', 'en']; // Valor fixo para evitar problemas com config()
            
            if (in_array($locale, $supportedLocales)) {
                Translator::getInstance()->setLocale($locale);
            }
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