<?php

namespace App\Controllers\Backend\Painel;

use Core\Controller\BaseController;
use Core\Http\Request;
use Core\Http\Response;
use Core\Config\Config;
use Core\Cache\Cache;

class ConfiguracoesBackendController extends BaseController
{
    private $config;
    private $cache;

    public function __construct()
    {
        $this->config = new Config();
        $this->cache = new Cache();
    }

    /**
     * API para obter todas as configurações
     */
    public function index(): Response
    {
        try {
            $configuracoes = [
                'geral' => $this->carregarConfiguracoesGerais(),
                'usuarios' => $this->carregarConfiguracoesUsuarios(),
                'empresas' => $this->carregarConfiguracoesEmpresas(),
                'seguranca' => $this->carregarConfiguracoesSeguranca(),
                'notificacoes' => $this->carregarConfiguracoesNotificacoes(),
                'backup' => $this->carregarConfiguracoesBackup(),
                'sistema' => $this->carregarConfiguracoesSistema()
            ];

            return $this->jsonResponse([
                'success' => true,
                'data' => $configuracoes
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao carregar configurações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para obter configurações de uma seção específica
     */
    public function show($secao): Response
    {
        try {
            $configuracoes = $this->carregarConfiguracoesSecao($secao);

            if ($configuracoes === null) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Seção de configuração não encontrada'
                ], 404);
            }

            return $this->jsonResponse([
                'success' => true,
                'data' => $configuracoes
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao carregar configurações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para atualizar configurações
     */
    public function update(Request $request, $secao): Response
    {
        try {
            $dados = $request->all();
            
            if (empty($dados)) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Nenhum dado fornecido para atualização'
                ], 422);
            }

            // Validar seção
            $secoesValidas = ['geral', 'usuarios', 'empresas', 'seguranca', 'notificacoes', 'backup', 'sistema'];
            if (!in_array($secao, $secoesValidas)) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Seção de configuração inválida'
                ], 422);
            }

            // Validar dados específicos da seção
            $validacao = $this->validarConfiguracoes($secao, $dados);
            if (!$validacao['valido']) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validacao['erros']
                ], 422);
            }

            // Salvar configurações
            if ($this->salvarConfiguracoes($secao, $dados)) {
                // Limpar cache de configurações
                $this->cache->delete('config_' . $secao);
                
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Configurações atualizadas com sucesso'
                ]);
            }

            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao salvar configurações'
            ], 500);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao atualizar configurações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para redefinir configurações para padrão
     */
    public function reset($secao): Response
    {
        try {
            $configuracoesPadrao = $this->getConfiguracoesPadrao($secao);
            
            if ($configuracoesPadrao === null) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Seção de configuração não encontrada'
                ], 404);
            }

            if ($this->salvarConfiguracoes($secao, $configuracoesPadrao)) {
                // Limpar cache
                $this->cache->delete('config_' . $secao);
                
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Configurações redefinidas para padrão com sucesso'
                ]);
            }

            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao redefinir configurações'
            ], 500);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao redefinir configurações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para exportar configurações
     */
    public function exportar($secao = null): Response
    {
        try {
            if ($secao) {
                $configuracoes = $this->carregarConfiguracoesSecao($secao);
                $nomeArquivo = "config_{$secao}.json";
            } else {
                $configuracoes = [
                    'geral' => $this->carregarConfiguracoesGerais(),
                    'usuarios' => $this->carregarConfiguracoesUsuarios(),
                    'empresas' => $this->carregarConfiguracoesEmpresas(),
                    'seguranca' => $this->carregarConfiguracoesSeguranca(),
                    'notificacoes' => $this->carregarConfiguracoesNotificacoes(),
                    'backup' => $this->carregarConfiguracoesBackup(),
                    'sistema' => $this->carregarConfiguracoesSistema()
                ];
                $nomeArquivo = "config_completo.json";
            }

            return new Response(json_encode($configuracoes, JSON_PRETTY_PRINT), 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $nomeArquivo . '"'
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao exportar configurações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para importar configurações
     */
    public function importar(Request $request): Response
    {
        try {
            $arquivo = $request->getFile('arquivo');
            
            if (!$arquivo) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Nenhum arquivo fornecido'
                ], 422);
            }

            // Validar tipo de arquivo
            if ($arquivo->getMimeType() !== 'application/json') {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Arquivo deve ser no formato JSON'
                ], 422);
            }

            // Ler conteúdo do arquivo
            $conteudo = file_get_contents($arquivo->getPathname());
            $configuracoes = json_decode($conteudo, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Arquivo JSON inválido'
                ], 422);
            }

            // Validar estrutura das configurações
            $validacao = $this->validarEstruturaConfiguracoes($configuracoes);
            if (!$validacao['valido']) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Estrutura de configurações inválida',
                    'errors' => $validacao['erros']
                ], 422);
            }

            // Salvar configurações
            $sucessos = 0;
            $erros = 0;

            foreach ($configuracoes as $secao => $dados) {
                if ($this->salvarConfiguracoes($secao, $dados)) {
                    $this->cache->delete('config_' . $secao);
                    $sucessos++;
                } else {
                    $erros++;
                }
            }

            return $this->jsonResponse([
                'success' => true,
                'message' => "Configurações importadas: {$sucessos} sucessos, {$erros} erros",
                'data' => [
                    'sucessos' => $sucessos,
                    'erros' => $erros
                ]
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao importar configurações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Métodos privados para carregar configurações
     */
    private function carregarConfiguracoesSecao($secao)
    {
        switch ($secao) {
            case 'geral':
                return $this->carregarConfiguracoesGerais();
            case 'usuarios':
                return $this->carregarConfiguracoesUsuarios();
            case 'empresas':
                return $this->carregarConfiguracoesEmpresas();
            case 'seguranca':
                return $this->carregarConfiguracoesSeguranca();
            case 'notificacoes':
                return $this->carregarConfiguracoesNotificacoes();
            case 'backup':
                return $this->carregarConfiguracoesBackup();
            case 'sistema':
                return $this->carregarConfiguracoesSistema();
            default:
                return null;
        }
    }

    private function carregarConfiguracoesGerais()
    {
        return [
            'nome_sistema' => $this->config->get('app.nome', 'Sistema de Gestão Empresarial'),
            'versao_sistema' => $this->config->get('app.versao', '1.0.0'),
            'timezone' => $this->config->get('app.timezone', 'America/Sao_Paulo'),
            'idioma' => $this->config->get('app.idioma', 'pt_BR'),
            'registros_por_pagina' => $this->config->get('app.registros_por_pagina', '25'),
            'modo_manutencao' => $this->config->get('app.modo_manutencao', false),
            'debug' => $this->config->get('app.debug', false)
        ];
    }

    private function carregarConfiguracoesUsuarios()
    {
        return [
            'min_senha' => $this->config->get('usuarios.min_senha', 8),
            'expiracao_senha' => $this->config->get('usuarios.expiracao_senha', 90),
            'tentativas_login' => $this->config->get('usuarios.tentativas_login', 5),
            'bloqueio_temporario' => $this->config->get('usuarios.bloqueio_temporario', 30),
            'requer_maiuscula' => $this->config->get('usuarios.requer_maiuscula', true),
            'requer_minuscula' => $this->config->get('usuarios.requer_minuscula', true),
            'requer_numero' => $this->config->get('usuarios.requer_numero', true),
            'requer_caractere_especial' => $this->config->get('usuarios.requer_caractere_especial', false)
        ];
    }

    private function carregarConfiguracoesEmpresas()
    {
        return [
            'max_empresas' => $this->config->get('empresas.max_empresas', 10),
            'max_estabelecimentos' => $this->config->get('empresas.max_estabelecimentos', 50),
            'validacao_cnpj' => $this->config->get('empresas.validacao_cnpj', true),
            'consulta_receita' => $this->config->get('empresas.consulta_receita', false),
            'auto_aprovacao' => $this->config->get('empresas.auto_aprovacao', false)
        ];
    }

    private function carregarConfiguracoesSeguranca()
    {
        return [
            'sessao_timeout' => $this->config->get('seguranca.sessao_timeout', 30),
            'max_sessoes' => $this->config->get('seguranca.max_sessoes', 3),
            'log_acesso' => $this->config->get('seguranca.log_acesso', true),
            'log_alteracoes' => $this->config->get('seguranca.log_alteracoes', true),
            'ips_permitidos' => $this->config->get('seguranca.ips_permitidos', ''),
            'https_obrigatorio' => $this->config->get('seguranca.https_obrigatorio', false),
            'headers_seguranca' => $this->config->get('seguranca.headers_seguranca', true)
        ];
    }

    private function carregarConfiguracoesNotificacoes()
    {
        return [
            'email_novo_usuario' => $this->config->get('notificacoes.email_novo_usuario', true),
            'email_nova_empresa' => $this->config->get('notificacoes.email_nova_empresa', true),
            'notif_sistema' => $this->config->get('notificacoes.notif_sistema', true),
            'email_remetente' => $this->config->get('notificacoes.email_remetente', 'noreply@sistema.com'),
            'nome_remetente' => $this->config->get('notificacoes.nome_remetente', 'Sistema'),
            'smtp_host' => $this->config->get('notificacoes.smtp_host', ''),
            'smtp_porta' => $this->config->get('notificacoes.smtp_porta', 587),
            'smtp_usuario' => $this->config->get('notificacoes.smtp_usuario', ''),
            'smtp_senha' => $this->config->get('notificacoes.smtp_senha', '')
        ];
    }

    private function carregarConfiguracoesBackup()
    {
        return [
            'backup_automatico' => $this->config->get('backup.backup_automatico', false),
            'frequencia' => $this->config->get('backup.frequencia', 'semanal'),
            'retencao' => $this->config->get('backup.retencao', 30),
            'compressao' => $this->config->get('backup.compressao', true),
            'caminho' => $this->config->get('backup.caminho', '/backups'),
            'notificar_erro' => $this->config->get('backup.notificar_erro', true),
            'backup_arquivos' => $this->config->get('backup.backup_arquivos', false)
        ];
    }

    private function carregarConfiguracoesSistema()
    {
        return [
            'ambiente' => $this->config->get('sistema.ambiente', 'producao'),
            'log_level' => $this->config->get('sistema.log_level', 'info'),
            'cache_driver' => $this->config->get('sistema.cache_driver', 'file'),
            'session_driver' => $this->config->get('sistema.session_driver', 'file'),
            'queue_driver' => $this->config->get('sistema.queue_driver', 'sync'),
            'max_upload_size' => $this->config->get('sistema.max_upload_size', '10MB'),
            'manutencao_mensagem' => $this->config->get('sistema.manutencao_mensagem', 'Sistema em manutenção')
        ];
    }

    /**
     * Métodos privados para validação e salvamento
     */
    private function validarConfiguracoes($secao, $dados)
    {
        $erros = [];
        
        switch ($secao) {
            case 'geral':
                if (isset($dados['nome_sistema']) && strlen($dados['nome_sistema']) < 3) {
                    $erros[] = 'Nome do sistema deve ter pelo menos 3 caracteres';
                }
                if (isset($dados['registros_por_pagina']) && !in_array($dados['registros_por_pagina'], [10, 25, 50, 100])) {
                    $erros[] = 'Registros por página deve ser 10, 25, 50 ou 100';
                }
                break;
                
            case 'usuarios':
                if (isset($dados['min_senha']) && ($dados['min_senha'] < 6 || $dados['min_senha'] > 20)) {
                    $erros[] = 'Tamanho mínimo da senha deve ser entre 6 e 20 caracteres';
                }
                if (isset($dados['expiracao_senha']) && $dados['expiracao_senha'] < 0) {
                    $erros[] = 'Expiração da senha não pode ser negativa';
                }
                break;
                
            case 'empresas':
                if (isset($dados['max_empresas']) && $dados['max_empresas'] < 1) {
                    $erros[] = 'Máximo de empresas deve ser pelo menos 1';
                }
                if (isset($dados['max_estabelecimentos']) && $dados['max_estabelecimentos'] < 1) {
                    $erros[] = 'Máximo de estabelecimentos deve ser pelo menos 1';
                }
                break;
                
            case 'seguranca':
                if (isset($dados['sessao_timeout']) && ($dados['sessao_timeout'] < 5 || $dados['sessao_timeout'] > 480)) {
                    $erros[] = 'Timeout da sessão deve ser entre 5 e 480 minutos';
                }
                if (isset($dados['max_sessoes']) && ($dados['max_sessoes'] < 1 || $dados['max_sessoes'] > 10)) {
                    $erros[] = 'Máximo de sessões deve ser entre 1 e 10';
                }
                break;
        }

        return [
            'valido' => empty($erros),
            'erros' => $erros
        ];
    }

    private function validarEstruturaConfiguracoes($configuracoes)
    {
        $erros = [];
        $secoesValidas = ['geral', 'usuarios', 'empresas', 'seguranca', 'notificacoes', 'backup', 'sistema'];
        
        foreach ($configuracoes as $secao => $dados) {
            if (!in_array($secao, $secoesValidas)) {
                $erros[] = "Seção '{$secao}' não é válida";
            }
            
            if (!is_array($dados)) {
                $erros[] = "Dados da seção '{$secao}' devem ser um array";
            }
        }

        return [
            'valido' => empty($erros),
            'erros' => $erros
        ];
    }

    private function salvarConfiguracoes($secao, $dados): bool
    {
        try {
            foreach ($dados as $chave => $valor) {
                $this->config->set("{$secao}.{$chave}", $valor);
            }
            return true;
        } catch (\Exception $e) {
            error_log("Erro ao salvar configurações: " . $e->getMessage());
            return false;
        }
    }

    private function getConfiguracoesPadrao($secao)
    {
        switch ($secao) {
            case 'geral':
                return [
                    'nome_sistema' => 'Sistema de Gestão Empresarial',
                    'versao_sistema' => '1.0.0',
                    'timezone' => 'America/Sao_Paulo',
                    'idioma' => 'pt_BR',
                    'registros_por_pagina' => '25',
                    'modo_manutencao' => false,
                    'debug' => false
                ];
            case 'usuarios':
                return [
                    'min_senha' => 8,
                    'expiracao_senha' => 90,
                    'tentativas_login' => 5,
                    'bloqueio_temporario' => 30,
                    'requer_maiuscula' => true,
                    'requer_minuscula' => true,
                    'requer_numero' => true,
                    'requer_caractere_especial' => false
                ];
            // Adicionar outras seções conforme necessário
            default:
                return null;
        }
    }

    private function jsonResponse($dados, $statusCode = 200): Response
    {
        return new Response(json_encode($dados), $statusCode, [
            'Content-Type' => 'application/json'
        ]);
    }
} 