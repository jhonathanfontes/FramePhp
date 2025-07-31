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
