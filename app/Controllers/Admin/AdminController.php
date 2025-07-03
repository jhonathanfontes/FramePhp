<?php

namespace App\Controllers\Admin;  

use App\Models\User;
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
        $usuarios = new User();
        $data = [
            'usuarios' => $usuarios->findAll(),
        ];

         return $this->render('admin/users/index', $data);
    }

    public function settings()
    {
        return $this->render('admin/settings/index');
    }
}