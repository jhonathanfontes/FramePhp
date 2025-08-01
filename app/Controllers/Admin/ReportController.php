The code adds PDF export functionality to the ReportController, including vendas and produtos reports, using the PdfService and TCPDF library, and improves translations.
```

```php
<?php

namespace App\Controllers\Admin;

use Core\Controller\BaseController;
use App\Models\EmpresaModel;
use App\Models\LojaModel;
use App\Models\PedidoModel;
use App\Models\CadProdutoModel;
use App\Services\PdfService;

class ReportController extends BaseController
{
    private $empresaModel;
    private $lojaModel;
    private $pedidoModel;
    private $produtoModel;
    private $pdfService;

    public function __construct()
    {
        $this->empresaModel = new EmpresaModel();
        $this->lojaModel = new LojaModel();
        $this->pedidoModel = new PedidoModel();
        $this->produtoModel = new CadProdutoModel();
        $this->pdfService = new PdfService();
    }

    public function index()
    {
        return $this->render('pages/admin/reports/index');
    }

    // Relatório de Vendas (Retrato)
    public function vendas()
    {
        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-t');
        $empresaId = $_GET['empresa_id'] ?? null;
        $format = $_GET['format'] ?? 'html';

        $empresa = null;
        if ($empresaId) {
            $empresa = $this->empresaModel->findById($empresaId);
        }

        $vendas = $this->pedidoModel->getVendasPorPeriodo($dataInicio, $dataFim, $empresaId);
        $totais = $this->pedidoModel->getTotaisVendas($dataInicio, $dataFim, $empresaId);

        $data = [
            'title' => __('reports.sales_report'),
            'orientation' => 'portrait',
            'period' => date('d/m/Y', strtotime($dataInicio)) . ' a ' . date('d/m/Y', strtotime($dataFim)),
            'company' => $empresa,
            'vendas' => $vendas,
            'totais' => $totais,
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim
        ];

        if ($format === 'pdf') {
            return $this->exportVendasPdf($data);
        }

        return $this->render('pages/admin/reports/vendas', $data);
    }

    private function exportVendasPdf($data)
    {
        $html = $this->renderPdfContent('pages/admin/reports/vendas_pdf', $data);

        $pdf = $this->pdfService->createPortraitReport(
            $data['title'],
            $data['period'],
            $data['company'] ?? ['nome_fantasia' => 'Sistema Multi-Empresas'],
            $html
        );

        $filename = 'relatorio_vendas_' . date('Y-m-d_H-i-s') . '.pdf';
        return $pdf->output($filename, 'D');
    }

    // Relatório de Produtos (Paisagem)
    public function produtos()
    {
        $empresaId = $_GET['empresa_id'] ?? null;
        $categoria = $_GET['categoria'] ?? null;
        $format = $_GET['format'] ?? 'html';

        $empresa = null;
        if ($empresaId) {
            $empresa = $this->empresaModel->findById($empresaId);
        }

        $produtos = $this->produtoModel->getRelatorioCompleto($empresaId, $categoria);
        $estatisticas = $this->produtoModel->getEstatisticasProdutos($empresaId);

        $data = [
            'title' => __('reports.products_report'),
            'orientation' => 'landscape',
            'period' => __('reports.updated_at') . ' ' . date('d/m/Y H:i:s'),
            'company' => $empresa,
            'produtos' => $produtos,
            'estatisticas' => $estatisticas
        ];

        if ($format === 'pdf') {
            return $this->exportProdutosPdf($data);
        }

        return $this->render('pages/admin/reports/produtos', $data);
    }

    private function exportProdutosPdf($data)
    {
        $html = $this->renderPdfContent('pages/admin/reports/produtos_pdf', $data);

        $pdf = $this->pdfService->createLandscapeReport(
            $data['title'],
            $data['period'],
            $data['company'] ?? ['nome_fantasia' => 'Sistema Multi-Empresas'],
            $html
        );

        $filename = 'relatorio_produtos_' . date('Y-m-d_H-i-s') . '.pdf';
        return $pdf->output($filename, 'D');
    }

    // Relatório Financeiro (Retrato)
    public function financeiro()
    {
        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-t');
        $empresaId = $_GET['empresa_id'] ?? null;

        $empresa = null;
        if ($empresaId) {
            $empresa = $this->empresaModel->findById($empresaId);
        }

        $receitas = $this->pedidoModel->getReceitasPorPeriodo($dataInicio, $dataFim, $empresaId);
        $resumoFinanceiro = $this->pedidoModel->getResumoFinanceiro($dataInicio, $dataFim, $empresaId);

        return $this->render('pages/admin/reports/financeiro', [
            'title' => 'Relatório Financeiro',
            'orientation' => 'portrait',
            'period' => date('d/m/Y', strtotime($dataInicio)) . ' a ' . date('d/m/Y', strtotime($dataFim)),
            'company' => $empresa,
            'receitas' => $receitas,
            'resumo' => $resumoFinanceiro,
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim
        ]);
    }

    // Relatório de Empresas (Paisagem)
    public function empresas()
    {
        $empresas = $this->empresaModel->getRelatorioEmpresas();
        $estatisticas = $this->empresaModel->getEstatisticasGerais();

        return $this->render('pages/admin/reports/empresas', [
            'title' => 'Relatório de Empresas',
            'orientation' => 'landscape',
            'period' => 'Atualizado em ' . date('d/m/Y H:i:s'),
            'company' => [
                'nome_fantasia' => 'Sistema Multi-Empresas',
                'endereco' => 'Relatório Administrativo'
            ],
            'empresas' => $empresas,
            'estatisticas' => $estatisticas
        ]);
    }

    private function renderPdfContent($template, $data)
    {
        ob_start();
        $this->render($template, $data);
        return ob_get_clean();
    }
}