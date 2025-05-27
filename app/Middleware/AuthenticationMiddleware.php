<?php

namespace App\Middleware;

use Core\Http\Request;
use Core\Http\Response;
use Core\Interface\MiddlewareInterface;
use App\Policies\WebPolicy;
use App\Policies\AdminPolicy;
use App\Policies\ApiPolicy;

class AuthenticationMiddleware implements MiddlewareInterface
{
    private $policy;

    public function __construct(string $type = 'web')
    {
   
        $this->policy = $this->resolvePolicy($type);
    }

    private function resolvePolicy(string $type): string
    {
        $policies = [
            'web' => WebPolicy::class,
            'admin' => AdminPolicy::class,
            'api' => ApiPolicy::class
        ];

        if (!isset($policies[$type])) {
            throw new \InvalidArgumentException('Invalid authentication type');
        }

        return $policies[$type];
    }

    public function handle(Request $request, \Closure $next): Response
    {
        
        $policyClass = $this->policy;
        
        // Verifica a autorização usando a policy
        $response = $policyClass::check($request);

        if ($response !== null) {
            return $response;
        }

        // Continua o fluxo se autorizado
        return $next($request);
    }
}
