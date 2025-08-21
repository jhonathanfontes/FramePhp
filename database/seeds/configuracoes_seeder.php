<?php

use Core\Database\Seeder;
use App\Models\Config;

class ConfiguracoesSeeder extends Seeder
{
    public function run(): void
    {
        $config = new Config();
        
        // Configurações Gerais
        $config->set('nome_sistema', 'FramePhp Admin', 'string', 'Nome do sistema', 'geral');
        $config->set('versao', '1.0.0', 'string', 'Versão atual do sistema', 'geral');
        $config->set('timezone', 'America/Sao_Paulo', 'string', 'Fuso horário padrão', 'geral');
        $config->set('idioma', 'pt_BR', 'string', 'Idioma padrão do sistema', 'geral');
        $config->set('registros_por_pagina', 15, 'integer', 'Número de registros por página', 'geral');
        $config->set('modo_manutencao', false, 'boolean', 'Ativar modo de manutenção', 'geral');
        
        // Configurações de Usuários
        $config->set('min_senha', 8, 'integer', 'Tamanho mínimo da senha', 'usuarios');
        $config->set('max_tentativas_login', 5, 'integer', 'Máximo de tentativas de login', 'usuarios');
        $config->set('tempo_bloqueio', 30, 'integer', 'Tempo de bloqueio em minutos', 'usuarios');
        $config->set('requer_maiuscula', true, 'boolean', 'Requer letra maiúscula na senha', 'usuarios');
        $config->set('requer_minuscula', true, 'boolean', 'Requer letra minúscula na senha', 'usuarios');
        $config->set('requer_numero', true, 'boolean', 'Requer número na senha', 'usuarios');
        $config->set('requer_especial', true, 'boolean', 'Requer caractere especial na senha', 'usuarios');
        
        // Configurações de Empresas
        $config->set('limite_cadastro', 1000, 'integer', 'Limite de empresas por usuário', 'empresas');
        $config->set('validacao_automatica', true, 'boolean', 'Validação automática de CNPJ', 'empresas');
        $config->set('consulta_receita', false, 'boolean', 'Consulta automática na Receita Federal', 'empresas');
        $config->set('requer_cnpj_valido', true, 'boolean', 'Requer CNPJ válido', 'empresas');
        $config->set('requer_endereco_completo', true, 'boolean', 'Requer endereço completo', 'empresas');
        
        // Configurações de Segurança
        $config->set('sessao_timeout', 120, 'integer', 'Timeout da sessão em minutos', 'seguranca');
        $config->set('log_acesso', true, 'boolean', 'Ativar log de acesso', 'seguranca');
        $config->set('ips_permitidos', '', 'string', 'IPs permitidos (separados por vírgula)', 'seguranca');
        $config->set('https_obrigatorio', false, 'boolean', 'HTTPS obrigatório', 'seguranca');
        $config->set('csrf_protection', true, 'boolean', 'Proteção CSRF', 'seguranca');
        
        // Configurações de Notificações
        $config->set('email_remetente', 'noreply@framephp.com', 'string', 'E-mail remetente padrão', 'notificacoes');
        $config->set('nome_remetente', 'FramePhp Admin', 'string', 'Nome do remetente padrão', 'notificacoes');
        $config->set('smtp_host', 'localhost', 'string', 'Host do servidor SMTP', 'notificacoes');
        $config->set('smtp_porta', 587, 'integer', 'Porta do servidor SMTP', 'notificacoes');
        $config->set('smtp_usuario', '', 'string', 'Usuário SMTP', 'notificacoes');
        $config->set('smtp_senha', '', 'string', 'Senha SMTP', 'notificacoes');
        $config->set('notificar_novos_usuarios', true, 'boolean', 'Notificar novos usuários', 'notificacoes');
        $config->set('notificar_alteracoes_empresa', true, 'boolean', 'Notificar alterações em empresas', 'notificacoes');
        
        // Configurações de Backup
        $config->set('backup_automatico', true, 'boolean', 'Ativar backup automático', 'backup');
        $config->set('frequencia_backup', 'diario', 'string', 'Frequência do backup', 'backup');
        $config->set('retencao_backup', 30, 'integer', 'Dias de retenção do backup', 'backup');
        $config->set('compressao_backup', true, 'boolean', 'Ativar compressão do backup', 'backup');
        $config->set('caminho_backup', '/backups/', 'string', 'Caminho para armazenar backups', 'backup');
        $config->set('notificar_erro_backup', true, 'boolean', 'Notificar erros de backup', 'backup');
        
        echo "Configurações padrão criadas com sucesso!\n";
    }
} 