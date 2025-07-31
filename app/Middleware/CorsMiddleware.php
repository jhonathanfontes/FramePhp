
<?php

namespace App\Middleware;

use Core\Http\Request;
use Core\Http\Response;
use Core\Interface\MiddlewareInterface;

class CorsMiddleware implements MiddlewareInterface
{
    private $allowedOrigins;
    private $allowedMethods;
    private $allowedHeaders;

    public function __construct(
        array $allowedOrigins = ['*'],
        array $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        array $allowedHeaders = ['Content-Type', 'Authorization', 'X-Requested-With']
    ) {
        $this->allowedOrigins = $allowedOrigins;
        $this->allowedMethods = $allowedMethods;
        $this->allowedHeaders = $allowedHeaders;
    }

    public function handle(Request $request, \Closure $next): Response
    {
        // Handle preflight requests
        if ($request->getMethod() === 'OPTIONS') {
            return $this->createCorsResponse();
        }

        $response = $next($request);
        
        return $this->addCorsHeaders($response, $request);
    }

    private function createCorsResponse(): Response
    {
        $response = new Response('', 200);
        return $this->addCorsHeaders($response);
    }

    private function addCorsHeaders(Response $response, Request $request = null): Response
    {
        $origin = $request ? $request->getHeader('Origin') : '*';
        
        if (in_array('*', $this->allowedOrigins) || in_array($origin, $this->allowedOrigins)) {
            $response->addHeader('Access-Control-Allow-Origin', $origin ?: '*');
        }

        $response->addHeader('Access-Control-Allow-Methods', implode(', ', $this->allowedMethods));
        $response->addHeader('Access-Control-Allow-Headers', implode(', ', $this->allowedHeaders));
        $response->addHeader('Access-Control-Allow-Credentials', 'true');
        $response->addHeader('Access-Control-Max-Age', '86400');

        return $response;
    }
}
