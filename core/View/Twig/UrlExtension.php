<?php

namespace Core\View\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Core\Router\Router;

class UrlExtension extends AbstractExtension
{
    private $router;

    public function __construct()
    {
    }

    /**
     * Retorna as funções disponibilizadas por esta extensão
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('url', [$this, 'generateUrl']),
            new TwigFunction('base_url', [$this, 'getBaseUrl']),
        ];
    }

    public function generateUrl(string $name, ?array $params = []): ?string
    {
        $request = new \Core\Http\Request();
        $router = new \Core\Router\Router($request);
        return $router->generateUrl($name, $params);
    }

    public function getBaseUrl()
    {
        return rtrim($_ENV['APP_URL'] ?? '', '/');
    }
}
