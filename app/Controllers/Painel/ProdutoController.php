<?php

namespace App\Controllers\Painel;

use Core\Controller\BaseController;

class ProdutoController extends BaseController
{
   public function index()
    {
        return $this->render('templates/dashboard');
    }
} 