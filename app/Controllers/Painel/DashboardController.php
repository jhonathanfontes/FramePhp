<?php

namespace App\Controllers\Painel;

use Core\Controller\BaseController;
use App\Models\EmpresaModel;
use App\Models\Usuario;

class DashboardController extends BaseController
{
    private $empresaModel;
    private $usuarioModel;

    public function __construct()
    {
        $this->empresaModel = new EmpresaModel();
        $this->usuarioModel = new Usuario();
    }

    public function index()
    {
        $totalEmpresas  = 0;
        $empresasAtivas = 0;
        $totalUsuarios  = 0;
        
        return $this->render('painel/dashboard', [
            'active_menu' => 'dashboard',
            'total_empresas' => $totalEmpresas,
            'empresas_ativas' => $empresasAtivas,
            'total_usuarios' => $totalUsuarios
        ]);
    }
}
