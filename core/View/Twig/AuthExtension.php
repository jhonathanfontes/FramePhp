<?php

namespace Core\View\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Core\Auth\Auth;

class AuthExtension extends AbstractExtension
{
    /**
     * Retorna as funções disponibilizadas por esta extensão
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('auth', [$this, 'getAuthUser']),
        ];
    }

    /**
     * Retorna o usuário autenticado ou null se não estiver autenticado
     *
     * @return mixed
     */
    public function getAuthUser()
    {
        return Auth::user();
    }
}