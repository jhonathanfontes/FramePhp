<?php

namespace Core\Database;

abstract class Model
{
    protected $table;

    public function query(): QueryBuilder
    {
        return new QueryBuilder($this->table);
    }

    public function all(): array
    {
        return $this->query()->get();
    }

    public function find($id)
    {
        return $this->query()->where('id', '=', $id)->first();
    }
}