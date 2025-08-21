<?php

namespace App\Models;

use Core\Database\Model;

class AtividadeModel extends Model
{
    protected string $table = 'atividades';
    
    protected array $fillable = [
        'usuario_id',
        'empresa_id',
        'tipo',
        'acao',
        'descricao',
        'dados_anteriores',
        'dados_novos',
        'ip_address',
        'user_agent',
        'created_at'
    ];

    protected array $casts = [
        'dados_anteriores' => 'json',
        'dados_novos' => 'json',
        'created_at' => 'datetime'
    ];

    public function beforeCreate(array &$data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
    }

    // Métodos necessários para o sistema
    public function count(): int
    {
        return $this->query()->count();
    }

    public function countHoje(): int
    {
        $hoje = date('Y-m-d');
        return $this->query()
            ->where('DATE(created_at)', $hoje)
            ->count();
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
        if (!empty($filters['usuario_id'])) {
            $query->where('usuario_id', $filters['usuario_id']);
        }
        
        if (!empty($filters['empresa_id'])) {
            $query->where('empresa_id', $filters['empresa_id']);
        }
        
        if (!empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }
        
        if (!empty($filters['acao'])) {
            $query->where('acao', $filters['acao']);
        }
        
        if (!empty($filters['data_inicio'])) {
            $query->where('created_at', '>=', $filters['data_inicio']);
        }
        
        if (!empty($filters['data_fim'])) {
            $query->where('created_at', '<=', $filters['data_fim'] . ' 23:59:59');
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

    public function getByUsuario(int $usuarioId, int $limit = 20): array
    {
        return $this->query()
            ->where('usuario_id', $usuarioId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
    }

    public function getByEmpresa(int $empresaId, int $limit = 20): array
    {
        return $this->query()
            ->where('empresa_id', $empresaId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
    }

    public function getByTipo(string $tipo, int $limit = 20): array
    {
        return $this->query()
            ->where('tipo', $tipo)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
    }

    public function getByAcao(string $acao, int $limit = 20): array
    {
        return $this->query()
            ->where('acao', $acao)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
    }

    public function getAtividadesRecentes(int $dias = 7): array
    {
        $dataLimite = date('Y-m-d H:i:s', strtotime("-{$dias} days"));
        
        return $this->query()
            ->where('created_at', '>=', $dataLimite)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function getAtividadesHoje(): array
    {
        $hoje = date('Y-m-d');
        
        return $this->query()
            ->where('DATE(created_at)', $hoje)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function getAtividadesSemana(): array
    {
        $semanaPassada = date('Y-m-d H:i:s', strtotime('-7 days'));
        
        return $this->query()
            ->where('created_at', '>=', $semanaPassada)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function getAtividadesMes(): array
    {
        $mesPassado = date('Y-m-d H:i:s', strtotime('-30 days'));
        
        return $this->query()
            ->where('created_at', '>=', $mesPassado)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function getCountByTipo(): array
    {
        return $this->query()
            ->select('tipo, COUNT(*) as total')
            ->groupBy('tipo')
            ->orderBy('total', 'DESC')
            ->get();
    }

    public function getCountByAcao(): array
    {
        return $this->query()
            ->select('acao, COUNT(*) as total')
            ->groupBy('acao')
            ->orderBy('total', 'DESC')
            ->get();
    }

    public function getCountByUsuario(int $usuarioId): int
    {
        return $this->query()
            ->where('usuario_id', $usuarioId)
            ->count();
    }

    public function getCountByEmpresa(int $empresaId): int
    {
        return $this->query()
            ->where('empresa_id', $empresaId)
            ->count();
    }

    public function getCountByData(string $data): int
    {
        return $this->query()
            ->where('DATE(created_at)', $data)
            ->count();
    }

    public function getCountByPeriodo(string $dataInicio, string $dataFim): int
    {
        return $this->query()
            ->where('created_at', '>=', $dataInicio)
            ->where('created_at', '<=', $dataFim . ' 23:59:59')
            ->count();
    }

    public function registrarAtividade(int $usuarioId, int $empresaId, string $tipo, string $acao, string $descricao, array $dadosAnteriores = null, array $dadosNovos = null): bool
    {
        $dados = [
            'usuario_id' => $usuarioId,
            'empresa_id' => $empresaId,
            'tipo' => $tipo,
            'acao' => $acao,
            'descricao' => $descricao,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($dadosAnteriores !== null) {
            $dados['dados_anteriores'] = json_encode($dadosAnteriores);
        }

        if ($dadosNovos !== null) {
            $dados['dados_novos'] = json_encode($dadosNovos);
        }

        return $this->query()->insert($dados);
    }

    public function limparAtividadesAntigas(int $dias = 90): bool
    {
        $dataLimite = date('Y-m-d H:i:s', strtotime("-{$dias} days"));
        
        return $this->query()
            ->where('created_at', '<', $dataLimite)
            ->delete();
    }

    public function getEstatisticas(): array
    {
        $total = $this->count();
        $hoje = $this->countHoje();
        $semana = count($this->getAtividadesSemana());
        $mes = count($this->getAtividadesMes());
        
        $porTipo = $this->getCountByTipo();
        $porAcao = $this->getCountByAcao();
        
        return [
            'total' => $total,
            'hoje' => $hoje,
            'semana' => $semana,
            'mes' => $mes,
            'por_tipo' => $porTipo,
            'por_acao' => $porAcao
        ];
    }

    public function search(string $termo): array
    {
        return $this->query()
            ->where('descricao', 'LIKE', '%' . $termo . '%')
            ->orWhere('acao', 'LIKE', '%' . $termo . '%')
            ->orWhere('tipo', 'LIKE', '%' . $termo . '%')
            ->orderBy('created_at', 'DESC')
            ->limit(20)
            ->get();
    }
} 