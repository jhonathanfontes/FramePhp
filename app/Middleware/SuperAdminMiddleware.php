<?php

namespace App\Middleware;

use Core\Auth\Auth;
use Core\Http\Request;
use Core\Http\Response;
use Core\Interface\MiddlewareInterface;

class SuperAdminMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, \Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            return Response::redirectResponse(base_url('painel/login'));
        }
        
        return $next($request);
    }
}