<?php

namespace App\Middleware;

use Core\Http\Request;
use Core\Http\Response;
use Core\Interface\MiddlewareInterface;
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
        // Verifica o parâmetro 'lang' na URL para definir o idioma
        $locale = $request->getQueryParam('lang');
         // Adicionar definição de locales suportados
         $supportedLocales = ['en', 'pt_BR'];
        
        if ($locale && in_array($locale, $supportedLocales)) {
            Translator::getInstance()->setLocale($locale);
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