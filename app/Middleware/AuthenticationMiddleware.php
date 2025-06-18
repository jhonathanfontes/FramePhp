<?php

namespace App\Middleware;

use Core\Auth\Auth;
use Core\Http\Request;
use Core\Http\Response;
use Core\Interface\MiddlewareInterface;

class AuthenticationMiddleware implements MiddlewareInterface
{
    /**
     * Lida com a requisição de autenticação.
     *
     * A única responsabilidade deste middleware é garantir que um usuário
     * esteja autenticado. Se não estiver, ele é redirecionado para a página de login.
     * A verificação de permissões (roles) é delegada para outro middleware.
     *
     * @param Request $request A requisição HTTP.
     * @param \Closure $next O próximo passo no pipeline.
     * @return Response
     */
    public function handle(Request $request, \Closure $next): Response
    {
   
        if (!Auth::check()) {
            // Se o usuário não estiver logado, redireciona para a rota de login.
            return Response::redirectResponse(base_url('auth/login'));
        }
        
        // Se o usuário está logado, permite que a requisição continue.
        return $next($request);
    }
}