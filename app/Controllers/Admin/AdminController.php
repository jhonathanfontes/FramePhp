<?php

namespace App\Controllers\Admin;  

use Core\Controller\BaseController;

class AdminController extends BaseController
{
    public function dashboard()
    {
        return $this->render('admin/dashboard');
    }
}