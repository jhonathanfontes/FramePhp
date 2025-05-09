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
        $this->table = $table;
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