<?php

namespace App\Controllers\Admin;  

use App\Models\User;
use Core\Controller\BaseController;

class AdminController extends BaseController
{
    public function dashboard()
    {
        return $this->render('admin/dashboard');
    }

    public function users()
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