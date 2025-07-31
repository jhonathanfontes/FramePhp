<?php

namespace Core\Database;

use Core\Cache\CacheManager;

abstract class Model
{
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];
    protected array $casts = [];
    protected bool $timestamps = true;
    protected Database $db;
    protected CacheManager $cache;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->cache = CacheManager::getInstance();

        if (empty($this->table)) {
            $this->table = $this->getTableName();
        }
    }

    // Query methods
    public function query(): QueryBuilder
    {
        $queryBuilder = new QueryBuilder($this->table);
        
        // Se o modelo suporta soft delete, habilitar automaticamente
        if (property_exists($this, 'softDelete') && $this->softDelete) {
            $queryBuilder->enableSoftDelete();
            
            // Se tem coluna personalizada de soft delete
            if (property_exists($this, 'deletedAtColumn')) {
                $queryBuilder->setDeletedAtColumn($this->deletedAtColumn);
            }
        }
        
        return $queryBuilder;
    }

    public function find(int $id): ?array
    {
        $cacheKey = $this->table . '_' . $id;

        return $this->cache->remember($cacheKey, function() use ($id) {
            return $this->db->find($this->table, $this->primaryKey . ' = ?', [$id]);
        }, 300);
    }

    public function findBy(string $column, $value): ?array
    {
        return $this->query()
            ->where($column, $value)
            ->first();
    }

    public function all(): array
    {
        return $this->query()->get();
    }

    public function where(string $column, $value, string $operator = '='): QueryBuilder
    {
        return $this->query()->where($column, $value, $operator);
    }

    public function select(string $columns = '*'): QueryBuilder
    {
        return $this->query()->select($columns);
    }

    public function orderBy(string $column, string $direction = 'ASC'): QueryBuilder
    {
        return $this->query()->orderBy($column, $direction);
    }

    public function limit(int $limit): QueryBuilder
    {
        return $this->query()->limit($limit);
    }

    public function whereIn(string $column, array $values): QueryBuilder
    {
        return $this->query()->whereIn($column, $values);
    }

    public function whereNull(string $column): QueryBuilder
    {
        return $this->query()->whereNull($column);
    }

    public function whereNotNull(string $column): QueryBuilder
    {
        return $this->query()->whereNotNull($column);
    }

    public function join(string $table, string $first, string $second, string $operator = '='): QueryBuilder
    {
        return $this->query()->join($table, $first, $second, $operator);
    }

    public function leftJoin(string $table, string $first, string $second, string $operator = '='): QueryBuilder
    {
        return $this->query()->leftJoin($table, $first, $second, $operator);
    }

    public function create(array $data): int
    {
        $data = $this->filterFillable($data);

        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $id = $this->db->insert($this->table, $data);
        $this->clearModelCache();

        return $id;
    }

    public function update(int $id, array $data): bool
    {
        $data = $this->filterFillable($data);

        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $result = $this->db->update($this->table, $data, $this->primaryKey . ' = ?', [$id]);
        $this->clearModelCache($id);

        return $result;
    }

    public function delete(int $id): bool
    {
        $result = $this->db->delete($this->table, $this->primaryKey . ' = ?', [$id]);
        $this->clearModelCache($id);

        return $result;
    }

    public function paginate(int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;

        $total = $this->db->count($this->table);
        $data = $this->db->select($this->table, '*', '', [], $perPage, $offset);

        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }

    // Helper methods
    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    protected function hideAttributes(array $data): array
    {
        foreach ($this->hidden as $hidden) {
            unset($data[$hidden]);
        }

        return $data;
    }

    protected function castAttributes(array $data): array
    {
        foreach ($this->casts as $key => $type) {
            if (!isset($data[$key])) continue;

            switch ($type) {
                case 'int':
                case 'integer':
                    $data[$key] = (int) $data[$key];
                    break;
                case 'float':
                    $data[$key] = (float) $data[$key];
                    break;
                case 'bool':
                case 'boolean':
                    $data[$key] = (bool) $data[$key];
                    break;
                case 'json':
                    $data[$key] = json_decode($data[$key], true);
                    break;
            }
        }

        return $data;
    }

    protected function getTableName(): string
    {
        $className = (new \ReflectionClass($this))->getShortName();
        return strtolower(preg_replace('/Model$/', '', $className));
    }

    protected function clearModelCache(?int $id = null): void
    {
        if ($id) {
            $this->cache->delete($this->table . '_' . $id);
        }
        // Limpar cache de listagens também
        $this->cache->delete($this->table . '_all');
    }
}