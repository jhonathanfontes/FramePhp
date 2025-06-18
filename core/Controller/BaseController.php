<?php

namespace Core\Controller;

use Core\View\TwigManager;
use Core\Http\Response;
use Core\Controller;

class BaseController extends Controller
{
    protected function render(string $view, array $data = []): string
    {
        $twig = TwigManager::getInstance();
        return $twig->render($view, $data);
    }
}