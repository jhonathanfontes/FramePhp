<?php

namespace App\Controllers\Loja;

use Core\Controller\BaseController;

class DashboardController extends BaseController
{
    public function catalogo()
    {
        return $this->render('pages/client/sobre/index');
    }
    public function contato()
    {
        return $this->render('pages/client/contato/index');
    }
}