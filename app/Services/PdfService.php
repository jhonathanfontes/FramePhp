
<?php

namespace App\Services;

use TCPDF;

class PdfService
{
    private $pdf;
    
    public function __construct()
    {
        $this->pdf = new TCPDF();
        $this->setupDefaults();
    }
    
    private function setupDefaults()
    {
        // Configurações padrão
        $this->pdf->SetCreator('Sistema Multi-Empresas');
        $this->pdf->SetAuthor('Sistema Multi-Empresas');
        $this->pdf->SetTitle('Relatório');
        $this->pdf->SetSubject('Relatório Gerado');
        $this->pdf->SetKeywords('relatório, pdf, sistema');
        
        // Configurações de margens
        $this->pdf->SetMargins(15, 27, 15);
        $this->pdf->SetHeaderMargin(5);
        $this->pdf->SetFooterMargin(10);
        
        // Auto page breaks
        $this->pdf->SetAutoPageBreak(TRUE, 25);
        
        // Configurar fonte padrão
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $this->pdf->SetFont('helvetica', '', 10);
    }
    
    public function createPortraitReport($title, $period, $company, $content)
    {
        // Orientação retrato
        $this->pdf->AddPage('P', 'A4');
        $this->addHeader($title, $period, $company);
        $this->pdf->writeHTML($content, true, false, true, false, '');
        
        return $this->pdf;
    }
    
    public function createLandscapeReport($title, $period, $company, $content)
    {
        // Orientação paisagem
        $this->pdf->AddPage('L', 'A4');
        $this->addHeader($title, $period, $company);
        $this->pdf->writeHTML($content, true, false, true, false, '');
        
        return $this->pdf;
    }
    
    private function addHeader($title, $period, $company)
    {
        // Logo/Cabeçalho da empresa
        $this->pdf->SetFont('helvetica', 'B', 16);
        $this->pdf->Cell(0, 10, $company['nome_fantasia'] ?? 'Sistema Multi-Empresas', 0, 1, 'C');
        
        if (isset($company['endereco'])) {
            $this->pdf->SetFont('helvetica', '', 10);
            $this->pdf->Cell(0, 5, $company['endereco'], 0, 1, 'C');
        }
        
        $this->pdf->Ln(5);
        
        // Título do relatório
        $this->pdf->SetFont('helvetica', 'B', 14);
        $this->pdf->Cell(0, 10, $title, 0, 1, 'C');
        
        // Período
        $this->pdf->SetFont('helvetica', '', 10);
        $this->pdf->Cell(0, 5, $period, 0, 1, 'C');
        
        // Data de geração
        $this->pdf->Cell(0, 5, 'Gerado em: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
        
        $this->pdf->Ln(10);
    }
    
    public function output($filename = 'relatorio.pdf', $destination = 'I')
    {
        return $this->pdf->Output($filename, $destination);
    }
    
    public function addFooter()
    {
        $this->pdf->SetY(-15);
        $this->pdf->SetFont('helvetica', 'I', 8);
        $this->pdf->Cell(0, 10, 'Página ' . $this->pdf->getAliasNumPage() . '/' . $this->pdf->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}
