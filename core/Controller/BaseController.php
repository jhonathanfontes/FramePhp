<?php

namespace Core\Controller;

use Core\View\TwigManager;
use Core\Http\Response;
use Core\Controller;
use Core\Lib\AlertManager;
use Core\Lib\PermissionManager;
use Core\Lib\HttpValidator;

class BaseController extends Controller
{
    protected AlertManager $alertManager;
    protected PermissionManager $permissionManager;
    protected HttpValidator $httpValidator;

    public function __construct()
    {
        $this->alertManager = new AlertManager();
        $this->permissionManager = new PermissionManager();
        $this->httpValidator = new HttpValidator();
        
        // Define headers de segurança por padrão
        $this->httpValidator->setSecurityHeaders();
    }

    protected function isLogged(): bool
    {
        // Implemente sua lógica de verificação de login aqui
        // Ex: return isset($_SESSION['user_id']);
        return true; // Apenas para exemplo
    }

    protected function getServico(): array
    {
        // Implemente a lógica para obter dados de serviço (permissões, URLs, etc.)
        return [
            "URL" => "/admin/modulos", // URL base de exemplo
            "ALTERAR" => "1" // Permissão de alteração de exemplo
        ];
    }

    /**
     * Retorna um parâmetro GET ou POST, ou um valor padrão.
     * @param string $key Chave do parâmetro.
     * @param mixed $default Valor padrão se a chave não for encontrada.
     * @return mixed
     */
    protected function getParams(string $key, $default = null)
    {
        // Prioriza POST, depois GET
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        return $default;
    }

    /**
     * Retorna parâmetros POST
     */
    protected function postParams(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Retorna parâmetros GET
     */
    protected function getGetParams(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Retorna arquivos enviados
     */
    protected function filesParams(string $key): array
    {
        return $_FILES[$key] ?? [];
    }

    /**
     * Retorna parâmetros JSON da requisição
     */
    protected function jsonParams(): array
    {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }

    /**
     * Retorna a URL atual
     */
    protected function getCurrentUrl(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }

    /**
     * Renderiza uma view com dados
     */
    protected function render(string $view, array $data = []): string
    {
        $twig = TwigManager::getInstance();
        
        // Adiciona alertas automaticamente
        $alert = $this->alertManager->checkAlert();
        if ($alert) {
            $data['alert'] = $alert;
        }
        
        return $twig->render($view, $data);
    }

    /**
     * Redireciona para uma URL
     */
    protected function redirect($url)
    {
        header("Location: $url");
        exit;
    }

    /**
     * Redireciona com mensagem de sucesso
     */
    protected function redirectSuccess(string $url, string $message): void
    {
        $this->alertManager->success($message, $url);
    }

    /**
     * Redireciona com mensagem de erro
     */
    protected function redirectError(string $url, string $message): void
    {
        $this->alertManager->danger($message, $url);
    }

    /**
     * Redireciona com mensagem de atenção
     */
    protected function redirectWarning(string $url, string $message): void
    {
        $this->alertManager->warning($message, $url);
    }

    /**
     * Redireciona com mensagem informativa
     */
    protected function redirectInfo(string $url, string $message): void
    {
        $this->alertManager->info($message, $url);
    }

    /**
     * Renderiza conteúdo PDF
     */
    protected function renderPdfContent($template, $data = [])
    {
        $twig = TwigManager::getInstance();
        return $twig->render($template . '.twig', $data);
    }

    /**
     * Valida método HTTP
     */
    protected function validateHttpMethod(string $method, bool $isApi = false): bool
    {
        return $this->httpValidator->validateMethod($method, $isApi);
    }

    /**
     * Requer método POST
     */
    protected function requirePost(bool $isApi = false): bool
    {
        return $this->httpValidator->requirePost($isApi);
    }

    /**
     * Requer método GET
     */
    protected function requireGet(bool $isApi = false): bool
    {
        return $this->httpValidator->requireGet($isApi);
    }

    /**
     * Requer requisição AJAX
     */
    protected function requireAjax(): bool
    {
        return $this->httpValidator->requireAjax();
    }

    /**
     * Verifica permissão
     */
    protected function checkPermission(string $permission): bool
    {
        return $this->permissionManager->hasPermission($permission);
    }

    /**
     * Requer permissão específica
     */
    protected function requirePermission(string $permission, string $redirect = '/access-denied'): bool
    {
        return $this->permissionManager->requirePermission($permission, $redirect);
    }

    /**
     * Verifica papel do usuário
     */
    protected function checkRole(string $role): bool
    {
        return $this->permissionManager->hasRole($role);
    }

    /**
     * Requer papel específico
     */
    protected function requireRole(string $role, string $redirect = '/access-denied'): bool
    {
        return $this->permissionManager->requireRole($role, $redirect);
    }

    /**
     * Debug de valores (apenas em desenvolvimento)
     */
    protected function debug($value): void
    {
        if ($this->httpValidator->isDevelopmentMode()) {
            dump($value);
            exit;
        }
    }

    /**
     * Define headers para evitar cache
     */
    protected function noCache(): void
    {
        $this->httpValidator->setNoCacheHeaders();
    }

    /**
     * Gera token CSRF
     */
    protected function generateCsrfToken(): string
    {
        return $this->httpValidator->generateCsrfToken();
    }

    /**
     * Valida token CSRF
     */
    protected function validateCsrfToken(string $token): bool
    {
        return $this->httpValidator->validateCsrfToken($token);
    }

    /**
     * Retorna resposta JSON
     */
    protected function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Retorna resposta de sucesso
     */
    protected function jsonSuccess(string $message, array $data = [], int $statusCode = 200): void
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
        $this->jsonResponse($response, $statusCode);
    }

    /**
     * Retorna resposta de erro
     */
    protected function jsonError(string $message, array $data = [], int $statusCode = 400): void
    {
        $response = [
            'success' => false,
            'message' => $message,
            'data' => $data
        ];
        $this->jsonResponse($response, $statusCode);
    }
}