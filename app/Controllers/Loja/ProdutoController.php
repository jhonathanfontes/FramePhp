<?php

namespace App\Controllers\Loja;

use Core\Controller\BaseController;

class ProdutoController extends BaseController
{
    public function index()
    {
        return $this->render('pages/client/sobre/index');
    }

}