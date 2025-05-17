<?php

namespace Core;

class Controller
{
    protected function view($template, $data = [])
    {
        return \Core\View\TwigManager::getInstance()->render($template, $data);
    }

    protected function json($data, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    protected function redirect($url)
    {
        header("Location: {$url}");
        exit;
    }
} 