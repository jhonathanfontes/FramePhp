<?php

namespace Core\Controller;

abstract class BaseController
{
    protected function render(string $view, array $data = []): string
    {
        $viewPath = BASE_PATH . '/app/Views/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            throw new \Exception("View não encontrada: {$view}");
        }

        extract($data);
        
        ob_start();
        require $viewPath;
        return ob_get_clean();
    }

    protected function json($data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}