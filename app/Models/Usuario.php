<?php

namespace App\Models;

use Core\Database\Model;

class Usuario extends Model
{
    protected string $table = 'usuarios';
    protected array $fillable = [
        'empresa_id',
        'nome',
        'email',
        'senha',
        'tipo',
        'status',
        'cpf',
        'telefone',
        'data_nascimento',
        'genero',
        'endereco',
        'cidade',
        'estado',
        'cep',
        'biografia',
        'foto_perfil',
        'ultimo_acesso',
        'reset_token',
        'reset_token_expires',
        'remember_token',
        'created_at',
        'updated_at'
    ];

    protected array $casts = [
        'data_nascimento' => 'date',
        'ultimo_acesso' => 'datetime',
        'reset_token_expires' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function vendas()
    {
        return $this->hasMany(Venda::class);
    }

    // Métodos de autenticação
    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->getAttribute($this->getAuthIdentifierName());
    }

    public function getAuthPassword(): string
    {
        return $this->senha;
    }

    public function getRememberToken(): string
    {
        return $this->remember_token ?? '';
    }

    public function setRememberToken($value): void
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }

    // Métodos úteis
    public function isAdminGeral(): bool
    {
        return $this->tipo === 'admin_geral';
    }

    public function isAdminEmpresa(): bool
    {
        return $this->tipo === 'admin_empresa';
    }

    public function isAtivo(): bool
    {
        return $this->status === 'ativo';
    }

    public function setSenha($senha): void
    {
        $this->senha = password_hash($senha, PASSWORD_DEFAULT);
    }

    public function verificarSenha($senha): bool
    {
        return password_verify($senha, $this->senha);
    }

    public function atualizarUltimoAcesso(): void
    {
        $this->ultimo_acesso = now();
        $this->save();
    }

    public function scopeAtivos($query)
    {
        return $query->where('status', 'ativo');
    }

    public function scopeAdminGeral($query)
    {
        return $query->where('tipo', 'admin_geral');
    }

    public function scopeAdminEmpresa($query)
    {
        return $query->where('tipo', 'admin_empresa');
    }

    // Métodos necessários para o sistema
    public function count(): int
    {
        return $this->query()->count();
    }

    public function countAtivos(): int
    {
        return $this->query()->where('status', 'ativo')->count();
    }

    public function getRecent(int $limit = 10): array
    {
        return $this->query()
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
    }

    public function getPaginated(int $page = 1, int $perPage = 15, array $filters = []): array
    {
        $query = $this->query();
        
        // Aplicar filtros
        if (!empty($filters['empresa_id'])) {
            $query->where('empresa_id', $filters['empresa_id']);
        }
        
        if (!empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }
        
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (!empty($filters['nome'])) {
            $query->where('nome', 'LIKE', '%' . $filters['nome'] . '%');
        }
        
        if (!empty($filters['email'])) {
            $query->where('email', 'LIKE', '%' . $filters['email'] . '%');
        }

        $total = $query->count();
        $offset = ($page - 1) * $perPage;
        
        $data = $query->orderBy('created_at', 'DESC')
            ->limit($perPage)
            ->offset($offset)
            ->get();

        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }

    public function getByEmpresa(int $empresaId): array
    {
        return $this->query()
            ->where('empresa_id', $empresaId)
            ->where('status', 'ativo')
            ->orderBy('nome', 'ASC')
            ->get();
    }

    public function getByTipo(string $tipo): array
    {
        return $this->query()
            ->where('tipo', $tipo)
            ->where('status', 'ativo')
            ->orderBy('nome', 'ASC')
            ->get();
    }

    public function getByStatus(string $status): array
    {
        return $this->query()
            ->where('status', $status)
            ->orderBy('nome', 'ASC')
            ->get();
    }

    public function search(string $termo): array
    {
        return $this->query()
            ->where('nome', 'LIKE', '%' . $termo . '%')
            ->orWhere('email', 'LIKE', '%' . $termo . '%')
            ->orWhere('cpf', 'LIKE', '%' . $termo . '%')
            ->where('status', 'ativo')
            ->orderBy('nome', 'ASC')
            ->limit(20)
            ->get();
    }

    public function getTotalEmpresas(int $usuarioId): int
    {
        // Placeholder - implementar lógica real baseada no relacionamento
        return $this->query()
            ->where('id', $usuarioId)
            ->where('tipo', 'admin_empresa')
            ->count();
    }

    public function getTotalEstabelecimentos(int $usuarioId): int
    {
        // Placeholder - implementar lógica real baseada no relacionamento
        return $this->query()
            ->where('id', $usuarioId)
            ->where('tipo', 'admin_empresa')
            ->count();
    }

    public function getUsuariosAtivos(): array
    {
        return $this->query()
            ->where('status', 'ativo')
            ->orderBy('nome', 'ASC')
            ->get();
    }

    public function getUsuariosInativos(): array
    {
        return $this->query()
            ->where('status', 'inativo')
            ->orderBy('nome', 'ASC')
            ->get();
    }

    public function getUsuariosRecentes(int $dias = 30): array
    {
        $dataLimite = date('Y-m-d H:i:s', strtotime("-{$dias} days"));
        
        return $this->query()
            ->where('created_at', '>=', $dataLimite)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function getCountByTipo(): array
    {
        return $this->query()
            ->select('tipo, COUNT(*) as total')
            ->where('status', 'ativo')
            ->groupBy('tipo')
            ->orderBy('total', 'DESC')
            ->get();
    }

    public function getCountByEmpresa(int $empresaId): int
    {
        return $this->query()
            ->where('empresa_id', $empresaId)
            ->where('status', 'ativo')
            ->count();
    }

    public function getCountByStatus(): array
    {
        return $this->query()
            ->select('status, COUNT(*) as total')
            ->groupBy('status')
            ->orderBy('total', 'DESC')
            ->get();
    }

    public function ativar(int $id): bool
    {
        $usuario = $this->find($id);
        if ($usuario) {
            return $this->query()
                ->where('id', $id)
                ->update(['status' => 'ativo', 'updated_at' => date('Y-m-d H:i:s')]);
        }
        return false;
    }

    public function inativar(int $id): bool
    {
        $usuario = $this->find($id);
        if ($usuario) {
            return $this->query()
                ->where('id', $id)
                ->update(['status' => 'inativo', 'updated_at' => date('Y-m-d H:i:s')]);
        }
        return false;
    }

    public function alterarSenha(int $id, string $novaSenha): bool
    {
        $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
        
        return $this->query()
            ->where('id', $id)
            ->update([
                'senha' => $senhaHash,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
    }

    public function redefinirSenha(int $id, string $novaSenha): bool
    {
        $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
        
        return $this->query()
            ->where('id', $id)
            ->update([
                'senha' => $senhaHash,
                'reset_token' => null,
                'reset_token_expires' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
    }

    public function getEstatisticas(): array
    {
        $total = $this->count();
        $ativos = $this->countAtivos();
        $inativos = $total - $ativos;
        
        $porTipo = $this->getCountByTipo();
        $porStatus = $this->getCountByStatus();
        
        return [
            'total' => $total,
            'ativos' => $ativos,
            'inativos' => $inativos,
            'por_tipo' => $porTipo,
            'por_status' => $porStatus
        ];
    }

    public function validarCPF(string $cpf): bool
    {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        // Verifica se tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }
        
        // Verifica se todos os dígitos são iguais
        if (preg_match('/^(\d)\1+$/', $cpf)) {
            return false;
        }
        
        // Validação do primeiro dígito verificador
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += $cpf[$i] * (10 - $i);
        }
        $resto = $soma % 11;
        $dv1 = $resto < 2 ? 0 : 11 - $resto;
        
        // Validação do segundo dígito verificador
        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += $cpf[$i] * (11 - $i);
        }
        $resto = $soma % 11;
        $dv2 = $resto < 2 ? 0 : 11 - $resto;
        
        return $cpf[9] == $dv1 && $cpf[10] == $dv2;
    }

    public function validarEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function validarForcaSenha(string $senha): array
    {
        $erros = [];
        
        if (strlen($senha) < 8) {
            $erros[] = 'A senha deve ter pelo menos 8 caracteres';
        }
        
        if (!preg_match('/[A-Z]/', $senha)) {
            $erros[] = 'A senha deve conter pelo menos uma letra maiúscula';
        }
        
        if (!preg_match('/[a-z]/', $senha)) {
            $erros[] = 'A senha deve conter pelo menos uma letra minúscula';
        }
        
        if (!preg_match('/[0-9]/', $senha)) {
            $erros[] = 'A senha deve conter pelo menos um número';
        }
        
        if (!preg_match('/[^A-Za-z0-9]/', $senha)) {
            $erros[] = 'A senha deve conter pelo menos um caractere especial';
        }
        
        return [
            'valida' => empty($erros),
            'erros' => $erros,
            'forca' => $this->calcularForcaSenha($senha)
        ];
    }

    private function calcularForcaSenha(string $senha): int
    {
        $forca = 0;
        
        if (strlen($senha) >= 8) $forca += 1;
        if (preg_match('/[A-Z]/', $senha)) $forca += 1;
        if (preg_match('/[a-z]/', $senha)) $forca += 1;
        if (preg_match('/[0-9]/', $senha)) $forca += 1;
        if (preg_match('/[^A-Za-z0-9]/', $senha)) $forca += 1;
        if (strlen($senha) >= 12) $forca += 1;
        
        return $forca;
    }
} 