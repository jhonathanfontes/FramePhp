<?php

namespace Core\View;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use Twig\Extension\DebugExtension;

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
        
        $loader = new FilesystemLoader(BASE_PATH . '/app/Views');
        $this->twig = new Environment($loader, [
            'cache' => $cacheDir,
            'debug' => config('app.debug', false),
            'auto_reload' => true
        ]);

        // Adicionar a extensão de depuração
        $this->twig->addExtension(new DebugExtension());

        // Adicionar variáveis globais
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
        $this->twig->addExtension(new \Core\View\Twig\UrlExtension());
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
        return $this->twig->render($template . '.twig', $data);
    }

}
