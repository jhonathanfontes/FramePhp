<?php

namespace App\Controllers\Error;

use Core\Controller;

class ErrorController extends Controller
{
    public function show($errorId)
    {
        $error = $_SESSION['last_error'] ?? null;
        
        if (!$error) {
            return $this->view('errors/404', [
                'message' => 'Erro não encontrado'
            ]);
        }

        return $this->view('errors/error', [
            'error' => $error,
            'errorId' => $errorId
        ]);
    }

    public function list()
    {
        $errorLogFile = BASE_PATH . '/storage/logs/errors.json';
        $errors = [];

        if (file_exists($errorLogFile)) {
            $content = file_get_contents($errorLogFile);
            if (!empty($content)) {
                $errors = json_decode($content, true) ?? [];
            }
        }

        return $this->view('errors/list', [
            'errors' => $errors
        ]);
    }
} 