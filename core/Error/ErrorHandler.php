<?php

namespace Core\Error;

use Core\View\TwigManager;
use PDOException;

class ErrorHandler
{
    private static $errorLogFile;
    private static $instance = null;

    private function __construct()
    {
        self::$errorLogFile = BASE_PATH . '/storage/logs/errors.json';
        $this->ensureLogDirectoryExists();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function ensureLogDirectoryExists()
    {
        $logDir = dirname(self::$errorLogFile);
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
    }

    public static function register()
    {
        $instance = self::getInstance();
        set_error_handler([$instance, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([$instance, 'handleShutdown']);
    }

    public function handleError($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno)) {
            return false;
        }

        $error = [
            'type' => $this->getErrorType($errno),
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'timestamp' => date('Y-m-d H:i:s'),
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
        ];

        $this->logError($error);
        $this->renderErrorPage($error);

        return true;
    }

    public static function handleException($exception)
    {
        $instance = self::getInstance();
        
        $error = [
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'timestamp' => date('Y-m-d H:i:s'),
            'trace' => $exception->getTrace()
        ];

        // Tratamento específico para erros de banco de dados
        if ($exception instanceof PDOException) {
            $error['type'] = 'DatabaseError';
            $error['message'] = self::formatDatabaseErrorMessage($exception->getMessage());
            $error['sqlState'] = $exception->getCode();
        }

        $instance->logError($error);
        $instance->renderErrorPage($error);
    }

    private static function formatDatabaseErrorMessage($message)
    {
        // Mensagens amigáveis para erros comuns do banco de dados
        $errorMessages = [
            'Table.*doesn\'t exist' => 'A tabela necessária não existe no banco de dados. Por favor, execute as migrações do banco de dados.',
            'Unknown column' => 'Uma coluna necessária não existe na tabela.',
            'Duplicate entry' => 'Já existe um registro com este valor.',
            'Cannot add or update a child row' => 'Não é possível adicionar ou atualizar este registro devido a restrições de chave estrangeira.',
            'Access denied' => 'Acesso negado ao banco de dados. Verifique as credenciais de conexão.'
        ];

        foreach ($errorMessages as $pattern => $friendlyMessage) {
            if (preg_match('/' . $pattern . '/i', $message)) {
                return $friendlyMessage;
            }
        }

        return 'Ocorreu um erro no banco de dados. Por favor, tente novamente mais tarde.';
    }

    public function handleShutdown()
    {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $this->handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    private function logError($error)
    {
        $errors = [];
        if (file_exists(self::$errorLogFile)) {
            $content = file_get_contents(self::$errorLogFile);
            if (!empty($content)) {
                $errors = json_decode($content, true) ?? [];
            }
        }

        $errors[] = $error;
        file_put_contents(self::$errorLogFile, json_encode($errors, JSON_PRETTY_PRINT));
    }

    public function renderErrorPage($error)
    {
        try {
            $twig = TwigManager::getInstance();
            
            // Determinar o template e código do erro
            $template = 'errors/500';
            $errorCode = 500;
            
            // Log para debug
            error_log("Tipo de erro: " . $error['type']);
            error_log("Mensagem de erro: " . $error['message']);
            
            // Tratamento específico para erros de banco de dados
            if ($error['type'] === 'DatabaseError') {
                $template = 'errors/database';
                $errorCode = 500;
            } elseif (strpos($error['type'], 'NotFound') !== false || 
                     strpos($error['message'], 'not found') !== false ||
                     strpos($error['message'], 'não encontrado') !== false ||
                     strpos($error['message'], '404') !== false) {
                $template = 'errors/404';
                $errorCode = 404;
                error_log("Usando template 404");
            }

            // Log para debug
            error_log("Renderizando página de erro - Template: {$template}, Código: {$errorCode}");
            error_log("Dados do erro: " . json_encode($error));

            // Preparar dados para o template
            $data = [
                'error' => [
                    'type' => $error['type'],
                    'message' => $error['message'],
                    'file' => $error['file'] ?? '',
                    'line' => $error['line'] ?? '',
                    'trace' => $error['trace'] ?? []
                ],
                'errorId' => uniqid('err_'),
                'timestamp' => date('Y-m-d H:i:s'),
                'debug' => $_ENV['APP_DEBUG'] ?? false,
                'errorCode' => $errorCode
            ];

            // Definir o código de status HTTP
            http_response_code($errorCode);

            // Log do template e dados
            error_log("Template: " . $template);
            error_log("Dados para o template: " . json_encode($data));

            // Renderizar o template
            echo $twig->render($template, $data);
        } catch (\Exception $e) {
            // Log do erro
            error_log("Erro ao renderizar página de erro: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            // Fallback para caso o Twig falhe
            $this->renderFallback($error);
        }
        exit;
    }

    private function renderFallback($error)
    {
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html>';
        echo '<html lang="pt-BR">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        echo '<title>Erro - ' . htmlspecialchars($error['type']) . '</title>';
        echo '<style>';
        echo 'body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }';
        echo '.error-container { max-width: 800px; margin: 0 auto; }';
        echo '.error-header { background: #dc3545; color: white; padding: 20px; border-radius: 5px 5px 0 0; }';
        echo '.error-body { background: #f8f9fa; padding: 20px; border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 5px 5px; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        echo '<div class="error-container">';
        echo '<div class="error-header">';
        echo '<h1>' . htmlspecialchars($error['type']) . '</h1>';
        echo '</div>';
        echo '<div class="error-body">';
        echo '<h2>Mensagem do Erro</h2>';
        echo '<p>' . htmlspecialchars($error['message']) . '</p>';
        if ($_ENV['APP_DEBUG'] ?? false) {
            echo '<h3>Detalhes</h3>';
            echo '<ul>';
            echo '<li><strong>Arquivo:</strong> ' . htmlspecialchars($error['file']) . '</li>';
            echo '<li><strong>Linha:</strong> ' . htmlspecialchars($error['line']) . '</li>';
            echo '<li><strong>Data/Hora:</strong> ' . htmlspecialchars($error['timestamp']) . '</li>';
            if (isset($error['sqlState'])) {
                echo '<li><strong>SQL State:</strong> ' . htmlspecialchars($error['sqlState']) . '</li>';
            }
            echo '</ul>';
        }
        echo '</div>';
        echo '</div>';
        echo '</body>';
        echo '</html>';
    }

    private function getErrorType($type)
    {
        switch($type) {
            case E_ERROR:
                return 'E_ERROR';
            case E_WARNING:
                return 'E_WARNING';
            case E_PARSE:
                return 'E_PARSE';
            case E_NOTICE:
                return 'E_NOTICE';
            case E_CORE_ERROR:
                return 'E_CORE_ERROR';
            case E_CORE_WARNING:
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR:
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING:
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR:
                return 'E_USER_ERROR';
            case E_USER_WARNING:
                return 'E_USER_WARNING';
            case E_USER_NOTICE:
                return 'E_USER_NOTICE';
            case E_STRICT:
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR:
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED:
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED:
                return 'E_USER_DEPRECATED';
            default:
                return 'UNKNOWN';
        }
    }
}