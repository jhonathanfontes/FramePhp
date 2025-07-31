
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
<?php

namespace App\Services;

use TCPDF;

class PdfService
{
    private TCPDF $pdf;
    
    public function __construct()
    {
        $this->pdf = new TCPDF();
        $this->setupDefaults();
    }
    
    private function setupDefaults(): void
    {
        $this->pdf->SetCreator('Sistema Multi-Empresas');
        $this->pdf->SetAuthor('Framework PHP');
        $this->pdf->SetMargins(15, 27, 15);
        $this->pdf->SetHeaderMargin(5);
        $this->pdf->SetFooterMargin(10);
        $this->pdf->SetAutoPageBreak(true, 25);
        $this->pdf->setImageScale(1.25);
        $this->pdf->SetFont('helvetica', '', 10);
    }
    
    public function createReport(string $title, array $data, string $orientation = 'P'): string
    {
        $this->pdf->AddPage($orientation);
        
        // Header
        $this->pdf->SetFont('helvetica', 'B', 16);
        $this->pdf->Cell(0, 10, $title, 0, 1, 'C');
        $this->pdf->Ln(5);
        
        // Data da geração
        $this->pdf->SetFont('helvetica', '', 8);
        $this->pdf->Cell(0, 5, 'Gerado em: ' . date('d/m/Y H:i:s'), 0, 1, 'R');
        $this->pdf->Ln(5);
        
        return $this->pdf->Output('S');
    }
    
    public function addTable(array $headers, array $rows, array $widths = []): void
    {
        $this->pdf->SetFont('helvetica', 'B', 9);
        
        // Headers
        $totalWidth = 0;
        foreach ($headers as $index => $header) {
            $width = $widths[$index] ?? (180 / count($headers));
            $totalWidth += $width;
            $this->pdf->Cell($width, 7, $header, 1, 0, 'C', true);
        }
        $this->pdf->Ln();
        
        // Rows
        $this->pdf->SetFont('helvetica', '', 8);
        foreach ($rows as $row) {
            foreach ($row as $index => $cell) {
                $width = $widths[$index] ?? (180 / count($headers));
                $this->pdf->Cell($width, 6, $cell, 1, 0, 'L');
            }
            $this->pdf->Ln();
        }
    }
    
    public function addChart(string $imagePath, int $width = 150, int $height = 100): void
    {
        if (file_exists($imagePath)) {
            $this->pdf->Image($imagePath, '', '', $width, $height);
        }
    }
    
    public function output(string $filename = 'relatorio.pdf', string $dest = 'D'): string
    {
        return $this->pdf->Output($filename, $dest);
    }
}
