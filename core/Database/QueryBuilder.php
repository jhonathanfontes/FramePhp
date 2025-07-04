<?php

namespace Core\Database;

use PDO;
use PDOException;

class QueryBuilder
{
    private string $table;
    private array $where = [];
    private array $params = [];
    private string $select = '*';
    private string $orderBy = '';
    private string $limit = '';
    private array $joins = [];
    private Connection $connection;
    private bool $softDeleteEnabled = false; // Adicionado para controlar soft delete
    private string $deletedAtColumn = 'deleted_at'; // Coluna de soft delete

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

    public function where(string $column, $value, string $operator = '='): self
    {
        $this->validateOperator($operator);
        $this->where[] = "(`{$column}` {$operator} ?)"; // Adiciona parênteses para agrupar
        $this->params[] = $value;
        return $this;
    }

    public function orWhere(string $column, string $operator, $value): self
    {
        $this->validateOperator($operator);
        $connector = empty($this->where) ? '' : ' OR ';
        $this->where[] = "{$connector}(`{$column}` {$operator} ?)";
        $this->params[] = $value;
        return $this;
    }

    public function whereIn(string $column, array $values): self
    {
        if (empty($values)) {
            // Se o array estiver vazio, a condição IN seria inválida.
            // Poderíamos adicionar uma condição que sempre falha para evitar resultados
            // ou retornar sem adicionar uma condição (dependendo da lógica desejada).
            // Por simplicidade, retornamos sem adicionar WHERE clause.
            return $this;
        }

        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        $this->where[] = "(`{$column}` IN ({$placeholders}))";
        $this->params = array_merge($this->params, $values);
        return $this;
    }

    public function whereNull(string $column): self
    {
        $this->where[] = "(`{$column}` IS NULL)";
        return $this;
    }

    public function whereNotNull(string $column): self
    {
        $this->where[] = "(`{$column}` IS NOT NULL)";
        return $this;
    }

    public function join(string $table, string $first, string $second, string $operator = '='): self
    {
        $this->joins[] = "JOIN `{$table}` ON `{$first}` {$operator} `{$second}`";
        return $this;
    }

    public function leftJoin(string $table, string $first, string $second, string $operator = '='): self
    {
        $this->joins[] = "LEFT JOIN `{$table}` ON `{$first}` {$operator} `{$second}`";
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $direction = strtoupper($direction);
        if (!in_array($direction, ['ASC', 'DESC'])) {
            throw new \InvalidArgumentException("Direção de ordenação inválida: '{$direction}'. Use 'ASC' ou 'DESC'.");
        }

        $this->orderBy = " ORDER BY `{$column}` {$direction}";
        return $this;
    }

    public function limit(int $limit): self
    {
        if ($limit <= 0) {
            throw new \InvalidArgumentException("Limit deve ser maior que zero.");
        }

        $this->limit = " LIMIT {$limit}";
        return $this;
    }

    public function offset(int $offset): self
    {
        if ($offset < 0) {
            throw new \InvalidArgumentException("Offset não pode ser negativo.");
        }

        // Garante que o LIMIT foi definido antes do OFFSET
        if (empty($this->limit)) {
            // Pode-se definir um limite padrão alto ou forçar o desenvolvedor a chamar limit() primeiro
            // Por simplicidade, adicionamos uma nota aqui.
            error_log("Atenção: OFFSET usado sem LIMIT explícito. Pode não funcionar como esperado em todos os bancos de dados.");
        }
        $this->limit .= " OFFSET {$offset}";
        return $this;
    }

    public function get(): array
    {
        $sql = $this->buildQuery();

        try {

            $stmt = $this->connection->getPdo()->prepare($sql);
            $stmt->execute($this->params);

            $results = $stmt->fetchAll(PDO::FETCH_OBJ); // Retorna objetos para consistência com Model
            return $results;

        } catch (PDOException $e) {
            $this->logError($sql, $e);
            throw $e;
        }
    }

    public function first(): ?object
    {
        $originalLimit = $this->limit;
        $this->limit(1);

        $result = $this->get();

        $this->limit = $originalLimit; // Restaura o limite original para reutilização do QueryBuilder

        return $result[0] ?? null;
    }

    public function count(): int
    {
        $originalSelect = $this->select;
        $this->select = 'COUNT(*) as total';

        $sql = $this->buildQuery();

        try {
            $stmt = $this->connection->getPdo()->prepare($sql);
            $stmt->execute($this->params);

            $result = $stmt->fetch(PDO::FETCH_OBJ);
            $this->select = $originalSelect; // Restaura o select original

            return (int) $result->total;
        } catch (PDOException $e) {
            $this->select = $originalSelect; // Garante que o select original seja restaurado mesmo em erro
            $this->logError($sql, $e);
            throw $e;
        }
    }

    public function exists(): bool
    {
        return $this->count() > 0;
    }

    public function toSql(): string
    {
        return $this->buildQuery();
    }

    private function buildQuery(): string
    {
        $sql = "SELECT {$this->select} FROM `{$this->table}`";

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        $whereClauses = $this->where;

        // Adiciona condição de soft delete se habilitado e não desativado por withTrashed()
        if ($this->softDeleteEnabled) {
            $whereClauses[] = "`{$this->deletedAtColumn}` IS NULL";
        }

        if (!empty($whereClauses)) {
            $sql .= ' WHERE ' . implode(' AND ', $whereClauses);
        }

        $sql .= $this->orderBy . $this->limit;

        return $sql;
    }

    private function validateOperator(string $operator): void
    {
        $validOperators = ['=', '!=', '<>', '<', '>', '<=', '>=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'IS', 'IS NOT']; // Adicionado IS/IS NOT
        if (!in_array(strtoupper($operator), $validOperators)) {
            throw new \InvalidArgumentException("Operador inválido: '{$operator}'.");
        }
    }

    private function logError(string $sql, PDOException $e): void
    {
        error_log("=== ERRO NO QUERYBUILDER ===");
        error_log("Erro: " . $e->getMessage());
        error_log("SQL: " . $sql);
        error_log("Parâmetros: " . json_encode($this->params));
        error_log("=============================");
    }

    private function logDebug(string $message): void
    {
        $appDebug = $_ENV['APP_DEBUG'] ?? false;
        $dbDebug = $_ENV['DB_DEBUG'] ?? false;

        if ($appDebug || $dbDebug) {
            error_log("[QB DEBUG] " . $message);
        }
    }
}