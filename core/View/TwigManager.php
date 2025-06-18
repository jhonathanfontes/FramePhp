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
        $cacheDir = BASE_PATH . '/storage/cache/twig';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }
                     
        $loader = new FilesystemLoader(BASE_VIEW);
        $this->twig = new Environment($loader, [
            'cache' => $cacheDir,
            'debug' => config('app.debug', false),
            'auto_reload' => true
        ]);

        $this->twig->addExtension(new DebugExtension());

        \Core\Session\Session::start();
        $this->twig->addGlobal('session', $_SESSION ?? []);

        // Registra as funções globais para serem usadas nos templates.
        // O segundo argumento de TwigFunction é o nome da função PHP global a ser chamada.
        $this->twig->addFunction(new TwigFunction('app_name', 'app_name'));
        $this->twig->addFunction(new TwigFunction('app_version', 'app_version'));
        $this->twig->addFunction(new TwigFunction('trans', 'trans'));
        $this->twig->addFunction(new TwigFunction('__', '__'));
        $this->twig->addFunction(new TwigFunction('get_locale', 'get_locale'));
        
        $this->twig->addFunction(new TwigFunction('csrf_token', function() {
            return $_SESSION['csrf_token'] ?? '';
        }));
        
        // Mantém as correções anteriores da UrlExtension e AuthExtension
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

    public function render(string $template, array $data = []): string
    {
        try {
            $viewsPath = BASE_VIEW . '/';
            $templatePath = str_replace('/', DIRECTORY_SEPARATOR, $template);
            
            $pathParts = explode(DIRECTORY_SEPARATOR, $templatePath);
            $templateName = end($pathParts);
            
            $htmlFile = $templatePath . '.html.twig';
            $templateDir = $templatePath;
            
            if (is_dir($viewsPath . $templateDir)) {
                if (file_exists($viewsPath . $templateDir . DIRECTORY_SEPARATOR . 'index.html.twig')) {
                    $htmlFile = $templateDir . DIRECTORY_SEPARATOR . 'index.html.twig';
                } elseif (file_exists($viewsPath . $templateDir . DIRECTORY_SEPARATOR . $templateName . '.html.twig')) {
                    $htmlFile = $templateDir . DIRECTORY_SEPARATOR . $templateName . '.html.twig';
                }
            }         

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
            
            $data['page_css'] = $cssFile;
            $data['page_js'] = $jsFile;
            
            $fileToRender = file_exists($viewsPath . $htmlFile) ? $htmlFile : $template . '.twig';
            return $this->twig->render($fileToRender, $data);
        } catch (\Exception $e) {
              throw $e;
        }
    }

    public function getTwig(): Environment
    {
        return $this->twig;
    }
}