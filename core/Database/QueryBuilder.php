<?php

namespace Core\Database;

class QueryBuilder
{
    private $table;
    private $where = [];
    private $params = [];
    private $select = '*';
    private $orderBy = '';
    private $limit = '';

    public function __construct(string $table)
    {
        if (empty($table)) {
            throw new \InvalidArgumentException("Nome da tabela não pode estar vazio.");
        }
        $this->table = $table;
        $this->connection = Connection::getInstance();
    }

    // Define a coluna de soft delete, se for diferente de 'deleted_at'
    public function setDeletedAtColumn(string $column): self
    {
        $this->deletedAtColumn = $column;
        return $this;
    }

    // Habilita o soft delete para esta instância do QueryBuilder
    public function enableSoftDelete(): self
    {
        $this->softDeleteEnabled = true;
        return $this;
    }

    // Inclui registros soft-deletados na consulta
    public function withTrashed(): self
    {
        $this->softDeleteEnabled = false; // Desativa a condição "IS NULL"
        return $this;
    }

    public function select(string $columns = '*'): self
    {
        $this->select = $columns;
        return $this;
    }

    public function where(string $column, string $operator, $value): self
    {
        $this->where[] = "{$column} {$operator} ?";
        $this->params[] = $value;
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy = " ORDER BY {$column} {$direction}";
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = " LIMIT {$limit}";
        return $this;
    }

    public function get(): array
    {
        $where = !empty($this->where) ? ' WHERE ' . implode(' AND ', $this->where) : '';
        $sql = "SELECT {$this->select} FROM {$this->table}{$where}{$this->orderBy}{$this->limit}";

        $stmt = Connection::getInstance()->getPdo()->prepare($sql);
        $stmt->execute($this->params);

        return $stmt->fetchAll();
    }

    public function first()
    {
        $this->limit(1);
        $result = $this->get();
        return $result[0] ?? null;
    }
}