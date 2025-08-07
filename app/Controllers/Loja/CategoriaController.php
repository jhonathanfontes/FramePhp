<?php

namespace App\Controllers\Loja;

use Core\Controller\BaseController;

class CategoriaController extends BaseController
{
    public function index()
    {
        return $this->render('pages/shop/produto');
    }
}