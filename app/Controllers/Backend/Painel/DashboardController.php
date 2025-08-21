<?php

namespace App\Controllers\Backend\Painel;

use App\Lib\TableBuilder;
use Core\Controller\BaseController;
use Core\Http\Request;
use Core\Http\Response;
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

    /**
     * API para obter estatísticas do dashboard
     */
    public function getEstatisticas(Request $request): Response
    {
        try {
            $estatisticas = [
                'total_empresas' => $this->empresaModel->count(),
                'total_usuarios' => $this->usuarioModel->count(),
                'total_estabelecimentos' => $this->estabelecimentoModel->count(),
                'atividades_hoje' => $this->atividadeModel->countHoje(),
                'empresas_ativas' => $this->empresaModel->countAtivas(),
                'usuarios_ativos' => $this->usuarioModel->countAtivos(),
                'crescimento_mensal' => $this->getCrescimentoMensal(),
                'distribuicao_estados' => $this->getDistribuicaoEstados()
            ];

            return $this->jsonResponse([
                'success' => true,
                'data' => $estatisticas
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao carregar estatísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para obter dados de gráficos
     */
    public function getDadosGraficos(Request $request): Response
    {
        try {
            $tipo = $request->get('tipo', 'empresas_porte');
            
            switch ($tipo) {
                case 'empresas_porte':
                    $dados = $this->getEmpresasPorPorte();
                    break;
                case 'empresas_estado':
                    $dados = $this->getEmpresasPorEstado();
                    break;
                case 'crescimento_tempo':
                    $dados = $this->getCrescimentoTempo();
                    break;
                case 'usuarios_por_empresa':
                    $dados = $this->getUsuariosPorEmpresa();
                    break;
                default:
                    $dados = $this->getEmpresasPorPorte();
            }

            return $this->jsonResponse([
                'success' => true,
                'data' => $dados
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao carregar dados dos gráficos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para obter atividades recentes
     */
    public function getAtividadesRecentes(Request $request): Response
    {
        try {
            $limite = $request->get('limite', 10);
            $atividades = $this->atividadeModel->getRecent($limite);

            return $this->jsonResponse([
                'success' => true,
                'data' => $atividades
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao carregar atividades: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para obter dados de tabelas
     */
    public function getDadosTabela(Request $request): Response
    {
        try {
            $tipo = $request->get('tipo', 'empresas');
            $pagina = $request->get('pagina', 1);
            $porPagina = $request->get('por_pagina', 10);
            $filtros = $request->get('filtros', []);

            switch ($tipo) {
                case 'empresas':
                    $dados = $this->getEmpresasTabela($pagina, $porPagina, $filtros);
                    break;
                case 'usuarios':
                    $dados = $this->getUsuariosTabela($pagina, $porPagina, $filtros);
                    break;
                case 'estabelecimentos':
                    $dados = $this->getEstabelecimentosTabela($pagina, $porPagina, $filtros);
                    break;
                default:
                    $dados = $this->getEmpresasTabela($pagina, $porPagina, $filtros);
            }

            return $this->jsonResponse([
                'success' => true,
                'data' => $dados
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao carregar dados da tabela: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Métodos privados para obter dados específicos
     */
    private function getCrescimentoMensal()
    {
        $meses = [];
        $valores = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $data = date('Y-m', strtotime("-$i months"));
            $meses[] = date('M/Y', strtotime("-$i months"));
            $valores[] = $this->empresaModel->countByMonth($data);
        }

        return [
            'labels' => $meses,
            'data' => $valores
        ];
    }

    private function getDistribuicaoEstados()
    {
        return $this->empresaModel->getCountByEstado();
    }

    private function getEmpresasPorPorte()
    {
        $dados = $this->empresaModel->getCountByPorte();
        
        return [
            'labels' => array_column($dados, 'porte'),
            'data' => array_column($dados, 'quantidade'),
            'colors' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
        ];
    }

    private function getEmpresasPorEstado()
    {
        $dados = $this->empresaModel->getCountByEstado();
        
        return [
            'labels' => array_column($dados, 'estado'),
            'data' => array_column($dados, 'quantidade'),
            'colors' => ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
        ];
    }

    private function getCrescimentoTempo()
    {
        $dias = [];
        $valores = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $data = date('Y-m-d', strtotime("-$i days"));
            $dias[] = date('d/m', strtotime("-$i days"));
            $valores[] = $this->empresaModel->countByDate($data);
        }

        return [
            'labels' => $dias,
            'data' => $valores
        ];
    }

    private function getUsuariosPorEmpresa()
    {
        return $this->usuarioModel->getCountByEmpresa();
    }

    private function getEmpresasTabela($pagina, $porPagina, $filtros)
    {
        $offset = ($pagina - 1) * $porPagina;
        $empresas = $this->empresaModel->getPaginated($offset, $porPagina, $filtros);
        $total = $this->empresaModel->countWithFilters($filtros);

        return [
            'dados' => $empresas,
            'paginacao' => [
                'pagina_atual' => $pagina,
                'por_pagina' => $porPagina,
                'total' => $total,
                'total_paginas' => ceil($total / $porPagina)
            ]
        ];
    }

    private function getUsuariosTabela($pagina, $porPagina, $filtros)
    {
        $offset = ($pagina - 1) * $porPagina;
        $usuarios = $this->usuarioModel->getPaginated($offset, $porPagina, $filtros);
        $total = $this->usuarioModel->countWithFilters($filtros);

        return [
            'dados' => $usuarios,
            'paginacao' => [
                'pagina_atual' => $pagina,
                'por_pagina' => $porPagina,
                'total' => $total,
                'total_paginas' => ceil($total / $porPagina)
            ]
        ];
    }

    private function getEstabelecimentosTabela($pagina, $porPagina, $filtros)
    {
        $offset = ($pagina - 1) * $porPagina;
        $estabelecimentos = $this->estabelecimentoModel->getPaginated($offset, $porPagina, $filtros);
        $total = $this->estabelecimentoModel->countWithFilters($filtros);

        return [
            'dados' => $estabelecimentos,
            'paginacao' => [
                'pagina_atual' => $pagina,
                'por_pagina' => $porPagina,
                'total' => $total,
                'total_paginas' => ceil($total / $porPagina)
            ]
        ];
    }

    private function jsonResponse($dados, $statusCode = 200): Response
    {
        return new Response(json_encode($dados), $statusCode, [
            'Content-Type' => 'application/json'
        ]);
    }
} 