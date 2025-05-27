<?php

namespace Core\Controller;

use Core\View\TwigManager;
use Core\Http\Response;

class BaseController
{
    protected function view($template, $data = [])
    {
        return TwigManager::getInstance()->render($template, $data);
    }
    
    protected function render(string $view, array $data = []): string
    {
        $twig = TwigManager::getInstance();
        return $twig->render($view, $data);
    }
}