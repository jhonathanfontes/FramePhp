<?php

namespace App\Controllers\Error;

use Core\Controller;

class ErrorController extends Controller
{
    public function show($errorId = null)
    {
        $error = $_SESSION['last_error'] ?? null;
        
        if (!$error) {
            return $this->view('errors/404', [
                'message' => 'Erro não encontrado'
            ]);
        }

        return $this->view('errors/error', [
            'error' => $error,
            'errorId' => $errorId ?? 'unknown'
        ]);
    }
}