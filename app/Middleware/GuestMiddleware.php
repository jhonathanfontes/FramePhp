<?php

namespace App\Middleware;

use Core\Auth\Auth;
use Core\Http\Request;
use Core\Http\Response;
use Core\Middleware\MiddlewareInterface;

class GuestMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, \Closure $next): Response
    {
        error_log("GuestMiddleware - Auth::check(): " . (Auth::check() ? "true" : "false"));
        error_log("GuestMiddleware - Sessão: " . json_encode($_SESSION));
        
        // Verifica se o usuário está autenticado e não está já em uma rota protegida
        if (Auth::check() && !str_starts_with($request->getPath(), '/dashboard')) {
            return Response::redirectResponse(base_url('dashboard'));
        }
        
        return $next($request);
    }
}