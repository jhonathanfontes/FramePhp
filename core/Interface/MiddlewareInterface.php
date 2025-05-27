<?php

namespace Core\Interface;

use Core\Http\Request;
use Core\Http\Response;

interface MiddlewareInterface
{
    /**
     * Processa a requisição através do middleware
     *
     * @param Request $request A requisição HTTP
     * @param \Closure $next O próximo middleware na pilha
     * @return Response A resposta HTTP
     */
    public function handle(Request $request, \Closure $next): Response;
}