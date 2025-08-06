<?php

namespace App\Models;

use Core\Database\Model;
use Core\Auth\Authenticatable;

class Usuario extends Model implements Authenticatable
{
    protected $table = 'usuarios';
    protected $fillable = [
        'empresa_id',
        'nome',
        'email',
        'senha',
        'tipo',
        'status'
    ];

    protected $hidden = ['senha'];

    protected $casts = [
        'ultimo_acesso' => 'datetime',
        'data_cadastro' => 'datetime',
        'data_atualizacao' => 'datetime'
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
} 