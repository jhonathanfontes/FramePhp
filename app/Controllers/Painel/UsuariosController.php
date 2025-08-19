<?php

namespace App\Controllers\Painel;

use Core\Controller\BaseController;

class UsuariosController extends BaseController
{
    public function index()
    {
        $usuarios = [];
        return $this->render('painel/usuarios', compact('usuarios'));
    }
} 