<?php

namespace App\Controllers\Painel;

use Core\Controller\BaseController;
use Core\Http\Request;
use Core\Http\Response;
use App\Models\EmpresaModel;
use App\Models\Usuario;
use App\Models\EstabelecimentoModel;

class RelatoriosController extends BaseController
{
    private $empresaModel;
    private $usuarioModel;
    private $estabelecimentoModel;

    public function __construct()
    {
        $this->empresaModel = new EmpresaModel();
        $this->usuarioModel = new Usuario();
        $this->estabelecimentoModel = new EstabelecimentoModel();
    }

    public function index()
    {
        // Obter filtros da requisição
        // $filtros = [
        //     'data_inicio' => $request->get('data_inicio'),
        //     'data_fim' => $request->get('data_fim'),
        //     'empresa_id' => $request->get('empresa_id'),
        //     'tipo_relatorio' => $request->get('tipo_relatorio', 'empresas')
        // ];

        // // Resumo geral
        // $resumo = [
        //     'total_empresas' => $this->empresaModel->count(),
        //     'total_usuarios' => $this->usuarioModel->count(),
        //     'total_estabelecimentos' => $this->estabelecimentoModel->count(),
        //     'atividades_hoje' => 0 // Placeholder
        // ];

        // // Lista de empresas para filtro
        // $empresas = $this->empresaModel->getAll();

        // Dados do relatório baseado no tipo
       // $dados_relatorio = $this->gerarRelatorio($filtros);

        return $this->render('painel/relatorios', [
            'active_menu' => 'relatorios'
        ]);
    }

    public function exportar(Request $request): Response
    {
        $tipo = $request->get('exportar');
        $filtros = [
            'data_inicio' => $request->get('data_inicio'),
            'data_fim' => $request->get('data_fim'),
            'empresa_id' => $request->get('empresa_id'),
            'tipo_relatorio' => $request->get('tipo_relatorio', 'empresas')
        ];

        $dados = $this->gerarRelatorio($filtros);

        if ($tipo === 'excel') {
            return $this->exportarExcel($dados, $filtros);
        } elseif ($tipo === 'pdf') {
            return $this->exportarPDF($dados, $filtros);
        }

        return $this->redirect('/painel/relatorios')->with('error', 'Tipo de exportação inválido');
    }

    private function gerarRelatorio($filtros)
    {
        $tipo = $filtros['tipo_relatorio'];
        
        switch ($tipo) {
            case 'empresas':
                return $this->gerarRelatorioEmpresas($filtros);
            case 'usuarios':
                return $this->gerarRelatorioUsuarios($filtros);
            case 'estabelecimentos':
                return $this->gerarRelatorioEstabelecimentos($filtros);
            case 'atividades':
                return $this->gerarRelatorioAtividades($filtros);
            default:
                return $this->gerarRelatorioEmpresas($filtros);
        }
    }

    private function gerarRelatorioEmpresas($filtros)
    {
        $query = $this->empresaModel->query();
        
        if ($filtros['data_inicio']) {
            $query->where('created_at', '>=', $filtros['data_inicio']);
        }
        
        if ($filtros['data_fim']) {
            $query->where('created_at', '<=', $filtros['data_fim'] . ' 23:59:59');
        }
        
        if ($filtros['empresa_id']) {
            $query->where('id', $filtros['empresa_id']);
        }
        
        return $query->orderBy('created_at', 'DESC')->get();
    }

    private function gerarRelatorioUsuarios($filtros)
    {
        $query = $this->usuarioModel->query();
        
        if ($filtros['data_inicio']) {
            $query->where('created_at', '>=', $filtros['data_inicio']);
        }
        
        if ($filtros['data_fim']) {
            $query->where('created_at', '<=', $filtros['data_fim'] . ' 23:59:59');
        }
        
        if ($filtros['empresa_id']) {
            $query->where('empresa_id', $filtros['empresa_id']);
        }
        
        return $query->orderBy('created_at', 'DESC')->get();
    }

    private function gerarRelatorioEstabelecimentos($filtros)
    {
        $query = $this->estabelecimentoModel->query();
        
        if ($filtros['data_inicio']) {
            $query->where('created_at', '>=', $filtros['data_inicio']);
        }
        
        if ($filtros['data_fim']) {
            $query->where('created_at', '<=', $filtros['data_fim'] . ' 23:59:59');
        }
        
        if ($filtros['empresa_id']) {
            $query->where('empresa_id', $filtros['empresa_id']);
        }
        
        return $query->orderBy('created_at', 'DESC')->get();
    }

    private function gerarRelatorioAtividades($filtros)
    {
        // Placeholder para relatório de atividades
        return [];
    }

    private function exportarExcel($dados, $filtros)
    {
        // Implementar exportação para Excel
        // Por enquanto, retorna uma resposta simples
        return new Response('Exportação Excel em desenvolvimento', 200, [
            'Content-Type' => 'text/plain'
        ]);
    }

    private function exportarPDF($dados, $filtros)
    {
        // Implementar exportação para PDF
        // Por enquanto, retorna uma resposta simples
        return new Response('Exportação PDF em desenvolvimento', 200, [
            'Content-Type' => 'text/plain'
        ]);
    }
} 