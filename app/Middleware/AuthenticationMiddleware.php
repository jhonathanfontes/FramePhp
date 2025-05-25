<?php

namespace App\Middleware;

use Core\Auth\Auth;
use Core\Security\JWT;
use Core\Http\Request;
use Core\Http\Response;
use Core\Middleware\MiddlewareInterface;
use Core\Cache\CacheManager;

class AuthenticationMiddleware implements MiddlewareInterface
{
    private $type;
    private $redirectUrl;
    private const ALLOWED_TYPES = ['web', 'admin', 'client', 'api'];
    private const MAX_TOKEN_AGE = 3600; // 1 hora em segundos
    private const RATE_LIMIT_WEB = 1000; // requisições por hora para web
    private const RATE_LIMIT_API = 100;  // requisições por hora para API

    public function __construct(string $type = 'web', string $redirectUrl = null)
    {
        if (!in_array($type, self::ALLOWED_TYPES, true)) {
            throw new \InvalidArgumentException('Invalid authentication type');
        }
        $this->type = $type;
        $this->redirectUrl = ($redirectUrl) ? $redirectUrl : base_url('auth/login');
    }

    public function handle(Request $request, \Closure $next): Response
    {
        // Prevent session fixation para autenticação web
        if ($this->type !== 'api' && session_status() === PHP_SESSION_NONE) {
            session_start();
            if (!isset($_SESSION['_created_at'])) {
                $_SESSION['_created_at'] = time();
                session_regenerate_id(true);
            }
        }

        // Session timeout check (4 horas) para autenticação web
        if ($this->type !== 'api' && isset($_SESSION['_created_at']) && time() - $_SESSION['_created_at'] > 14400) {
            session_destroy();
            return Response::redirectResponse($this->redirectUrl);
        }

        // Rate limiting
        if (!$this->checkRateLimit($request)) {
            return new Response(
                json_encode(['error' => 'Too many requests']),
                429,
                ['Content-Type' => 'application/json']
            );
        }

        error_log("AuthenticationMiddleware - Tipo: " . $this->type);
        error_log("AuthenticationMiddleware - Auth::check(): " . (Auth::check() ? "true" : "false"));
        
        // Usar switch com default que retorna erro 500
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
                // Retornar erro 500 em vez de assumir web como padrão
                return new Response(
                    json_encode(['error' => 'Internal Server Error: Invalid authentication type']),
                    500,
                    ['Content-Type' => 'application/json']
                );
        }
    }

    private function checkRateLimit(Request $request): bool
    {
        $ip = $request->getClientIp();
        $identifier = $this->type === 'api' ? $this->getApiIdentifier($request) : $ip;
        $key = "rate_limit_{$this->type}_" . md5($identifier);
        $cache = CacheManager::getInstance();
        
        $current = $cache->get($key, ['count' => 0, 'timestamp' => time()]);
        
        // Resetar contador se passou 1 hora
        if (time() - $current['timestamp'] > 3600) {
            $current = ['count' => 0, 'timestamp' => time()];
        }
        
        // Verificar limite
        $limit = $this->type === 'api' ? self::RATE_LIMIT_API : self::RATE_LIMIT_WEB;
        if ($current['count'] >= $limit) {
            return false;
        }
        
        // Incrementar contador
        $current['count']++;
        $cache->set($key, $current, 3600); // TTL de 1 hora
        
        return true;
    }
    
    private function getApiIdentifier(Request $request): string
    {
        // Tentar obter token JWT para identificação
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';
        
        if (!empty($token) && str_starts_with($token, 'Bearer ')) {
            $token = substr($token, 7);
            try {
                $payload = JWT::decode($token);
                if (isset($payload['sub'])) {
                    return $payload['sub']; // Usar ID do usuário como identificador
                }
            } catch (\Exception $e) {
                // Falha ao decodificar, usar IP
            }
        }
        
        // Fallback para IP
        return $request->getClientIp();
    }

    /**
     * Método genérico para autenticação baseada em sessão
     */
    private function handleSessionAuth(Request $request, \Closure $next, string $type): Response
    {
        if (!Auth::check() || Auth::user()->type !== $type) {
            error_log("AuthenticationMiddleware - Usuário não autenticado ou tipo inválido, redirecionando para: " . $this->redirectUrl);
            return Response::redirectResponse($this->redirectUrl);
        }

        error_log("AuthenticationMiddleware - Usuário autenticado, continuando...");
        $response = $next($request);
        
        // Se o resultado for uma string, converte para Response
        if (is_string($response)) {
            return new Response($response);
        }
        
        return $response;
    }

    private function handleWebAuth(Request $request, \Closure $next): Response
    {
        return $this->handleSessionAuth($request, $next, 'web');
    }

    private function handleAdminAuth(Request $request, \Closure $next): Response
    {
        return $this->handleSessionAuth($request, $next, 'admin');
    }

    private function handleClientAuth(Request $request, \Closure $next): Response
    {
        return $this->handleSessionAuth($request, $next, 'client');
    }

    private function handleApiAuth(Request $request, \Closure $next): Response
    {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';

        if (empty($token) || !str_starts_with($token, 'Bearer ')) {
            return new Response(
                json_encode(['error' => 'Token inválido ou não fornecido']),
                401,
                ['Content-Type' => 'application/json']
            );
        }

        $token = substr($token, 7); // Remove 'Bearer ' safely

        try {
            // Usar método centralizado para validação de token
            $payload = $this->validateJwtToken($token);
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
    
    /**
     * Método centralizado para validação de token JWT
     */
    private function validateJwtToken(string $token): array
    {
        if (!JWT::validate($token)) {
            throw new \Exception('Token inválido');
        }

        $payload = JWT::decode($token);
        
        // Check token expiration
        if (!isset($payload['exp']) || $payload['exp'] < time()) {
            throw new \Exception('Token expirado');
        }

        // Validate token claims
        if (!isset($payload['sub']) || !isset($payload['type']) || $payload['type'] !== $this->type) {
            throw new \Exception('Token inválido ou não autorizado para este recurso');
        }

        return $payload;
    }
}
