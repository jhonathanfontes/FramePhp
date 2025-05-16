<?php

namespace App\Middleware;

use Core\Auth\Auth;
use Core\Security\JWT;
use Core\Http\Request;
use Core\Http\Response;
use Core\Middleware\MiddlewareInterface;

class AuthenticationMiddleware implements MiddlewareInterface
{
    private $type;
    private $redirectUrl;

    /**
     * Construtor que define o tipo de autenticação
     * 
     * @param string $type Tipo de autenticação: 'web', 'admin', 'client', 'api'
     * @param string $redirectUrl URL de redirecionamento em caso de falha (não usado para API)
     */
    public function __construct(string $type = 'web', string $redirectUrl = null)
    {
        $this->type = $type;
        $this->redirectUrl = ($redirectUrl) ? $redirectUrl : base_url('auth/login');
    }

    /**
     * Processa a requisição e verifica a autenticação conforme o tipo
     *
     * @param Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle(Request $request, \Closure $next): Response
    {
        switch ($this->type) {
            case 'web':
                return $this->handleWebAuth($request, $next);
            case 'admin':
                return $this->handleAdminAuth($request, $next);
            case 'client':
                return $this->handleClientAuth($request, $next);
            case 'api':
                return $this->handleApiAuth($request, $next);
            default:
                return $this->handleWebAuth($request, $next);
        }
    }

    /**
     * Manipula autenticação web padrão
     */
    private function handleWebAuth(Request $request, \Closure $next): Response
    {
        if (!Auth::check()) {
            return Response::redirectResponse($this->redirectUrl);
        }

        $response = $next($request);

        if (!($response instanceof Response)) {
            return new Response((string) $response);
        }

        return $response;
    }

    /**
     * Manipula autenticação de admin
     */
    private function handleAdminAuth(Request $request, \Closure $next): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['admin_user'])) {
            return Response::redirectResponse($this->redirectUrl);
        }

        $token = $_SESSION['admin_token'] ?? '';

        if (!JWT::validate($token)) {
            session_destroy();
            return Response::redirectResponse($this->redirectUrl);
        }

        $response = $next($request);

        if (!($response instanceof Response)) {
            return new Response((string) $response);
        }

        return $response;
    }

    /**
     * Manipula autenticação de cliente
     */
    private function handleClientAuth(Request $request, \Closure $next): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['client_user'])) {
            return Response::redirectResponse($this->redirectUrl);
        }

        $token = $_SESSION['client_token'] ?? '';

        if (!JWT::validate($token)) {
            session_destroy();
            return Response::redirectResponse($this->redirectUrl);
        }

        $response = $next($request);

        if (!($response instanceof Response)) {
            return new Response((string) $response);
        }

        return $response;
    }

    /**
     * Manipula autenticação de API
     */
    private function handleApiAuth(Request $request, \Closure $next): Response
    {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';

        if (empty($token)) {
            return new Response(
                json_encode(['error' => 'Token não fornecido']),
                401,
                ['Content-Type' => 'application/json']
            );
        }

        $token = str_replace('Bearer ', '', $token);

        if (!JWT::validate($token)) {
            return new Response(
                json_encode(['error' => 'Token inválido']),
                401,
                ['Content-Type' => 'application/json']
            );
        }

        try {
            $payload = JWT::decode($token);
            $request->setAttribute('user', $payload);
        } catch (\Exception $e) {
            return new Response(
                json_encode(['error' => $e->getMessage()]),
                401,
                ['Content-Type' => 'application/json']
            );
        }

        return $next($request);
    }
}
