<?php

namespace Core\Error;

use Core\View\TwigManager;

class ErrorHandler
{
    public static function register()
    {
        // Definir manipulador de erros
        set_error_handler([self::class, 'handleError']);
        
        // Definir manipulador de exceções
        set_exception_handler([self::class, 'handleException']);
        
        // Registrar shutdown function
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    public static function handleError($level, $message, $file, $line)
    {
        if (!(error_reporting() & $level)) {
            return false;
        }

        $error = [
            'type' => 'Erro',
            'message' => $message,
            'file' => $file,
            'line' => $line,
            'level' => $level
        ];

        self::renderError($error, 500);
        return true;
    }

    public static function handleException($exception)
    {
        $error = [
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ];

        $statusCode = 500;
        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        }

        // Verificar se é um erro de banco de dados
        if (strpos($exception->getMessage(), 'Erro de conexão com o banco de dados') !== false) {
            self::renderTemplate('errors/database', $error);
            return;
        }

        self::renderError($error, $statusCode);
    }

    public static function handleShutdown()
    {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            self::handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    public static function handleNotFound()
    {
        $error = [
            'type' => 'NotFoundError',
            'message' => 'A página solicitada não foi encontrada.',
            'file' => '',
            'line' => 0
        ];

        self::renderTemplate('errors/404', $error);
    }

    private static function renderError(array $error, int $statusCode)
    {
        http_response_code($statusCode);
        
        // Determinar qual template usar com base no código de status
        $template = 'errors/default';
        
        switch ($statusCode) {
            case 404:
                $template = 'errors/404';
                break;
            case 500:
                $template = 'errors/500';
                break;
            // Adicione mais casos conforme necessário
        }
        
        // Configurações específicas para cada tipo de erro
        $errorConfig = self::getErrorConfig($statusCode, $error);
        
        self::renderTemplate($template, $error, array_merge([
            'code' => $statusCode
        ], $errorConfig));
    }
    
    private static function renderTemplate(string $template, array $error, array $data = [])
    {
        try {
            $twig = TwigManager::getInstance();
            
            echo $twig->render($template, array_merge([
                'error' => $error
            ], $data));
        } catch (\Exception $e) {
            // Fallback para caso o Twig falhe
            self::renderFallback($error, $data['code'] ?? 500);
        }
        
        exit;
    }
    
    private static function renderFallback(array $error, int $statusCode)
    {
        echo '<h1>Erro ' . $statusCode . '</h1>';
        echo '<p>' . $error['message'] . '</p>';
        if (APP_DEBUG) {
            echo '<p>Arquivo: ' . $error['file'] . ' (linha ' . $error['line'] . ')</p>';
        } else {
            echo '<p>Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.</p>';
        }
    }

    private static function getErrorConfig(int $statusCode, array $error): array
    {
        switch ($statusCode) {
            case 404:
                return [
                    'title' => 'Página Não Encontrada',
                    'color' => 'warning',
                    'help' => [
                        'Verifique se a URL está correta',
                        'A página pode ter sido movida ou excluída',
                        'Volte para a página inicial e tente navegar para o conteúdo desejado'
                    ]
                ];
                
            case 403:
                return [
                    'title' => 'Acesso Proibido',
                    'color' => 'danger',
                    'help' => [
                        'Você não tem permissão para acessar este recurso',
                        'Faça login com uma conta que tenha as permissões necessárias',
                        'Entre em contato com o administrador se acredita que isso é um erro'
                    ]
                ];
                
            case 401:
                return [
                    'title' => 'Não Autorizado',
                    'color' => 'warning',
                    'help' => [
                        'Você precisa fazer login para acessar este recurso',
                        'Sua sessão pode ter expirado',
                        'Faça login novamente para continuar'
                    ]
                ];
                
            case 400:
                return [
                    'title' => 'Requisição Inválida',
                    'color' => 'warning',
                    'help' => [
                        'A requisição enviada contém erros',
                        'Verifique os dados enviados e tente novamente',
                        'Se o problema persistir, entre em contato com o suporte'
                    ]
                ];
                
            case 500:
            default:
                return [
                    'title' => 'Erro Interno do Servidor',
                    'color' => 'danger',
                    'help' => [
                        'Este é um problema no servidor e não com sua requisição',
                        'Os administradores foram notificados do problema',
                        'Tente novamente mais tarde ou entre em contato com o suporte'
                    ]
                ];
        }
    }
}