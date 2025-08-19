<?php

namespace App\Controllers\Painel;

use Core\Controller\BaseController;

class EmpresasController extends BaseController
{
    public function index()
    {
        $empresas = [];
        return $this->render('painel/empresas', compact('empresas'));
    }
}