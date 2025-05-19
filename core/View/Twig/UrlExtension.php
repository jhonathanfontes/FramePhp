<?php

namespace Core\View\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Core\Router\Router;

class UrlExtension extends AbstractExtension
{
    private $router;

    public function __construct(Router $router = null)
    {
        // Obter a instância do Router do container global ou criar uma nova
        $this->router = $router ?? new Router();
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

    public function generateUrl($name, $params = [])
    {
        return $this->router->generateUrl($name, $params);
    }

    public function getBaseUrl()
    {
        return rtrim($_ENV['APP_URL'] ?? '', '/');
    }
}