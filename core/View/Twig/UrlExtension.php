<?php

namespace Core\View\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UrlExtension extends AbstractExtension
{
    /**
     * Retorna as funções disponibilizadas por esta extensão
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('base_url', [$this, 'baseUrl']),
        ];
    }

    /**
     * Retorna a URL base da aplicação
     *
     * @param string $path
     * @return string
     */
    public function baseUrl(string $path = '')
    {
        $baseUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost', '/');
        $path = ltrim($path, '/');
        
        return $path ? "{$baseUrl}/{$path}" : $baseUrl;
    }
}