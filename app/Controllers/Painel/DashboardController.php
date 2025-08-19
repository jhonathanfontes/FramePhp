<?php

namespace App\Controllers\Painel;

use Core\Controller\BaseController;

class DashboardController extends BaseController
{
   public function dashboard()
    {
        return $this->render('painel/dashboard');
    }
} 