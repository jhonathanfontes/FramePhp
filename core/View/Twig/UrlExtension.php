<?php

namespace Core\View\Twig;

use Core\Router\Router;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UrlExtension extends AbstractExtension
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('url', [$this, 'generateUrl']),
            // A definição aqui permanece a mesma
            new TwigFunction('base_url', [$this, 'getBaseUrl']),
        ];
    }

    public function generateUrl(string $name, array $params = []): ?string
    {
        return $this->router->generateUrl($name, $params);
    }

    /**
     * --- MÉTODO ALTERADO ---
     * Retorna a URL base da aplicação, com um caminho opcional.
     *
     * @param string|null $path O caminho a ser adicionado à URL base.
     * @return string
     */
    public function getBaseUrl(?string $path = null): string
    {
        // Pega a URL base a partir do arquivo .env e remove qualquer barra no final.
        $baseUrl = rtrim(env('APP_URL', 'http://localhost'), '/');

        // Se um caminho foi passado como parâmetro...
        if ($path) {
            // Remove qualquer barra do início do caminho e o anexa à URL base.
            return $baseUrl . '/' . ltrim($path, '/');
        }

        // Se nenhum caminho foi passado, retorna apenas a URL base.
        return $baseUrl;
    }
}