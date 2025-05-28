<?php

namespace App\Controllers\Admin;  

use Core\Controller\BaseController;

class AdminController extends BaseController
{
    public function dashboard()
    {
        echo $this->render('admin/dashboard/index');
    }

    public function users()
    {
        return $this->render('admin/users/index');
    }

    public function settings()
    {
        return $this->render('admin/settings/index');
    }
}