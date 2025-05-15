<?php

namespace App\Controllers\Admin;

use Core\Controller\BaseController;

class ExemploController extends BaseController
{
    public function index()
    {
        return $this->json([
            'mensagem' => 'Olá do ExemploController!',
            'status' => 'sucesso'
        ]);
    }
}