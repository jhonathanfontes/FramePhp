<?php

// Initialize Router

use Core\Container\Container;

$router = new \Core\Router\Router();

$config = require BASE_PATH .  '/config/routes.php';

$container = Container::getInstance();

// Registrar middlewares a partir do config
foreach ($config['middleware']['auth'] as $key => $data) {
    $container->bindMiddleware("auth.$key", function() use ($data) {
        return new $data['class']($data['guard'], $data['redirect']);
    });

}

$container->bindMiddleware('csrf', function() use ($config) {
    return new $config['middleware']['csrf']();
});
$container->bindMiddleware('locale', function() use ($config) {
    return new $config['middleware']['locale']();
});
$container->bindMiddleware('guest', function() use ($config) {
    return new $config['middleware']['guest']();
});
$container->bindMiddleware('permission.admin', function() use ($config) {
    return new $config['middleware']['permission']['admin']('admin');
});

// Bind Middlewares dynamically
$middlewareConfig = config('middleware', []);

foreach ($middlewareConfig as $key => $middleware) {
    // Handle middlewares that might require constructor arguments or specific setup
    // This part might need adjustment based on how your specific middlewares are structured
    // The provided example handles nested arrays, which might be for different guards/redirects
    // Let's adapt the provided logic:
    if (is_array($middleware)) {
        foreach ($middleware as $subKey => $data) {
            $name = "{$key}.{$subKey}";
            $container->bindMiddleware($name, function () use ($data) {
                // Assuming 'class' is the middleware class name
                // You might need to pass constructor arguments here based on your middleware needs
                // Example: new $data['class']($data['param1'] ?? null, ...);
                return new $data['class'](); // Adjust constructor arguments as needed
            });
        }
    } else {
        // Simple middleware without complex constructor args
        $container->bindMiddleware($key, function () use ($middleware) {
            return new $middleware();
        });
    }
}

// Load routes
require_once BASE_PATH . '/routes/web.php';

// Dispatch route
$router->dispatch();

// Registrar manipulador de erros
\Core\Error\ErrorHandler::register();

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inicializar o Translator
\Core\Translation\Translator::getInstance();