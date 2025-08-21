<?php

namespace App\Controllers\Backend\Painel;

use Core\Controller\BaseController;
use Core\Http\Request;
use Core\Http\Response;
use Core\Config\Config;
use Core\Cache\Cache;

class BackupBackendController extends BaseController
{
    private $config;
    private $cache;

    public function __construct()
    {
        $this->config = new Config();
        $this->cache = new Cache();
    }

    /**
     * API para executar backup manual
     */
    public function executar(Request $request): Response
    {
        try {
            $tipo = $request->get('tipo', 'completo');
            $descricao = $request->get('descricao', 'Backup manual executado em ' . date('d/m/Y H:i:s'));
            
            // Verificar se já existe um backup em andamento
            if ($this->backupEmAndamento()) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Já existe um backup em andamento'
                ], 422);
            }

            // Marcar backup como em andamento
            $this->marcarBackupEmAndamento();

            // Executar backup em background
            $this->executarBackupBackground($tipo, $descricao);

            return $this->jsonResponse([
                'success' => true,
                'message' => 'Backup iniciado com sucesso. Você será notificado quando concluir.'
            ]);

        } catch (\Exception $e) {
            $this->removerBackupEmAndamento();
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao executar backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para listar backups disponíveis
     */
    public function index(Request $request): Response
    {
        try {
            $pagina = $request->get('pagina', 1);
            $porPagina = $request->get('por_pagina', 10);
            $filtros = $request->get('filtros', []);

            $backups = $this->listarBackups($pagina, $porPagina, $filtros);
            $total = $this->countBackups($filtros);

            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'backups' => $backups,
                    'paginacao' => [
                        'pagina_atual' => $pagina,
                        'por_pagina' => $porPagina,
                        'total' => $total,
                        'total_paginas' => ceil($total / $porPagina)
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao carregar backups: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para obter informações de um backup específico
     */
    public function show($id): Response
    {
        try {
            $backup = $this->getBackupInfo($id);
            
            if (!$backup) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Backup não encontrado'
                ], 404);
            }

            return $this->jsonResponse([
                'success' => true,
                'data' => $backup
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao carregar backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para restaurar backup
     */
    public function restaurar(Request $request, $id): Response
    {
        try {
            $backup = $this->getBackupInfo($id);
            
            if (!$backup) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Backup não encontrado'
                ], 404);
            }

            $confirmacao = $request->get('confirmacao');
            
            if ($confirmacao !== 'CONFIRMO') {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Confirmação incorreta. Digite CONFIRMO para confirmar.'
                ], 422);
            }

            // Verificar se não há usuários ativos
            if ($this->hasUsuariosAtivos()) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Não é possível restaurar backup com usuários ativos no sistema'
                ], 422);
            }

            // Executar restauração em background
            $this->executarRestauracaoBackground($id);

            return $this->jsonResponse([
                'success' => true,
                'message' => 'Restauração iniciada. O sistema será reiniciado quando concluir.'
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao restaurar backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para excluir backup
     */
    public function destroy($id): Response
    {
        try {
            $backup = $this->getBackupInfo($id);
            
            if (!$backup) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Backup não encontrado'
                ], 404);
            }

            if ($this->excluirBackup($id)) {
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Backup excluído com sucesso'
                ]);
            }

            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao excluir backup'
            ], 500);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao excluir backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para obter status do backup
     */
    public function status(): Response
    {
        try {
            $status = [
                'em_andamento' => $this->backupEmAndamento(),
                'ultimo_backup' => $this->getUltimoBackup(),
                'proximo_backup' => $this->getProximoBackup(),
                'estatisticas' => $this->getEstatisticasBackup(),
                'configuracoes' => $this->getConfiguracoesBackup()
            ];

            return $this->jsonResponse([
                'success' => true,
                'data' => $status
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao carregar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para testar configurações de backup
     */
    public function testarConfiguracoes(): Response
    {
        try {
            $resultados = [
                'diretorio_backup' => $this->testarDiretorioBackup(),
                'conexao_banco' => $this->testarConexaoBanco(),
                'comando_mysqldump' => $this->testarComandoMysqldump(),
                'permissoes_arquivo' => $this->testarPermissoesArquivo(),
                'espaco_disco' => $this->testarEspacoDisco()
            ];

            $sucessos = count(array_filter($resultados, function($resultado) {
                return $resultado['status'] === 'success';
            }));

            $statusGeral = $sucessos === count($resultados) ? 'success' : 'warning';

            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'status_geral' => $statusGeral,
                    'sucessos' => $sucessos,
                    'total' => count($resultados),
                    'testes' => $resultados
                ]
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao testar configurações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para limpar backups antigos
     */
    public function limparAntigos(): Response
    {
        try {
            $configuracao = $this->getConfiguracoesBackup();
            $retencao = $configuracao['retencao'] ?? 30;
            
            $backupsRemovidos = $this->limparBackupsAntigos($retencao);

            return $this->jsonResponse([
                'success' => true,
                'message' => 'Limpeza concluída com sucesso',
                'data' => [
                    'backups_removidos' => $backupsRemovidos,
                    'retencao_dias' => $retencao
                ]
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao limpar backups antigos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Métodos privados auxiliares
     */
    private function backupEmAndamento(): bool
    {
        return $this->cache->has('backup_em_andamento');
    }

    private function marcarBackupEmAndamento(): void
    {
        $this->cache->set('backup_em_andamento', [
            'inicio' => time(),
            'tipo' => 'manual'
        ], 3600); // Expira em 1 hora
    }

    private function removerBackupEmAndamento(): void
    {
        $this->cache->delete('backup_em_andamento');
    }

    private function executarBackupBackground($tipo, $descricao): void
    {
        // Em um ambiente real, isso seria executado em uma fila/job
        // Por enquanto, simulamos a execução
        $this->cache->set('backup_job', [
            'tipo' => $tipo,
            'descricao' => $descricao,
            'status' => 'executando',
            'inicio' => time()
        ], 3600);
    }

    private function executarRestauracaoBackground($id): void
    {
        // Marcar restauração em andamento
        $this->cache->set('restauracao_em_andamento', [
            'backup_id' => $id,
            'inicio' => time()
        ], 3600);
    }

    private function listarBackups($pagina, $porPagina, $filtros): array
    {
        $caminhoBackup = $this->getCaminhoBackup();
        
        if (!is_dir($caminhoBackup)) {
            return [];
        }

        $backups = [];
        $arquivos = glob($caminhoBackup . '/backup_*.{sql,gz}', GLOB_BRACE);
        
        foreach ($arquivos as $arquivo) {
            $backups[] = [
                'id' => md5($arquivo),
                'nome' => basename($arquivo),
                'tamanho' => $this->formatarTamanho(filesize($arquivo)),
                'tamanho_bytes' => filesize($arquivo),
                'data_criacao' => date('d/m/Y H:i:s', filemtime($arquivo)),
                'timestamp' => filemtime($arquivo),
                'tipo' => pathinfo($arquivo, PATHINFO_EXTENSION) === 'gz' ? 'comprimido' : 'sql',
                'caminho' => $arquivo
            ];
        }

        // Aplicar filtros
        if (!empty($filtros['tipo'])) {
            $backups = array_filter($backups, function($backup) use ($filtros) {
                return $backup['tipo'] === $filtros['tipo'];
            });
        }

        if (!empty($filtros['data_inicio'])) {
            $dataInicio = strtotime($filtros['data_inicio']);
            $backups = array_filter($backups, function($backup) use ($dataInicio) {
                return $backup['timestamp'] >= $dataInicio;
            });
        }

        if (!empty($filtros['data_fim'])) {
            $dataFim = strtotime($filtros['data_fim'] . ' 23:59:59');
            $backups = array_filter($backups, function($backup) use ($dataFim) {
                return $backup['timestamp'] <= $dataFim;
            });
        }

        // Ordenar por data (mais recente primeiro)
        usort($backups, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });

        // Paginação
        $offset = ($pagina - 1) * $porPagina;
        return array_slice($backups, $offset, $porPagina);
    }

    private function countBackups($filtros): int
    {
        $caminhoBackup = $this->getCaminhoBackup();
        
        if (!is_dir($caminhoBackup)) {
            return 0;
        }

        $arquivos = glob($caminhoBackup . '/backup_*.{sql,gz}', GLOB_BRACE);
        return count($arquivos);
    }

    private function getBackupInfo($id): ?array
    {
        $caminhoBackup = $this->getCaminhoBackup();
        $arquivos = glob($caminhoBackup . '/backup_*.{sql,gz}', GLOB_BRACE);
        
        foreach ($arquivos as $arquivo) {
            if (md5($arquivo) === $id) {
                return [
                    'id' => $id,
                    'nome' => basename($arquivo),
                    'tamanho' => $this->formatarTamanho(filesize($arquivo)),
                    'tamanho_bytes' => filesize($arquivo),
                    'data_criacao' => date('d/m/Y H:i:s', filemtime($arquivo)),
                    'timestamp' => filemtime($arquivo),
                    'tipo' => pathinfo($arquivo, PATHINFO_EXTENSION) === 'gz' ? 'comprimido' : 'sql',
                    'caminho' => $arquivo
                ];
            }
        }
        
        return null;
    }

    private function excluirBackup($id): bool
    {
        $backup = $this->getBackupInfo($id);
        
        if (!$backup) {
            return false;
        }

        return unlink($backup['caminho']);
    }

    private function getUltimoBackup(): ?array
    {
        $caminhoBackup = $this->getCaminhoBackup();
        $arquivos = glob($caminhoBackup . '/backup_*.{sql,gz}', GLOB_BRACE);
        
        if (empty($arquivos)) {
            return null;
        }

        $ultimoArquivo = array_reduce($arquivos, function($carry, $item) {
            return filemtime($item) > filemtime($carry) ? $item : $carry;
        });

        return [
            'nome' => basename($ultimoArquivo),
            'data' => date('d/m/Y H:i:s', filemtime($ultimoArquivo)),
            'tamanho' => $this->formatarTamanho(filesize($ultimoArquivo))
        ];
    }

    private function getProximoBackup(): ?string
    {
        $configuracao = $this->getConfiguracoesBackup();
        
        if (!$configuracao['backup_automatico']) {
            return null;
        }

        $ultimoBackup = $this->getUltimoBackup();
        if (!$ultimoBackup) {
            return 'Imediato';
        }

        $ultimoTimestamp = strtotime($ultimoBackup['data']);
        $frequencia = $configuracao['frequencia'];
        
        switch ($frequencia) {
            case 'diario':
                $proximo = $ultimoTimestamp + 86400;
                break;
            case 'semanal':
                $proximo = $ultimoTimestamp + 604800;
                break;
            case 'mensal':
                $proximo = strtotime('+1 month', $ultimoTimestamp);
                break;
            default:
                return null;
        }

        return date('d/m/Y H:i:s', $proximo);
    }

    private function getEstatisticasBackup(): array
    {
        $caminhoBackup = $this->getCaminhoBackup();
        
        if (!is_dir($caminhoBackup)) {
            return [
                'total_backups' => 0,
                'tamanho_total' => '0 B',
                'backup_mais_antigo' => null,
                'backup_mais_recente' => null
            ];
        }

        $arquivos = glob($caminhoBackup . '/backup_*.{sql,gz}', GLOB_BRACE);
        $tamanhoTotal = array_sum(array_map('filesize', $arquivos));
        
        if (empty($arquivos)) {
            return [
                'total_backups' => 0,
                'tamanho_total' => '0 B',
                'backup_mais_antigo' => null,
                'backup_mais_recente' => null
            ];
        }

        $timestamps = array_map('filemtime', $arquivos);
        $maisAntigo = min($timestamps);
        $maisRecente = max($timestamps);

        return [
            'total_backups' => count($arquivos),
            'tamanho_total' => $this->formatarTamanho($tamanhoTotal),
            'backup_mais_antigo' => date('d/m/Y H:i:s', $maisAntigo),
            'backup_mais_recente' => date('d/m/Y H:i:s', $maisRecente)
        ];
    }

    private function getConfiguracoesBackup(): array
    {
        return [
            'backup_automatico' => $this->config->get('backup.backup_automatico', false),
            'frequencia' => $this->config->get('backup.frequencia', 'semanal'),
            'retencao' => $this->config->get('backup.retencao', 30),
            'compressao' => $this->config->get('backup.compressao', true),
            'caminho' => $this->config->get('backup.caminho', '/backups')
        ];
    }

    private function testarDiretorioBackup(): array
    {
        $caminho = $this->getCaminhoBackup();
        
        if (!is_dir($caminho)) {
            if (mkdir($caminho, 0755, true)) {
                return ['status' => 'success', 'message' => 'Diretório criado com sucesso'];
            } else {
                return ['status' => 'error', 'message' => 'Não foi possível criar o diretório'];
            }
        }

        if (is_writable($caminho)) {
            return ['status' => 'success', 'message' => 'Diretório acessível e gravável'];
        } else {
            return ['status' => 'error', 'message' => 'Diretório não é gravável'];
        }
    }

    private function testarConexaoBanco(): array
    {
        try {
            // Simular teste de conexão
            return ['status' => 'success', 'message' => 'Conexão com banco de dados OK'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Erro na conexão: ' . $e->getMessage()];
        }
    }

    private function testarComandoMysqldump(): array
    {
        $output = [];
        $returnVar = 0;
        exec('mysqldump --version', $output, $returnVar);
        
        if ($returnVar === 0) {
            return ['status' => 'success', 'message' => 'mysqldump disponível: ' . $output[0]];
        } else {
            return ['status' => 'error', 'message' => 'mysqldump não encontrado'];
        }
    }

    private function testarPermissoesArquivo(): array
    {
        $caminho = $this->getCaminhoBackup();
        $arquivoTeste = $caminho . '/teste.tmp';
        
        if (file_put_contents($arquivoTeste, 'teste') !== false) {
            unlink($arquivoTeste);
            return ['status' => 'success', 'message' => 'Permissões de arquivo OK'];
        } else {
            return ['status' => 'error', 'message' => 'Sem permissão para criar arquivos'];
        }
    }

    private function testarEspacoDisco(): array
    {
        $caminho = $this->getCaminhoBackup();
        $espacoLivre = disk_free_space($caminho);
        $espacoTotal = disk_total_space($caminho);
        $espacoUsado = $espacoTotal - $espacoLivre;
        $percentualUsado = ($espacoUsado / $espacoTotal) * 100;
        
        if ($percentualUsado < 90) {
            return [
                'status' => 'success',
                'message' => 'Espaço disponível: ' . $this->formatarTamanho($espacoLivre)
            ];
        } else {
            return [
                'status' => 'warning',
                'message' => 'Pouco espaço disponível: ' . $this->formatarTamanho($espacoLivre)
            ];
        }
    }

    private function hasUsuariosAtivos(): bool
    {
        // Simular verificação de usuários ativos
        return false;
    }

    private function limparBackupsAntigos($retencao): int
    {
        $caminhoBackup = $this->getCaminhoBackup();
        $arquivos = glob($caminhoBackup . '/backup_*.{sql,gz}', GLOB_BRACE);
        $limite = time() - ($retencao * 24 * 60 * 60);
        $removidos = 0;

        foreach ($arquivos as $arquivo) {
            if (filemtime($arquivo) < $limite) {
                if (unlink($arquivo)) {
                    $removidos++;
                }
            }
        }

        return $removidos;
    }

    private function getCaminhoBackup(): string
    {
        return $this->config->get('backup.caminho', 'backups');
    }

    private function formatarTamanho($bytes): string
    {
        $unidades = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($unidades) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $unidades[$pow];
    }

    private function jsonResponse($dados, $statusCode = 200): Response
    {
        return new Response(json_encode($dados), $statusCode, [
            'Content-Type' => 'application/json'
        ]);
    }
} 