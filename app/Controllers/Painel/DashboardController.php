<?php

namespace App\Controllers\Painel;

use Core\Controller\BaseController;
use App\Models\EmpresaModel;
use App\Models\Usuario;
use App\Models\EstabelecimentoModel;
use App\Models\AtividadeModel;

class DashboardController extends BaseController
{
    private $empresaModel;
    private $usuarioModel;
    private $estabelecimentoModel;
    private $atividadeModel;

    public function __construct()
    {
        $this->empresaModel = new EmpresaModel();
        $this->usuarioModel = new Usuario();
        $this->estabelecimentoModel = new EstabelecimentoModel();
        $this->atividadeModel = new AtividadeModel();
    }

    public function index()
    {
        // Estatísticas principais
        $estatisticas = [
            'total_empresas' => $this->empresaModel->count(),
            'total_usuarios' => $this->usuarioModel->count(),
            'total_estabelecimentos' => $this->estabelecimentoModel->count(),
            'atividades_hoje' => $this->atividadeModel->countHoje()
        ];

        // Empresas recentes (últimas 5)
        $empresas_recentes = $this->empresaModel->getRecent(5);

        // Usuários recentes (últimos 5)
        $usuarios_recentes = $this->usuarioModel->getRecent(5);

        // Atividades recentes do sistema
        $atividades_recentes = $this->atividadeModel->getRecent(10);

        // Dados para gráficos
        $empresas_por_porte = $this->empresaModel->getCountByPorte();
        $empresas_por_estado = $this->empresaModel->getCountByEstado();

        return $this->render('painel/dashboard', [
            'active_menu' => 'dashboard',
            'estatisticas' => $estatisticas,
            'empresas_recentes' => $empresas_recentes,
            'usuarios_recentes' => $usuarios_recentes,
            'atividades_recentes' => $atividades_recentes,
            'empresas_por_porte' => $empresas_por_porte,
            'empresas_por_estado' => $empresas_por_estado
        ]);
    }
}
