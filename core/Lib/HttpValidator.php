<?php

namespace Core\Lib;

use Core\Lib\AlertManager;

/**
 * Sistema de Validação de Métodos HTTP para FramePhp
 * Baseado no sistema de validação do SpeedPHP
 */
class HttpValidator
{
    private AlertManager $alertManager;

    public function __construct()
    {
        $this->alertManager = new AlertManager();
    }

    /**
     * Valida se o método da requisição é o esperado
     */
    public function validateMethod(string $expectedMethod, bool $isApi = false): bool
    {
        $currentMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        if (strtoupper($currentMethod) !== strtoupper($expectedMethod)) {
            if ($isApi) {
                $this->sendApiError('Método incorreto', 405);
                return false;
            } else {
                $this->alertManager->warning('Método incorreto!', '/');
                return false;
            }
        }
        
        return true;
    }

    /**
     * Valida se é uma requisição POST
     */
    public function requirePost(bool $isApi = false): bool
    {
        return $this->validateMethod('POST', $isApi);
    }

    /**
     * Valida se é uma requisição GET
     */
    public function requireGet(bool $isApi = false): bool
    {
        return $this->validateMethod('GET', $isApi);
    }

    /**
     * Valida se é uma requisição PUT
     */
    public function requirePut(bool $isApi = false): bool
    {
        return $this->validateMethod('PUT', $isApi);
    }

    /**
     * Valida se é uma requisição DELETE
     */
    public function requireDelete(bool $isApi = false): bool
    {
        return $this->validateMethod('DELETE', $isApi);
    }

    /**
     * Valida se é uma requisição PATCH
     */
    public function requirePatch(bool $isApi = false): bool
    {
        return $this->validateMethod('PATCH', $isApi);
    }

    /**
     * Valida se é uma requisição AJAX
     */
    public function requireAjax(): bool
    {
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            $this->alertManager->warning('Requisição AJAX necessária!', '/');
            return false;
        }
        return true;
    }

    /**
     * Valida se é uma requisição de API
     */
    public function requireApi(): bool
    {
        $contentType = $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        
        if (strpos($contentType, 'application/json') === false && 
            strpos($accept, 'application/json') === false) {
            $this->sendApiError('Requisição de API necessária', 400);
            return false;
        }
        return true;
    }

    /**
     * Valida se o token CSRF é válido
     */
    public function validateCsrfToken(string $token): bool
    {
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        
        if (empty($sessionToken) || $token !== $sessionToken) {
            $this->alertManager->danger('Token CSRF inválido!', '/');
            return false;
        }
        
        return true;
    }

    /**
     * Gera um novo token CSRF
     */
    public function generateCsrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        
        return $token;
    }

    /**
     * Valida se o reCAPTCHA é válido
     */
    public function validateRecaptcha(string $recaptchaResponse, string $secretKey): bool
    {
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $secretKey,
            'response' => $recaptchaResponse,
            'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $response = json_decode($result, true);

        return $response['success'] ?? false;
    }

    /**
     * Envia erro de API em formato JSON
     */
    private function sendApiError(string $message, int $statusCode = 400): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        
        $response = [
            'error' => true,
            'message' => $message,
            'status_code' => $statusCode,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        echo json_encode($response);
        exit;
    }

    /**
     * Verifica se é uma requisição de desenvolvimento
     */
    public function isDevelopmentMode(): bool
    {
        $serverName = $_SERVER['SERVER_NAME'] ?? '';
        return strpos($serverName, 'localhost') !== false || 
               $serverName === 'localhost' ||
               $serverName === '127.0.0.1';
    }

    /**
     * Define headers de segurança
     */
    public function setSecurityHeaders(): void
    {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        if (!$this->isDevelopmentMode()) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }

    /**
     * Define headers para evitar cache
     */
    public function setNoCacheHeaders(): void
    {
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
    }
}
