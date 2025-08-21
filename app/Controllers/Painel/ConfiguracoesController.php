<?php

namespace App\Controllers\Painel;

use Core\Controller\BaseController;
use Core\Http\Request;
use Core\Http\Response;
use Core\Config\Config;

class ConfiguracoesController extends BaseController
{
    private $config;

    public function __construct()
    {
        $this->config = new Config();
    }

    public function index()
    {
        // Carregar configurações atuais
        $config = [
            'geral' => $this->carregarConfiguracoesGerais(),
            'usuarios' => $this->carregarConfiguracoesUsuarios(),
            'empresas' => $this->carregarConfiguracoesEmpresas(),
            'seguranca' => $this->carregarConfiguracoesSeguranca(),
            'notificacoes' => $this->carregarConfiguracoesNotificacoes(),
            'backup' => $this->carregarConfiguracoesBackup()
        ];

        return $this->render('painel/configuracoes', [
            'active_menu' => 'configuracoes',
            'config' => $config
        ]);
    }

    public function geral(Request $request): Response
    {
        if ($request->isPost()) {
            $dados = [
                'nome_sistema' => $request->get('nome_sistema'),
                'versao_sistema' => $request->get('versao_sistema'),
                'timezone' => $request->get('timezone'),
                'idioma' => $request->get('idioma'),
                'registros_por_pagina' => $request->get('registros_por_pagina'),
                'modo_manutencao' => $request->has('manutencao')
            ];

            if ($this->salvarConfiguracoes('geral', $dados)) {
                return $this->redirect('/painel/configuracoes')->with('success', 'Configurações gerais salvas com sucesso!');
            }

            return $this->redirect('/painel/configuracoes')->with('error', 'Erro ao salvar configurações');
        }

        return $this->redirect('/painel/configuracoes');
    }

    public function usuarios(Request $request): Response
    {
        if ($request->isPost()) {
            $dados = [
                'min_senha' => $request->get('min_senha'),
                'expiracao_senha' => $request->get('expiracao_senha'),
                'tentativas_login' => $request->get('tentativas_login'),
                'bloqueio_temporario' => $request->get('bloqueio_temporario'),
                'requer_maiuscula' => $request->has('maiuscula'),
                'requer_minuscula' => $request->has('minuscula'),
                'requer_numero' => $request->has('numero'),
                'requer_caractere_especial' => $request->has('caractere_especial')
            ];

            if ($this->salvarConfiguracoes('usuarios', $dados)) {
                return $this->redirect('/painel/configuracoes')->with('success', 'Configurações de usuários salvas com sucesso!');
            }

            return $this->redirect('/painel/configuracoes')->with('error', 'Erro ao salvar configurações');
        }

        return $this->redirect('/painel/configuracoes');
    }

    public function empresas(Request $request): Response
    {
        if ($request->isPost()) {
            $dados = [
                'max_empresas' => $request->get('max_empresas'),
                'max_estabelecimentos' => $request->get('max_estabelecimentos'),
                'validacao_cnpj' => $request->has('validacao_cnpj'),
                'consulta_receita' => $request->has('consulta_receita')
            ];

            if ($this->salvarConfiguracoes('empresas', $dados)) {
                return $this->redirect('/painel/configuracoes')->with('success', 'Configurações de empresas salvas com sucesso!');
            }

            return $this->redirect('/painel/configuracoes')->with('error', 'Erro ao salvar configurações');
        }

        return $this->redirect('/painel/configuracoes');
    }

    public function seguranca(Request $request): Response
    {
        if ($request->isPost()) {
            $dados = [
                'sessao_timeout' => $request->get('sessao_timeout'),
                'max_sessoes' => $request->get('max_sessoes'),
                'log_acesso' => $request->has('log_acesso'),
                'log_alteracoes' => $request->has('log_alteracoes'),
                'ips_permitidos' => $request->get('ips_permitidos')
            ];

            if ($this->salvarConfiguracoes('seguranca', $dados)) {
                return $this->redirect('/painel/configuracoes')->with('success', 'Configurações de segurança salvas com sucesso!');
            }

            return $this->redirect('/painel/configuracoes')->with('error', 'Erro ao salvar configurações');
        }

        return $this->redirect('/painel/configuracoes');
    }

    public function notificacoes(Request $request): Response
    {
        if ($request->isPost()) {
            $dados = [
                'email_novo_usuario' => $request->has('email_novo_usuario'),
                'email_nova_empresa' => $request->has('email_nova_empresa'),
                'notif_sistema' => $request->has('notif_sistema'),
                'email_remetente' => $request->get('email_remetente'),
                'nome_remetente' => $request->get('nome_remetente')
            ];

            if ($this->salvarConfiguracoes('notificacoes', $dados)) {
                return $this->redirect('/painel/configuracoes')->with('success', 'Configurações de notificações salvas com sucesso!');
            }

            return $this->redirect('/painel/configuracoes')->with('error', 'Erro ao salvar configurações');
        }

        return $this->redirect('/painel/configuracoes');
    }

    public function backup(Request $request): Response
    {
        if ($request->isPost()) {
            $dados = [
                'backup_automatico' => $request->has('backup_automatico'),
                'frequencia' => $request->get('frequencia_backup'),
                'retencao' => $request->get('retencao_backup'),
                'compressao' => $request->has('compressao_backup'),
                'caminho' => $request->get('caminho_backup')
            ];

            if ($this->salvarConfiguracoes('backup', $dados)) {
                return $this->redirect('/painel/configuracoes')->with('success', 'Configurações de backup salvas com sucesso!');
            }

            return $this->redirect('/painel/configuracoes')->with('error', 'Erro ao salvar configurações');
        }

        return $this->redirect('/painel/configuracoes');
    }

    private function carregarConfiguracoesGerais()
    {
        return [
            'nome_sistema' => $this->config->get('app.nome', 'Sistema de Gestão Empresarial'),
            'versao_sistema' => $this->config->get('app.versao', '1.0.0'),
            'timezone' => $this->config->get('app.timezone', 'America/Sao_Paulo'),
            'idioma' => $this->config->get('app.idioma', 'pt_BR'),
            'registros_por_pagina' => $this->config->get('app.registros_por_pagina', '25'),
            'modo_manutencao' => $this->config->get('app.modo_manutencao', false)
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
            'consulta_receita' => $this->config->get('empresas.consulta_receita', false)
        ];
    }

    private function carregarConfiguracoesSeguranca()
    {
        return [
            'sessao_timeout' => $this->config->get('seguranca.sessao_timeout', 30),
            'max_sessoes' => $this->config->get('seguranca.max_sessoes', 3),
            'log_acesso' => $this->config->get('seguranca.log_acesso', true),
            'log_alteracoes' => $this->config->get('seguranca.log_alteracoes', true),
            'ips_permitidos' => $this->config->get('seguranca.ips_permitidos', '')
        ];
    }

    private function carregarConfiguracoesNotificacoes()
    {
        return [
            'email_novo_usuario' => $this->config->get('notificacoes.email_novo_usuario', true),
            'email_nova_empresa' => $this->config->get('notificacoes.email_nova_empresa', true),
            'notif_sistema' => $this->config->get('notificacoes.notif_sistema', true),
            'email_remetente' => $this->config->get('notificacoes.email_remetente', 'noreply@sistema.com'),
            'nome_remetente' => $this->config->get('notificacoes.nome_remetente', 'Sistema')
        ];
    }

    private function carregarConfiguracoesBackup()
    {
        return [
            'backup_automatico' => $this->config->get('backup.backup_automatico', false),
            'frequencia' => $this->config->get('backup.frequencia', 'semanal'),
            'retencao' => $this->config->get('backup.retencao', 30),
            'compressao' => $this->config->get('backup.compressao', true),
            'caminho' => $this->config->get('backup.caminho', '/backups')
        ];
    }

    private function salvarConfiguracoes($secao, $dados)
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
} 