<?php

namespace App\Models;

use Core\Database\Model;

class EstabelecimentoModel extends Model
{
    protected string $table = 'estabelecimentos';
    
    protected array $fillable = [
        'empresa_id',
        'nome',
        'cnpj',
        'endereco',
        'cidade',
        'estado',
        'cep',
        'telefone',
        'email',
        'responsavel',
        'status',
        'tipo_estabelecimento',
        'data_abertura',
        'created_at',
        'updated_at'
    ];

    protected array $casts = [
        'data_abertura' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function beforeCreate(array &$data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['status'] = $data['status'] ?? 'ativo';
    }

    public function beforeUpdate(array &$data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
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
        
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }
        
        if (!empty($filters['cidade'])) {
            $query->where('cidade', 'LIKE', '%' . $filters['cidade'] . '%');
        }
        
        if (!empty($filters['nome'])) {
            $query->where('nome', 'LIKE', '%' . $filters['nome'] . '%');
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

    public function getByEstado(string $estado): array
    {
        return $this->query()
            ->where('estado', $estado)
            ->where('status', 'ativo')
            ->get();
    }

    public function getByCidade(string $cidade): array
    {
        return $this->query()
            ->where('cidade', 'LIKE', '%' . $cidade . '%')
            ->where('status', 'ativo')
            ->get();
    }

    public function search(string $termo): array
    {
        return $this->query()
            ->where('nome', 'LIKE', '%' . $termo . '%')
            ->orWhere('cnpj', 'LIKE', '%' . $termo . '%')
            ->orWhere('responsavel', 'LIKE', '%' . $termo . '%')
            ->orWhere('cidade', 'LIKE', '%' . $termo . '%')
            ->where('status', 'ativo')
            ->orderBy('nome', 'ASC')
            ->limit(20)
            ->get();
    }

    public function getCountByEmpresa(int $empresaId): int
    {
        return $this->query()
            ->where('empresa_id', $empresaId)
            ->where('status', 'ativo')
            ->count();
    }

    public function getCountByEstado(): array
    {
        return $this->query()
            ->select('estado, COUNT(*) as total')
            ->where('status', 'ativo')
            ->groupBy('estado')
            ->orderBy('total', 'DESC')
            ->get();
    }

    public function getCountByTipo(): array
    {
        return $this->query()
            ->select('tipo_estabelecimento, COUNT(*) as total')
            ->where('status', 'ativo')
            ->groupBy('tipo_estabelecimento')
            ->orderBy('total', 'DESC')
            ->get();
    }

    public function getEstabelecimentosAtivos(): array
    {
        return $this->query()
            ->where('status', 'ativo')
            ->orderBy('nome', 'ASC')
            ->get();
    }

    public function getEstabelecimentosInativos(): array
    {
        return $this->query()
            ->where('status', 'inativo')
            ->orderBy('nome', 'ASC')
            ->get();
    }

    public function ativar(int $id): bool
    {
        $estabelecimento = $this->find($id);
        if ($estabelecimento) {
            return $this->query()
                ->where('id', $id)
                ->update(['status' => 'ativo', 'updated_at' => date('Y-m-d H:i:s')]);
        }
        return false;
    }

    public function inativar(int $id): bool
    {
        $estabelecimento = $this->find($id);
        if ($estabelecimento) {
            return $this->query()
                ->where('id', $id)
                ->update(['status' => 'inativo', 'updated_at' => date('Y-m-d H:i:s')]);
        }
        return false;
    }

    public function getEstabelecimentosRecentes(int $dias = 30): array
    {
        $dataLimite = date('Y-m-d H:i:s', strtotime("-{$dias} days"));
        
        return $this->query()
            ->where('created_at', '>=', $dataLimite)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function getEstatisticas(): array
    {
        $total = $this->count();
        $ativos = $this->countAtivos();
        $inativos = $total - $ativos;
        
        $porEstado = $this->getCountByEstado();
        $porTipo = $this->getCountByTipo();
        
        return [
            'total' => $total,
            'ativos' => $ativos,
            'inativos' => $inativos,
            'por_estado' => $porEstado,
            'por_tipo' => $porTipo
        ];
    }
} 