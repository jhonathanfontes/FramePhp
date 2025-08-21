<?php

namespace App\Models;

use Core\Database\Model;

class Config extends Model
{
    protected string $table = 'configuracoes';
    
    protected array $fillable = [
        'chave',
        'valor',
        'tipo',
        'descricao',
        'categoria',
        'editavel',
        'created_at',
        'updated_at'
    ];

    protected array $casts = [
        'valor' => 'json',
        'editavel' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function beforeCreate(array &$data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['editavel'] = $data['editavel'] ?? true;
    }

    public function beforeUpdate(array &$data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
    }

    // Métodos para gerenciar configurações
    public function get(string $chave, $padrao = null)
    {
        $config = $this->query()
            ->where('chave', $chave)
            ->first();
        
        if ($config) {
            return $this->parseValue($config['valor'], $config['tipo']);
        }
        
        return $padrao;
    }

    public function set(string $chave, $valor, string $tipo = 'string', string $descricao = '', string $categoria = 'geral'): bool
    {
        $config = $this->query()
            ->where('chave', $chave)
            ->first();
        
        $dados = [
            'chave' => $chave,
            'valor' => $this->serializeValue($valor, $tipo),
            'tipo' => $tipo,
            'descricao' => $descricao,
            'categoria' => $categoria,
            'editavel' => true
        ];
        
        if ($config) {
            return $this->query()
                ->where('chave', $chave)
                ->update($dados);
        } else {
            return $this->query()->insert($dados);
        }
    }

    public function has(string $chave): bool
    {
        return $this->query()
            ->where('chave', $chave)
            ->exists();
    }

    public function delete(string $chave): bool
    {
        return $this->query()
            ->where('chave', $chave)
            ->delete();
    }

    public function getAll(): array
    {
        return $this->query()
            ->orderBy('categoria', 'ASC')
            ->orderBy('chave', 'ASC')
            ->get();
    }

    public function getByCategoria(string $categoria): array
    {
        return $this->query()
            ->where('categoria', $categoria)
            ->orderBy('chave', 'ASC')
            ->get();
    }

    public function getCategorias(): array
    {
        return $this->query()
            ->select('categoria')
            ->distinct()
            ->orderBy('categoria', 'ASC')
            ->get();
    }

    public function getConfiguracoesGerais(): array
    {
        $configs = $this->getByCategoria('geral');
        $resultado = [];
        
        foreach ($configs as $config) {
            $resultado[$config['chave']] = $this->parseValue($config['valor'], $config['tipo']);
        }
        
        return $resultado;
    }

    public function getConfiguracoesUsuarios(): array
    {
        $configs = $this->getByCategoria('usuarios');
        $resultado = [];
        
        foreach ($configs as $config) {
            $resultado[$config['chave']] = $this->parseValue($config['valor'], $config['tipo']);
        }
        
        return $resultado;
    }

    public function getConfiguracoesEmpresas(): array
    {
        $configs = $this->getByCategoria('empresas');
        $resultado = [];
        
        foreach ($configs as $config) {
            $resultado[$config['chave']] = $this->parseValue($config['valor'], $config['tipo']);
        }
        
        return $resultado;
    }

    public function getConfiguracoesSeguranca(): array
    {
        $configs = $this->getByCategoria('seguranca');
        $resultado = [];
        
        foreach ($configs as $config) {
            $resultado[$config['chave']] = $this->parseValue($config['valor'], $config['tipo']);
        }
        
        return $resultado;
    }

    public function getConfiguracoesNotificacoes(): array
    {
        $configs = $this->getByCategoria('notificacoes');
        $resultado = [];
        
        foreach ($configs as $config) {
            $resultado[$config['chave']] = $this->parseValue($config['valor'], $config['tipo']);
        }
        
        return $resultado;
    }

    public function getConfiguracoesBackup(): array
    {
        $configs = $this->getByCategoria('backup');
        $resultado = [];
        
        foreach ($configs as $config) {
            $resultado[$config['chave']] = $this->parseValue($config['valor'], $config['tipo']);
        }
        
        return $resultado;
    }

    public function salvarConfiguracoes(string $categoria, array $dados): bool
    {
        $sucesso = true;
        
        foreach ($dados as $chave => $valor) {
            $tipo = $this->determinarTipo($valor);
            $descricao = $this->getDescricaoPadrao($chave);
            
            if (!$this->set($chave, $valor, $tipo, $descricao, $categoria)) {
                $sucesso = false;
            }
        }
        
        return $sucesso;
    }

    public function resetarParaPadrao(string $categoria): bool
    {
        $configuracoesPadrao = $this->getConfiguracoesPadrao($categoria);
        return $this->salvarConfiguracoes($categoria, $configuracoesPadrao);
    }

    public function importarConfiguracoes(array $dados): bool
    {
        $sucesso = true;
        
        foreach ($dados as $categoria => $configs) {
            if (is_array($configs)) {
                foreach ($configs as $chave => $valor) {
                    $tipo = $this->determinarTipo($valor);
                    $descricao = $this->getDescricaoPadrao($chave);
                    
                    if (!$this->set($chave, $valor, $tipo, $descricao, $categoria)) {
                        $sucesso = false;
                    }
                }
            }
        }
        
        return $sucesso;
    }

    public function exportarConfiguracoes(): array
    {
        $configs = $this->getAll();
        $resultado = [];
        
        foreach ($configs as $config) {
            $categoria = $config['categoria'];
            if (!isset($resultado[$categoria])) {
                $resultado[$categoria] = [];
            }
            
            $resultado[$categoria][$config['chave']] = $this->parseValue($config['valor'], $config['tipo']);
        }
        
        return $resultado;
    }

    private function parseValue($valor, string $tipo)
    {
        if ($tipo === 'json') {
            return json_decode($valor, true);
        } elseif ($tipo === 'boolean') {
            return (bool)$valor;
        } elseif ($tipo === 'integer') {
            return (int)$valor;
        } elseif ($tipo === 'float') {
            return (float)$valor;
        } elseif ($tipo === 'array') {
            return is_array($valor) ? $valor : [$valor];
        }
        
        return $valor;
    }

    private function serializeValue($valor, string $tipo)
    {
        if ($tipo === 'json' || $tipo === 'array') {
            return json_encode($valor);
        }
        
        return (string)$valor;
    }

    private function determinarTipo($valor): string
    {
        if (is_bool($valor)) return 'boolean';
        if (is_int($valor)) return 'integer';
        if (is_float($valor)) return 'float';
        if (is_array($valor)) return 'array';
        if (is_string($valor) && (strpos($valor, '{') === 0 || strpos($valor, '[') === 0)) return 'json';
        
        return 'string';
    }

    private function getDescricaoPadrao(string $chave): string
    {
        $descricoes = [
            'nome_sistema' => 'Nome do sistema',
            'versao' => 'Versão atual do sistema',
            'timezone' => 'Fuso horário padrão',
            'idioma' => 'Idioma padrão do sistema',
            'registros_por_pagina' => 'Número de registros por página',
            'modo_manutencao' => 'Ativar modo de manutenção',
            'min_senha' => 'Tamanho mínimo da senha',
            'max_tentativas_login' => 'Máximo de tentativas de login',
            'tempo_bloqueio' => 'Tempo de bloqueio em minutos',
            'sessao_timeout' => 'Timeout da sessão em minutos',
            'log_acesso' => 'Ativar log de acesso',
            'ips_permitidos' => 'IPs permitidos (separados por vírgula)',
            'email_remetente' => 'E-mail remetente padrão',
            'nome_remetente' => 'Nome do remetente padrão',
            'smtp_host' => 'Host do servidor SMTP',
            'smtp_porta' => 'Porta do servidor SMTP',
            'backup_automatico' => 'Ativar backup automático',
            'frequencia_backup' => 'Frequência do backup (diário, semanal, mensal)',
            'retencao_backup' => 'Dias de retenção do backup',
            'compressao_backup' => 'Ativar compressão do backup'
        ];
        
        return $descricoes[$chave] ?? 'Configuração do sistema';
    }

    private function getConfiguracoesPadrao(string $categoria): array
    {
        $padroes = [
            'geral' => [
                'nome_sistema' => 'FramePhp Admin',
                'versao' => '1.0.0',
                'timezone' => 'America/Sao_Paulo',
                'idioma' => 'pt_BR',
                'registros_por_pagina' => 15,
                'modo_manutencao' => false
            ],
            'usuarios' => [
                'min_senha' => 8,
                'max_tentativas_login' => 5,
                'tempo_bloqueio' => 30,
                'requer_maiuscula' => true,
                'requer_minuscula' => true,
                'requer_numero' => true,
                'requer_especial' => true
            ],
            'empresas' => [
                'limite_cadastro' => 1000,
                'validacao_automatica' => true,
                'consulta_receita' => false,
                'requer_cnpj_valido' => true,
                'requer_endereco_completo' => true
            ],
            'seguranca' => [
                'sessao_timeout' => 120,
                'log_acesso' => true,
                'ips_permitidos' => '',
                'https_obrigatorio' => false,
                'csrf_protection' => true
            ],
            'notificacoes' => [
                'email_remetente' => 'noreply@framephp.com',
                'nome_remetente' => 'FramePhp Admin',
                'smtp_host' => 'localhost',
                'smtp_porta' => 587,
                'smtp_usuario' => '',
                'smtp_senha' => '',
                'notificar_novos_usuarios' => true,
                'notificar_alteracoes_empresa' => true
            ],
            'backup' => [
                'backup_automatico' => true,
                'frequencia_backup' => 'diario',
                'retencao_backup' => 30,
                'compressao_backup' => true,
                'caminho_backup' => '/backups/',
                'notificar_erro_backup' => true
            ]
        ];
        
        return $padroes[$categoria] ?? [];
    }
} 