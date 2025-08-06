<?php

namespace App\Models;

use Core\Database\Model;

class Empresa extends Model
{
    protected $table = 'empresas';
    protected $fillable = [
        'nome_fantasia',
        'razao_social',
        'cnpj',
        'email',
        'telefone',
        'endereco',
        'cidade',
        'estado',
        'cep',
        'logo',
        'cor_primaria',
        'cor_secundaria',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'data_cadastro' => 'datetime',
        'data_atualizacao' => 'datetime'
    ];

    // Relacionamentos
    public function usuarios()
    {
        return $this->hasMany(Usuario::class);
    }

    public function pessoas()
    {
        return $this->hasMany(Pessoa::class);
    }

    public function categorias()
    {
        return $this->hasMany(Categoria::class);
    }

    public function produtos()
    {
        return $this->hasMany(Produto::class);
    }

    public function vendas()
    {
        return $this->hasMany(Venda::class);
    }

    // Métodos úteis
    public function getEnderecoCompleto(): string
    {
        return "{$this->endereco}, {$this->cidade} - {$this->estado}, CEP: {$this->cep}";
    }

    public function getLogoUrl(): string
    {
        if (!$this->logo) {
            return '/assets/images/default-logo.png';
        }
        return "/uploads/empresas/{$this->logo}";
    }

    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }
} 