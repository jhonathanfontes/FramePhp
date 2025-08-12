<?php

namespace App\Controllers\Admin;

use Core\Controller\BaseController;

class ProdutoController extends BaseController
{
     public function index()
    {
        return $this->render('admin/produtos');
    }

} 