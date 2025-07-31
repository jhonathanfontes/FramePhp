
<?php

namespace App\Middleware;

use Core\Http\Request;
use Core\Http\Response;
use Core\Interface\MiddlewareInterface;
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
