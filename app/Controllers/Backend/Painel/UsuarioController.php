<?php

namespace App\Controllers\Backend\Painel;

use App\Lib\TableBuilder;
use App\Models\CadUsuarioModel;
use Core\Controller\BaseController;

class UsuarioController extends BaseController
{
   public function creater()
   {
       $data = $this->getParams('nome');
       $usuarios = new CadUsuarioModel();
       $usuarios->create($data);
       return $this->redirect('usuarios');
   }
}