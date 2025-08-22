<?php

namespace App\Controllers\Painel;

use Core\Controller\BaseController;
use Core\Http\Request;
use Core\Http\Response;

class BackupController extends BaseController
{
    private $config;

    public function __construct()
    {
        $this->config = [];
    }

    public function index(): string
    {
        $backups = $this->listarBackups();

        return $this->render('painel/backup/index', [
            'active_menu' => 'backup',
            'backups' => $backups
        ]);
    }

    public function executar(Request $request): Response
    {
        try {
            $resultado = $this->criarBackup();
            
            if ($resultado['success']) {
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Backup executado com sucesso!',
                    'arquivo' => $resultado['arquivo'],
                    'tamanho' => $resultado['tamanho']
                ]);
            } else {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Erro ao executar backup: ' . $resultado['error']
                ]);
            }
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro inesperado: ' . $e->getMessage()
            ]);
        }
    }

    public function listar()
    {
        $backups = $this->listarBackups();
        
        return $this->render('painel/backup/listar', [
            'active_menu' => 'backup',
            'backups' => $backups
        ]);
    }

    public function download($nomeArquivo)
    {
        $caminhoBackup = $this->getCaminhoBackup() . '/' . $nomeArquivo;
        
        if (!file_exists($caminhoBackup)) {
            return $this->redirect('/painel/backup')->with('error', 'Arquivo de backup não encontrado');
        }

        // Verificar se o arquivo é um backup válido
        if (!$this->validarArquivoBackup($caminhoBackup)) {
            return $this->redirect('/painel/backup')->with('error', 'Arquivo de backup inválido');
        }

        // Forçar download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $nomeArquivo . '"');
        header('Content-Length: ' . filesize($caminhoBackup));
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        
        readfile($caminhoBackup);
        exit;
    }

    public function restaurar(Request $request, $nomeArquivo)
    {
        $caminhoBackup = $this->getCaminhoBackup() . '/' . $nomeArquivo;
        
        if (!file_exists($caminhoBackup)) {
            return $this->redirect('/painel/backup')->with('error', 'Arquivo de backup não encontrado');
        }

        if ($request->isPost()) {
            $confirmacao = $request->get('confirmacao');
            
            if ($confirmacao !== 'CONFIRMO') {
                return $this->redirect('/painel/backup')->with('error', 'Confirmação incorreta. Digite CONFIRMO para confirmar.');
            }

            try {
                $resultado = $this->restaurarBackup($caminhoBackup);
                
                if ($resultado['success']) {
                    return $this->redirect('/painel/backup')->with('success', 'Backup restaurado com sucesso!');
                } else {
                    return $this->redirect('/painel/backup')->with('error', 'Erro ao restaurar backup: ' . $resultado['error']);
                }
            } catch (\Exception $e) {
                return $this->redirect('/painel/backup')->with('error', 'Erro inesperado: ' . $e->getMessage());
            }
        }

        return $this->render('painel/backup/restaurar', [
            'active_menu' => 'backup',
            'nomeArquivo' => $nomeArquivo,
            'tamanho' => filesize($caminhoBackup),
            'dataCriacao' => date('d/m/Y H:i:s', filemtime($caminhoBackup))
        ]);
    }

    public function excluir($nomeArquivo)
    {
        $caminhoBackup = $this->getCaminhoBackup() . '/' . $nomeArquivo;
        
        if (!file_exists($caminhoBackup)) {
            return $this->redirect('/painel/backup')->with('error', 'Arquivo de backup não encontrado');
        }

        if (unlink($caminhoBackup)) {
            return $this->redirect('/painel/backup')->with('success', 'Backup excluído com sucesso!');
        } else {
            return $this->redirect('/painel/backup')->with('error', 'Erro ao excluir backup');
        }
    }

    private function criarBackup()
    {
        try {
            $caminhoBackup = $this->getCaminhoBackup();
            
            // Criar diretório se não existir
            if (!is_dir($caminhoBackup)) {
                mkdir($caminhoBackup, 0755, true);
            }

            // Nome do arquivo de backup
            $nomeArquivo = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $caminhoCompleto = $caminhoBackup . '/' . $nomeArquivo;

            // Configurações do banco
            $host = config('database.host', 'localhost');
            $porta = config('database.port', '3306');
            $usuario = config('database.username', 'root');
            $senha = config('database.password', '');
            $banco = config('database.database', 'framephp');

            // Comando mysqldump
            $comando = sprintf(
                'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
                escapeshellarg($host),
                escapeshellarg($porta),
                escapeshellarg($usuario),
                escapeshellarg($senha),
                escapeshellarg($banco),
                escapeshellarg($caminhoCompleto)
            );

            // Executar backup
            $output = [];
            $returnVar = 0;
            exec($comando, $output, $returnVar);

            if ($returnVar !== 0) {
                return [
                    'success' => false,
                    'error' => 'Erro ao executar mysqldump'
                ];
            }

            // Verificar se o arquivo foi criado
            if (!file_exists($caminhoCompleto)) {
                return [
                    'success' => false,
                    'error' => 'Arquivo de backup não foi criado'
                ];
            }

            // Comprimir se configurado
            if (config('backup.compressao', true)) {
                $arquivoComprimido = $this->comprimirArquivo($caminhoCompleto);
                if ($arquivoComprimido) {
                    unlink($caminhoCompleto); // Remove arquivo original
                    $caminhoCompleto = $arquivoComprimido;
                }
            }

            // Limpar backups antigos
            $this->limparBackupsAntigos();

            return [
                'success' => true,
                'arquivo' => basename($caminhoCompleto),
                'tamanho' => $this->formatarTamanho(filesize($caminhoCompleto)),
                'caminho' => $caminhoCompleto
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function restaurarBackup($caminhoBackup)
    {
        try {
            // Verificar se é arquivo comprimido
            if (pathinfo($caminhoBackup, PATHINFO_EXTENSION) === 'gz') {
                $caminhoBackup = $this->descomprimirArquivo($caminhoBackup);
                if (!$caminhoBackup) {
                    return ['success' => false, 'error' => 'Erro ao descomprimir arquivo'];
                }
            }

            // Configurações do banco
            $host = config('database.host', 'localhost');
            $porta = config('database.port', '3306');
            $usuario = config('database.username', 'root');
            $senha = config('database.password', '');
            $banco = config('database.database', 'framephp');

            // Comando mysql para restaurar
            $comando = sprintf(
                'mysql --host=%s --port=%s --user=%s --password=%s %s < %s',
                escapeshellarg($host),
                escapeshellarg($porta),
                escapeshellarg($usuario),
                escapeshellarg($senha),
                escapeshellarg($banco),
                escapeshellarg($caminhoBackup)
            );

            // Executar restauração
            $output = [];
            $returnVar = 0;
            exec($comando, $output, $returnVar);

            if ($returnVar !== 0) {
                return [
                    'success' => false,
                    'error' => 'Erro ao restaurar backup'
                ];
            }

            // Limpar arquivo temporário se foi descomprimido
            if (pathinfo($caminhoBackup, PATHINFO_EXTENSION) === 'sql') {
                unlink($caminhoBackup);
            }

            return ['success' => true];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function listarBackups()
    {
        $caminhoBackup = $this->getCaminhoBackup();
        
        if (!is_dir($caminhoBackup)) {
            return [];
        }

        $backups = [];
        $arquivos = glob($caminhoBackup . '/backup_*.{sql,gz}', GLOB_BRACE);
        
        foreach ($arquivos as $arquivo) {
            $backups[] = [
                'nome' => basename($arquivo),
                'tamanho' => $this->formatarTamanho(filesize($arquivo)),
                'data_criacao' => date('d/m/Y H:i:s', filemtime($arquivo)),
                'timestamp' => filemtime($arquivo)
            ];
        }

        // Ordenar por data de criação (mais recente primeiro)
        usort($backups, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });

        return $backups;
    }

    private function limparBackupsAntigos()
    {
        $retencao = $this->config->get('backup.retencao', 30);
        $caminhoBackup = $this->getCaminhoBackup();
        
        if (!is_dir($caminhoBackup)) {
            return;
        }

        $arquivos = glob($caminhoBackup . '/backup_*.{sql,gz}', GLOB_BRACE);
        $limite = time() - ($retencao * 24 * 60 * 60); // Dias em segundos

        foreach ($arquivos as $arquivo) {
            if (filemtime($arquivo) < $limite) {
                unlink($arquivo);
            }
        }
    }

    private function comprimirArquivo($caminhoArquivo)
    {
        $arquivoComprimido = $caminhoArquivo . '.gz';
        
        $handle = gzopen($arquivoComprimido, 'w9');
        if (!$handle) {
            return false;
        }

        $conteudo = file_get_contents($caminhoArquivo);
        gzwrite($handle, $conteudo);
        gzclose($handle);

        return $arquivoComprimido;
    }

    private function descomprimirArquivo($caminhoArquivo)
    {
        $arquivoDescomprimido = str_replace('.gz', '', $caminhoArquivo);
        
        $handle = gzopen($caminhoArquivo, 'r');
        if (!$handle) {
            return false;
        }

        $conteudo = '';
        while (!gzeof($handle)) {
            $conteudo .= gzread($handle, 4096);
        }
        gzclose($handle);

        if (file_put_contents($arquivoDescomprimido, $conteudo) === false) {
            return false;
        }

        return $arquivoDescomprimido;
    }

    private function validarArquivoBackup($caminhoArquivo)
    {
        $extensao = pathinfo($caminhoArquivo, PATHINFO_EXTENSION);
        return in_array($extensao, ['sql', 'gz']);
    }

    private function getCaminhoBackup()
    {
        return $this->config->get('backup.caminho', 'backups');
    }

    private function formatarTamanho($bytes)
    {
        $unidades = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($unidades) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $unidades[$pow];
    }

    protected function jsonResponse(array $dados, int $statusCode = 200): void  
    {
        parent::jsonResponse($dados, $statusCode);
    }
} 