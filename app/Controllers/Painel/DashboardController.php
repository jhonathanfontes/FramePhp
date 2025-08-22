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
        // // Estatísticas principais
        // $estatisticas = [
        //     'total_empresas' => $this->empresaModel->count(),
        //     'total_usuarios' => $this->usuarioModel->count(),
        //     'total_estabelecimentos' => $this->estabelecimentoModel->count(),
        //     'atividades_hoje' => $this->atividadeModel->countHoje()
        // ];

        // // Empresas recentes (últimas 5)
        // $empresas_recentes = $this->empresaModel->getRecent(5);

        // // Usuários recentes (últimos 5)
        // $usuarios_recentes = $this->usuarioModel->getRecent(5);

        // // Atividades recentes do sistema
        // $atividades_recentes = $this->atividadeModel->getRecent(10);

        // // Dados para gráficos
        // $empresas_por_porte = $this->empresaModel->getCountByPorte();
        // $empresas_por_estado = $this->empresaModel->getCountByEstado();

        $estatisticas = [
            'total_empresas' => 10,
            'total_usuarios' => 10,
            'total_estabelecimentos' => 10,
            'atividades_hoje' => 10
        ];
        $empresas_recentes = [
            'nome' => 'Empresa 1',
            'email' => 'empresa1@gmail.com',
            'telefone' => '11 99999-9999',
            'endereco' => 'Rua 1, 100',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => '04101-300',
            'cnpj' => '12.345.678/0001-00',
        ];
        $usuarios_recentes = [
            'nome' => 'Usuario 1',
            'email' => 'usuario1@gmail.com',
            'telefone' => '11 99999-9999',
            'endereco' => 'Rua 1, 100',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => '04101-300',
            'cnpj' => '12.345.678/0001-00',
        ];
        $atividades_recentes = [
            'descricao' => 'Atividade 1',
            'data' => '2025-01-01',
            'usuario' => 'Usuario 1',
            'empresa' => 'Empresa 1',
            'estabelecimento' => 'Estabelecimento 1',
        ];
        $empresas_por_porte = [
            'porte' => 'Pequena',
            'quantidade' => 10
        ];
        $empresas_por_estado = [
            'estado' => 'SP',
            'quantidade' => 0
        ];

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
