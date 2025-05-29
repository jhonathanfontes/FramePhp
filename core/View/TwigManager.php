<?php

namespace Core\View;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use Twig\Extension\DebugExtension;
use Core\View\Twig\UrlExtension;
use Core\Router\Router;

class TwigManager
{
    private static $instance = null;
    private $twig;

    private function __construct()
    {
        // Criar diretório de cache se não existir
        $cacheDir = BASE_PATH . '/storage/cache/twig';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }
        
        // Log para debug
        error_log("Inicializando TwigManager");
        error_log("Diretório de templates: " . BASE_VIEW);
        error_log("Diretório de cache: " . $cacheDir);
        
        $loader = new FilesystemLoader(BASE_VIEW);
        $this->twig = new Environment($loader, [
            'cache' => $cacheDir,
            'debug' => config('app.debug', false),
            'auto_reload' => true
        ]);

        // Adicionar a extensão de depuração
        $this->twig->addExtension(new DebugExtension());

        // Adicionar variáveis globais
        \Core\Session\Session::start(); // Ensure session is started
        $this->twig->addGlobal('session', $_SESSION);

        // Adicionar funções globais
        $this->twig->addFunction(new TwigFunction('base_url', 'base_url'));
        $this->twig->addFunction(new TwigFunction('app_name', 'app_name'));
        $this->twig->addFunction(new TwigFunction('app_version', 'app_version'));
        
        // Adicionar funções de tradução
        $this->twig->addFunction(new TwigFunction('trans', 'trans'));
        $this->twig->addFunction(new TwigFunction('__', '__'));
        $this->twig->addFunction(new TwigFunction('get_locale', 'get_locale'));
        
        // Outras funções úteis
        $this->twig->addFunction(new TwigFunction('csrf_token', function() {
            return $_SESSION['csrf_token'] ?? '';
        }));

        // Na função que inicializa o Twig (provavelmente init() ou __construct())
        // Adicione a linha abaixo junto com as outras extensões
        // Adicionar a extensão de URL
      
        
        // Obter a instância do Router
        $router = Router::getInstance();
        $this->twig->addExtension(new UrlExtension($router));
        $this->twig->addExtension(new \Core\View\Twig\AuthExtension());
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // E em outros lugares onde você usa o caminho das views
    public function render(string $template, array $data = []): string
    {
        try {
            // Log para debug
            error_log("Renderizando template: " . $template);
            error_log("Dados: " . json_encode($data));
            
            // Verificar se existem arquivos CSS e JS associados ou pastas
            $viewsPath = BASE_VIEW . '/';
            $templatePath = str_replace('/', DIRECTORY_SEPARATOR, $template);
            
            // Extrair o nome do template da última parte do caminho
            $pathParts = explode(DIRECTORY_SEPARATOR, $templatePath);
            $templateName = end($pathParts);
            
            // Verificar se existe um arquivo HTML ou uma pasta
            $htmlFile = $templatePath . '.html.twig';
            $templateDir = $templatePath;
            
            // Se for uma pasta, procurar por index.html.twig ou templateName.html.twig dentro dela
            if (is_dir($viewsPath . $templateDir)) {
                if (file_exists($viewsPath . $templateDir . DIRECTORY_SEPARATOR . 'index.html.twig')) {
                    $htmlFile = $templateDir . DIRECTORY_SEPARATOR . 'index.html.twig';
                } elseif (file_exists($viewsPath . $templateDir . DIRECTORY_SEPARATOR . $templateName . '.html.twig')) {
                    $htmlFile = $templateDir . DIRECTORY_SEPARATOR . $templateName . '.html.twig';
                }
            }         

            // Verificar CSS
            $cssFile = null;
            if (file_exists($viewsPath . $templatePath . '.css.twig')) {
                $cssFile = $templatePath . '.css.twig';
            } elseif (is_dir($viewsPath . $templateDir)) {
                if (file_exists($viewsPath . $templateDir . DIRECTORY_SEPARATOR . 'index.css.twig')) {
                    $cssFile = $templateDir . DIRECTORY_SEPARATOR . 'index.css.twig';
                } elseif (file_exists($viewsPath . $templateDir . DIRECTORY_SEPARATOR . $templateName . '.css.twig')) {
                    $cssFile = $templateDir . DIRECTORY_SEPARATOR . $templateName . '.css.twig';
                }
            }

            // Verificar JS
            $jsFile = null;
            if (file_exists($viewsPath . $templatePath . '.js.twig')) {
                $jsFile = $templatePath . '.js.twig';
            } elseif (is_dir($viewsPath . $templateDir)) {
                if (file_exists($viewsPath . $templateDir . DIRECTORY_SEPARATOR . 'index.js.twig')) {
                    $jsFile = $templateDir . DIRECTORY_SEPARATOR . 'index.js.twig';
                } elseif (file_exists($viewsPath . $templateDir . DIRECTORY_SEPARATOR . $templateName . '.js.twig')) {
                    $jsFile = $templateDir . DIRECTORY_SEPARATOR . $templateName . '.js.twig';
                }
            }
            
            // Adicionar informações sobre arquivos CSS e JS aos dados
            $data['page_css'] = $cssFile;
            $data['page_js'] = $jsFile;
            
            // Determinar qual arquivo renderizar
            $fileToRender = file_exists($viewsPath . $htmlFile) ? $htmlFile : $template . '.twig';
            return $this->twig->render($fileToRender, $data);
        } catch (\Exception $e) {
            error_log("Erro ao renderizar template: " . $e->getMessage());
            error_log("Template: " . $template);
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function getTwig(): Environment
    {
        return $this->twig;
    }
}
