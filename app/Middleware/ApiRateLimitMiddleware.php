
<?php

namespace App\Middleware;

use Core\Http\Request;
use Core\Http\Response;
use Core\Interface\MiddlewareInterface;

class ApiRateLimitMiddleware implements MiddlewareInterface
{
    private int $maxRequests;
    private int $timeWindow;

    public function __construct(int $maxRequests = 60, int $timeWindow = 60)
    {
        $this->maxRequests = $maxRequests;
        $this->timeWindow = $timeWindow;
    }

    public function handle(Request $request, \Closure $next): Response
    {
        $ip = $request->getClientIp();
        $key = 'rate_limit_' . md5($ip);
        
        // Iniciar sessão se necessário para armazenar dados de rate limiting
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $now = time();
        $requests = $_SESSION[$key] ?? [];
        
        // Remover requisições antigas
        $requests = array_filter($requests, function($timestamp) use ($now) {
            return ($now - $timestamp) < $this->timeWindow;
        });

        // Verificar se excedeu o limite
        if (count($requests) >= $this->maxRequests) {
            return new Response(json_encode([
                'error' => 'Rate limit exceeded',
                'retry_after' => $this->timeWindow
            ]), 429, [
                'Content-Type' => 'application/json',
                'X-RateLimit-Limit' => (string) $this->maxRequests,
                'X-RateLimit-Remaining' => '0',
                'X-RateLimit-Reset' => (string) ($now + $this->timeWindow)
            ]);
        }

        // Adicionar nova requisição
        $requests[] = $now;
        $_SESSION[$key] = $requests;

        $response = $next($request);
        
        // Garantir que temos um objeto Response
        if (!$response instanceof Response) {
            $response = new Response((string) $response);
        }

        // Adicionar headers de rate limit
        $remaining = $this->maxRequests - count($requests);
        $response->addHeaders([
            'X-RateLimit-Limit' => (string) $this->maxRequests,
            'X-RateLimit-Remaining' => (string) $remaining,
            'X-RateLimit-Reset' => (string) ($now + $this->timeWindow)
        ]);

        return $response;
    }
}
use Core\Cache\CacheManager;

class ApiRateLimitMiddleware implements MiddlewareInterface
{
    private $cache;
    private $maxRequests;
    private $timeWindow;

    public function __construct($maxRequests = 100, $timeWindow = 3600) // 100 req/hora por padrão
    {
        $this->cache = CacheManager::getInstance();
        $this->maxRequests = $maxRequests;
        $this->timeWindow = $timeWindow;
    }

    public function handle(Request $request, \Closure $next): Response
    {
        $clientIp = $request->getClientIp();
        $key = 'api_rate_limit:' . $clientIp;
        
        $requests = $this->cache->get($key, 0);
        
        if ($requests >= $this->maxRequests) {
            return Response::jsonResponse([
                'error' => 'Rate limit exceeded',
                'message' => "Maximum {$this->maxRequests} requests per hour allowed"
            ], 429);
        }

        // Incrementar contador
        $this->cache->set($key, $requests + 1, $this->timeWindow);

        // Adicionar headers de rate limit
        $response = $next($request);
        $response->addHeader('X-RateLimit-Limit', $this->maxRequests);
        $response->addHeader('X-RateLimit-Remaining', max(0, $this->maxRequests - $requests - 1));
        $response->addHeader('X-RateLimit-Reset', time() + $this->timeWindow);

        return $response;
    }
}
