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

        $this->twig->addFunction(new TwigFunction('csrf_token', function () {
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
            $viewsPath = BASE_VIEW . DIRECTORY_SEPARATOR;
            // Garante que estamos usando os separadores de diretório corretos do sistema operacional.
            $templatePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $template);

            // --- CORREÇÃO PRINCIPAL ---
            // Pega o nome do diretório e o nome base do template de forma mais segura.
            $templateDir = dirname($templatePath);
            $templateBaseName = basename($templatePath);

            // Define os possíveis nomes de arquivo a serem procurados.
            // Ex: para 'pages/home', procura por 'pages/home.html.twig' e 'pages/home/index.html.twig'.
            $possibleHtmlFiles = [
                $templatePath . '.html.twig',
                $templatePath . DIRECTORY_SEPARATOR . 'index.html.twig'
            ];

            $fileToRender = null;
            foreach ($possibleHtmlFiles as $file) {
                if (file_exists($viewsPath . $file)) {
                    $fileToRender = $file;
                    break;
                }
            }

            // Se nenhum arquivo de template for encontrado, lança uma exceção.
            if ($fileToRender === null) {
                throw new \Exception("Template \"{$template}\" não encontrado.");
            }

            // --- LÓGICA CORRIGIDA PARA CSS E JS ---
            // Procura por um arquivo CSS com o mesmo nome ou um 'style.css' no mesmo diretório.
            $possibleCssFiles = [
                str_replace('.html.twig', '.css.twig', $fileToRender),
                $templateDir . DIRECTORY_SEPARATOR . 'style.css.twig'
            ];

            $cssFile = null;
            foreach ($possibleCssFiles as $file) {
                if (file_exists($viewsPath . $file)) {
                    $cssFile = $file;
                    break;
                }
            }

            // Procura por um arquivo JS com o mesmo nome ou um 'script.js' no mesmo diretório.
            $possibleJsFiles = [
                str_replace('.html.twig', '.js.twig', $fileToRender),
                $templateDir . DIRECTORY_SEPARATOR . 'script.js.twig'
            ];

            $jsFile = null;
            foreach ($possibleJsFiles as $file) {
                if (file_exists($viewsPath . $file)) {
                    $jsFile = $file;
                    break;
                }
            }

            // Adiciona os caminhos encontrados (ou null) aos dados da view.
            $data['page_css'] = $cssFile;
            $data['page_js'] = $jsFile;

            // Renderiza o template encontrado.
            return $this->twig->render($fileToRender, $data);

        } catch (\Exception $e) {
            // Lança a exceção para que o ErrorHandler possa capturá-la.
            throw $e;
        }
    }

    public function getTwig(): Environment
    {
        return $this->twig;
    }
}