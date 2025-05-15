<?php

namespace Core\View;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigManager
{
    private static $instance = null;
    private $twig;

    private function __construct()
    {
        $loader = new FilesystemLoader(BASE_PATH . '/app/Views');
        $this->twig = new Environment($loader, [
            'cache' => BASE_PATH . '/storage/cache/twig',
            'debug' => APP_DEBUG,
            'auto_reload' => true
        ]);

        // Adicionar funções globais
        $this->twig->addFunction(new \Twig\TwigFunction('base_url', 'base_url'));
        $this->twig->addFunction(new \Twig\TwigFunction('app_name', 'app_name'));
        $this->twig->addFunction(new \Twig\TwigFunction('app_version', 'app_version'));
        $this->twig->addFunction(new \Twig\TwigFunction('app_debug', function() {
            return defined('APP_DEBUG') && APP_DEBUG === true;
        }));
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