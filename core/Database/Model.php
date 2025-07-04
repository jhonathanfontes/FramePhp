<?php

namespace Core\Database;

use PDOException; // Importa PDOException para uso no logError se necessário

abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $guarded = []; // Colunas que não podem ser preenchidas em massa
    protected array $attributes = [];
    protected bool $exists = false;

    protected bool $softDelete = false; // Habilita/desabilita soft delete para o modelo
    protected string $deletedAtColumn = 'deleted_at'; // Nome da coluna para soft delete

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public function query(): QueryBuilder
    {
        $query = new QueryBuilder($this->table);
        if ($this->softDelete) {
            $query->enableSoftDelete()->setDeletedAtColumn($this->deletedAtColumn);
        }
        return $query;
    }

    public function all(): array
    {
        return $this->query()->get();
    }

    public function find($id): ?self
    {
        $result = $this->query()->where($this->primaryKey, '=', $id)->first();

        if ($result) {
            return $this->newFromBuilder($result);
        }

        return null;
    }

    /**
     * Encontra um modelo incluindo os soft-deletados.
     * @param mixed $id
     * @return object|self|null
     */
    public function findWithTrashed($id): ?self
    {
        $query = $this->query();
        if ($this->softDelete) {
            $query->withTrashed(); // Garante que a consulta inclua os deletados
        }
        $result = $query->where($this->primaryKey, '=', $id)->first();

        if ($result) {
            return $this->newFromBuilder($result);
        }

        return null;
    }

    /**
     * Retorna apenas os registros soft-deletados.
     * @return array
     */
    public function onlyTrashed(): array
    {
        if (!$this->softDelete) {
            return []; // Retorna array vazio se soft delete não estiver habilitado
        }
        return $this->query()->withTrashed()->whereNotNull($this->deletedAtColumn)->get();
    }

    /**
     * Retorna todos os registros, incluindo os soft-deletados.
     * @return array
     */
    public function withTrashed(): array
    {
        if (!$this->softDelete) {
            return $this->all(); // Se soft delete não estiver habilitado, é o mesmo que all()
        }
        return $this->query()->withTrashed()->get();
    }

    public function create(array $attributes): self
    {
        $instance = new static($attributes);
        $instance->save();
        return $instance;
    }

    public function save(): bool
    {
        if ($this->exists) {
            return $this->performUpdate();
        }

        return $this->performInsert();
    }

    /**
     * Realiza um soft delete (ou delete físico se soft delete não estiver habilitado).
     * @return bool
     */
    public function delete(): bool
    {
        if (!$this->exists) {
            return false;
        }

        $database = Database::getInstance();
        $primaryKeyValue = $this->getAttribute($this->primaryKey);

        if ($this->softDelete) {
            // Realiza soft delete: atualiza a coluna deleted_at
            $affected = $database->update(
                $this->table,
                [$this->deletedAtColumn => date('Y-m-d H:i:s')],
                "{$this->primaryKey} = ?",
                [$primaryKeyValue]
            );
            return $affected > 0;
        } else {
            // Delete físico
            $affected = $database->delete($this->table, "{$this->primaryKey} = ?", [$primaryKeyValue]);
            if ($affected > 0) {
                $this->exists = false;
                return true;
            }
        }
        return false;
    }

    /**
     * Restaura um registro soft-deletado.
     * @return bool
     */
    public function restore(): bool
    {
        if (!$this->softDelete) {
            return false; // Não é um modelo soft-deletável
        }

        if ($this->getAttribute($this->deletedAtColumn) === null) {
            return false; // Não está soft-deletado
        }

        $database = Database::getInstance();
        $affected = $database->update(
            $this->table,
            [$this->deletedAtColumn => null],
            "{$this->primaryKey} = ?",
            [$this->getAttribute($this->primaryKey)]
        );

        if ($affected > 0) {
            $this->setAttribute($this->deletedAtColumn, null); // Atualiza o atributo no modelo
            return true;
        }
        return false;
    }

    /**
     * Força a exclusão física de um registro (mesmo que soft delete esteja habilitado).
     * @return bool
     */
    public function forceDelete(): bool
    {
        if (!$this->exists) {
            return false;
        }

        $database = Database::getInstance();
        $affected = $database->delete(
            $this->table,
            "{$this->primaryKey} = ?",
            [$this->getAttribute($this->primaryKey)]
        );

        if ($affected > 0) {
            $this->exists = false;
            return true;
        }
        return false;
    }

    protected function fill(array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            }
        }
    }

    protected function isFillable(string $key): bool
    {
        // Se fillable não estiver vazio, use-o como whitelist
        if (!empty($this->fillable)) {
            return in_array($key, $this->fillable);
        }

        // Se fillable estiver vazio, use guarded como blacklist
        return !in_array($key, $this->guarded);
    }

    protected function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    protected function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    protected function performInsert(): bool
    {
        $database = Database::getInstance();
        $id = $database->insert($this->table, $this->attributes);

        if ($id > 0) {
            $this->setAttribute($this->primaryKey, $id);
            $this->exists = true;
            return true;
        }

        return false;
    }

    protected function performUpdate(): bool
    {
        $database = Database::getInstance();
        $updateData = $this->attributes;

        // Remove a primary key e colunas guardadas/não preenchíveis dos dados de atualização
        unset($updateData[$this->primaryKey]);
        foreach ($this->guarded as $guardedKey) {
            if (isset($updateData[$guardedKey])) {
                unset($updateData[$guardedKey]);
            }
        }
        // Se fillable está definido, apenas use os atributos que são fillable
        if (!empty($this->fillable)) {
            $filteredData = [];
            foreach ($this->fillable as $fillableKey) {
                if (isset($this->attributes[$fillableKey])) {
                    $filteredData[$fillableKey] = $this->attributes[$fillableKey];
                }
            }
            $updateData = $filteredData;
        }


        if (empty($updateData)) {
            return false; // Não há dados para atualizar
        }

        $affected = $database->update(
            $this->table,
            $updateData,
            "{$this->primaryKey} = ?",
            [$this->getAttribute($this->primaryKey)]
        );

        return $affected > 0;
    }

    protected function newFromBuilder(object $attributes): self
    {
        $instance = new static();
        $instance->exists = true;
        // Converte o objeto genérico de PDO::FETCH_OBJ para array para consistência
        $instance->attributes = (array) $attributes;

        // Se o modelo é soft deletable, verifica se está deletado
        if ($instance->softDelete && isset($instance->attributes[$instance->deletedAtColumn])) {
            // O modelo carregado do banco de dados não altera seu status de 'exists'
            // Apenas carregamos o atributo deleted_at
        }
        return $instance;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function toJson(): string
    {
        return json_encode($this->attributes);
    }

    public function __get(string $key)
    {
        return $this->getAttribute($key);
    }

    public function __set(string $key, $value): void
    {
        $this->setAttribute($key, $value);
    }

    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }
}