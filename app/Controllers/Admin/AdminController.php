<?php

namespace App\Controllers\Admin;  

use Core\Controller\BaseController;
use App\Models\CadUsuarioModel; // Certifique-se de que o modelo está importado corretamente

class AdminController extends BaseController
{
    public function dashboard()
    {
        return $this->render('pages/admin/dashboard');
    }

  public function users(): string
    {
        // 1. Usa o model injetado para buscar todos os usuários.
        $users = new CadUsuarioModel();

        // 2. Renderiza o template Twig, passando a lista de usuários para ele.
        // O nome da variável no template será 'users'.
        return $this->render('pages/admin/users/index', [
            'users' => $users->findAllUsers()
        ]);
    }

    public function settings()
    {
        return $this->render('admin/settings/index');
    }
}