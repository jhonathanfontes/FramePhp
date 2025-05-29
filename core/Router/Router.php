<?php
namespace Core\Router;

use Core\Http\Request;

class Router
{
    private static $instance;
    private array $routes = [];
    private array $middlewares = [];
    private array $policies = [];
    private string $prefix = '';
    private string $currentName = '';
    private array $namedRoutes = [];
    private $fallback;

    private function __construct() {}

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function middleware(array $middlewares): static
    {
        $this->middlewares = $middlewares;
        return $this;
    }

    public function group(array $options = [], callable $callback): void
    {
        $originalMiddlewares = $this->middlewares;
        $originalPrefix = $this->prefix;
        $originalPolicies = $this->policies;

        if (isset($options['prefix'])) {
            $this->prefix .= '/' . trim($options['prefix'], '/');
        }

        $callback($this);

        $this->middlewares = $originalMiddlewares;
        $this->prefix = $originalPrefix;
        $this->policies = $originalPolicies;
    }

    public function get(string $uri, array|callable $callback): static
    {
        return $this->addRoute('GET', $uri, $callback);
    }

    public function post(string $uri, array|callable $callback): static
    {
        return $this->addRoute('POST', $uri, $callback);
    }

    public function name(string $name): static
    {
        $this->currentName = $name;
        return $this;
    }

    private function addRoute(string $method, string $uri, array|callable $callback): static
    {
        $fullUri = rtrim($this->prefix . '/' . trim($uri, '/'), '/') ?: '/';
    
        $route = [
            'uri' => $fullUri,
            'callback' => $callback,
            'middlewares' => $this->middlewares,
            'method' => $method,
        ];
    
        // Define o nome da rota: usa o nome manual ou gera automaticamente baseado na URI
        $routeName = $this->currentName ?: $this->generateDefaultNameFromUri($fullUri);
    
        // Registra a rota nomeada (com verificação de duplicidade)
        if (isset($this->namedRoutes[$routeName])) {
            error_log("Rota nomeada duplicada detectada: '{$routeName}' já está associada a '{$this->namedRoutes[$routeName]}'.", E_USER_WARNING);
        } else {
            $this->namedRoutes[$routeName] = $fullUri;
        }
    
        $this->currentName = ''; // limpa o nome temporário
    
        $this->routes[$method][] = $route;
    
        return $this;
    }
    
    private function generateDefaultNameFromUri(string $uri): string
{
    // Remove a barra inicial e substitui as demais por underlines
    $name = ltrim($uri, '/');
    $name = str_replace('/', '_', $name);

    // Se a URI for "/", nomeia como "home" por padrão
    return $name === '' ? 'home' : $name;
}


    public function dispatch(): void
    {
        $request = new Request();

        $currentUri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/') ?: '/';
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes[$method] ?? [] as $route) {
            $pattern = preg_replace('#\{[\w]+\}#', '([\w-]+)', $route['uri']);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $currentUri, $matches)) {
                array_shift($matches);

                $callback = $route['callback'];

                foreach ($route['middlewares'] as $middleware) {
                    if (class_exists($middleware)) {
                        (new $middleware())->handle($request, function ($request) use ($callback, $matches) {
                            return $this->invokeCallback($callback, $matches, $request);
                        });
                        return;
                    }
                }

                $this->invokeCallback($callback, $matches, $request);
                return;
            }
        }

        if ($this->fallback) {
            call_user_func($this->fallback);
        } else {
            http_response_code(404);
            echo "404 - Página não encontrada (Fallback não definido)";
        }
    }

    private function invokeCallback(array|callable $callback, array $matches, Request $request): mixed
    {
        if (is_array($callback)) {
            [$class, $method] = $callback;
            return call_user_func_array([new $class, $method], $matches);
        }

        return call_user_func($callback, $request, ...$matches);
    }

    public function route(string $name): ?string
    {
        return $this->namedRoutes[$name] ?? null;
    }

    public function setFallback(callable $callback): void
    {
        $this->fallback = $callback;
    }

    public function generateUrl(string $name, ?array $params = []): ?string
    {
        if (!array_key_exists($name, $this->namedRoutes)) {
            return null; // Não lança exceção diretamente, retorna null se preferir
        }

        $url = $this->namedRoutes[$name];

        // Substitui parâmetros na URL
        foreach ($params as $key => $value) {
            $placeholder = '{' . $key . '}';
            if (strpos($url, $placeholder) !== false) {
                $url = str_replace($placeholder, urlencode($value), $url);
                unset($params[$key]);
            }
        }

        // Adiciona query string se houver
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }
    
}
