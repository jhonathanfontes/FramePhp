<?php

namespace App\Controllers\Admin;  

use App\Models\User;
use Core\Controller\BaseController;

class AdminController extends BaseController
{
    public function dashboard()
    {
        return $this->render('templates/dashboard');
    }

    public function users()
    {
        $usuarios = new User();
        $data = [
            'usuarios' => $usuarios->findAll(),
        ];

         return $this->render('templates/users', $data);
    }

    public function settings()
    {
        return $this->render('admin/settings/index');
    }
}