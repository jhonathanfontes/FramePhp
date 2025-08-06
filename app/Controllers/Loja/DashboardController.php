<?php

namespace App\Controllers\Loja;

use Core\Controller\BaseController;

class DashboardController extends BaseController
{
    public function sobre()
    {
        return $this->render('loja/sobre');
    }
    public function contato()
    {
        return $this->render('loja/contato');
    }
}