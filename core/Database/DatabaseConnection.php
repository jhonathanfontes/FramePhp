<?php

// =============================================================================
// 1. CORE/DATABASE/CONNECTION.PHP - MELHORADO
// =============================================================================

namespace Core\Database;

use PDO;
use PDOException;
use Core\Config\Environment;

class Connection
{
    private static ?self $instance = null;
    private PDO $pdo;
    private array $config;

    private function __construct()
    {
        $this->loadConfig();
        $this->connect();
    }

    private function loadConfig(): void
    {
        $this->config = [
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'bd_model'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ]
        ];
    }

    private function connect(): void
    {
        try {
            $dsn = "mysql:host={$this->config['host']};port={$this->config['port']};dbname={$this->config['database']};charset={$this->config['charset']}";
            
            $this->pdo = new PDO(
                $dsn,
                $this->config['username'],
                $this->config['password'],
                $this->config['options']
            );
        } catch (PDOException $e) {
            throw new \Exception("Erro de conexão: " . $e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    // Métodos adicionais para melhor gerenciamento
    public function reconnect(): void
    {
        $this->connect();
    }

    public function isConnected(): bool
    {
        try {
            return $this->pdo->query('SELECT 1')->fetchColumn() === '1';
        } catch (PDOException $e) {
            return false;
        }
    }

    // Prevenir clonagem e serialização
    private function __clone() {}
    public function __wakeup() {}
}

// =============================================================================
// 2. CORE/DATABASE/DATABASE.PHP - MELHORADO
// =============================================================================

namespace Core\Database;

use PDO;
use PDOException;
use Core\Error\ErrorHandler;

class Database
{
    private static ?self $instance = null;
    private Connection $connection;

    private function __construct()
    {
        try {
            $this->connection = Connection::getInstance();
        } catch (\Exception $e) {
            ErrorHandler::handleException($e);
            exit;
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            try {
                self::$instance = new self();
            } catch (\Exception $e) {
                ErrorHandler::handleException($e);
                exit;
            }
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection->getPdo();
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        try {
            // Validação básica da query
            if (empty(trim($sql))) {
                throw new \InvalidArgumentException("SQL não pode estar vazio");
            }

            $stmt = $this->connection->getPdo()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $this->logError($sql, $params, $e);
            throw $e;
        }
    }

    public function insert(string $table, array $data): int
    {
        $this->validateTableName($table);
        $this->validateData($data);

        // Escapar nomes de colunas
        $columns = '`' . implode('`, `', array_keys($data)) . '`';
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO `{$table}` ({$columns}) VALUES ({$placeholders})";
        
        $this->query($sql, array_values($data));
        return (int) $this->connection->getPdo()->lastInsertId();
    }

    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $this->validateTableName($table);
        $this->validateData($data);
        $this->validateWhereClause($where);

        $set = [];
        foreach (array_keys($data) as $column) {
            $set[] = "`{$column}` = ?";
        }
        
        $sql = "UPDATE `{$table}` SET " . implode(', ', $set) . " WHERE {$where}";
        
        $stmt = $this->query($sql, array_merge(array_values($data), $whereParams));
        return $stmt->rowCount();
    }

    public function delete(string $table, string $where, array $whereParams = []): int
    {
        $this->validateTableName($table);
        $this->validateWhereClause($where);

        $sql = "DELETE FROM `{$table}` WHERE {$where}";
        
        $stmt = $this->query($sql, $whereParams);
        return $stmt->rowCount();
    }

    public function find(string $table, string $columns = '*', string $where, array $whereParams = []): ?array
    {
        try {
            $this->validateTableName($table);
            $this->validateWhereClause($where);
            
            // Log para debug (melhorado)
            $this->logDebug("Executando query FIND - Tabela: {$table}, Where: {$where}");
            
            $sql = "SELECT {$columns} FROM `{$table}` WHERE {$where} LIMIT 1";
            
            $this->logDebug("SQL: " . $sql);
            $this->logDebug("Parâmetros: " . json_encode($whereParams));
            
            $stmt = $this->query($sql, $whereParams);
            $result = $stmt->fetch();
            
            $this->logDebug("Resultado: " . ($result ? "Registro encontrado" : "Nenhum registro encontrado"));
            
            return $result !== false ? $result : null;
        } catch (PDOException $e) {
            $this->logError($sql ?? '', $whereParams, $e);
            throw $e;
        }
    }

    public function findAll(string $table, string $columns = '*', string $where = '1', array $whereParams = [], string $orderBy = null, int $limit = null, int $offset = null): array
    {
        $this->validateTableName($table);
        
        $sql = "SELECT {$columns} FROM `{$table}` WHERE {$where}";
        
        if ($orderBy !== null) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit !== null) {
            if ($limit <= 0) {
                throw new \InvalidArgumentException("Limit deve ser maior que zero");
            }
            $sql .= " LIMIT {$limit}";
            
            if ($offset !== null) {
                if ($offset < 0) {
                    throw new \InvalidArgumentException("Offset não pode ser negativo");
                }
                $sql .= " OFFSET {$offset}";
            }
        }
        
        $stmt = $this->query($sql, $whereParams);
        return $stmt->fetchAll();
    }

    // Métodos de transação
    public function beginTransaction(): void
    {
        $this->connection->getPdo()->beginTransaction();
    }

    public function commit(): void
    {
        $this->connection->getPdo()->commit();
    }

    public function rollback(): void
    {
        $this->connection->getPdo()->rollback();
    }

    public function inTransaction(): bool
    {
        return $this->connection->getPdo()->inTransaction();
    }

    // Métodos de validação
    private function validateTableName(string $table): void
    {
        if (empty($table) || !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $table)) {
            throw new \InvalidArgumentException("Nome de tabela inválido: {$table}");
        }
    }

    private function validateData(array $data): void
    {
        if (empty($data)) {
            throw new \InvalidArgumentException("Dados não podem estar vazios");
        }
    }

    private function validateWhereClause(string $where): void
    {
        if (empty(trim($where))) {
            throw new \InvalidArgumentException("Cláusula WHERE não pode estar vazia");
        }
    }

    // Método de log melhorado
    private function logError(string $sql, array $params, PDOException $e): void
    {
        error_log("=== ERRO DE BANCO DE DADOS ===");
        error_log("Erro: " . $e->getMessage());
        error_log("Código: " . $e->getCode());
        error_log("SQL: " . $sql);
        error_log("Parâmetros: " . json_encode($params));
        error_log("Stack trace: " . $e->getTraceAsString());
        error_log("===============================");
    }

    private function logDebug(string $message): void
    {
        if (($_ENV['APP_DEBUG'] ?? false) || ($_ENV['DB_DEBUG'] ?? false)) {
            error_log("[DB DEBUG] " . $message);
        }
    }
}

// =============================================================================
// 3. CORE/DATABASE/QUERYBUILDER.PHP - MELHORADO
// =============================================================================

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

    public function __construct(string $table)
    {
        if (empty($table)) {
            throw new \InvalidArgumentException("Nome da tabela não pode estar vazio");
        }
        $this->table = $table;
        $this->connection = Connection::getInstance();
    }

    public function select(string $columns = '*'): self
    {
        $this->select = $columns;
        return $this;
    }

    public function where(string $column, string $operator, $value): self
    {
        $this->validateOperator($operator);
        $this->where[] = "{$column} {$operator} ?";
        $this->params[] = $value;
        return $this;
    }

    public function orWhere(string $column, string $operator, $value): self
    {
        $this->validateOperator($operator);
        $connector = empty($this->where) ? '' : ' OR ';
        $this->where[] = $connector . "{$column} {$operator} ?";
        $this->params[] = $value;
        return $this;
    }

    public function whereIn(string $column, array $values): self
    {
        if (empty($values)) {
            return $this;
        }
        
        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        $this->where[] = "{$column} IN ({$placeholders})";
        $this->params = array_merge($this->params, $values);
        return $this;
    }

    public function whereNull(string $column): self
    {
        $this->where[] = "{$column} IS NULL";
        return $this;
    }

    public function whereNotNull(string $column): self
    {
        $this->where[] = "{$column} IS NOT NULL";
        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = "JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = "LEFT JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $direction = strtoupper($direction);
        if (!in_array($direction, ['ASC', 'DESC'])) {
            throw new \InvalidArgumentException("Direção inválida: {$direction}");
        }
        
        $this->orderBy = " ORDER BY {$column} {$direction}";
        return $this;
    }

    public function limit(int $limit): self
    {
        if ($limit <= 0) {
            throw new \InvalidArgumentException("Limit deve ser maior que zero");
        }
        
        $this->limit = " LIMIT {$limit}";
        return $this;
    }

    public function offset(int $offset): self
    {
        if ($offset < 0) {
            throw new \InvalidArgumentException("Offset não pode ser negativo");
        }
        
        $this->limit .= " OFFSET {$offset}";
        return $this;
    }

    public function get(): array
    {
        $sql = $this->buildQuery();
        
        try {
            $this->logDebug("Executando query: " . $sql);
            $this->logDebug("Parâmetros: " . json_encode($this->params));
            
            $stmt = $this->connection->getPdo()->prepare($sql);
            $stmt->execute($this->params);
            
            $results = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            $this->logDebug("Resultados encontrados: " . count($results));
            
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
        
        $this->limit = $originalLimit; // Restaurar limit original
        
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
            $this->select = $originalSelect; // Restaurar select original
            
            return (int) $result->total;
        } catch (PDOException $e) {
            $this->select = $originalSelect; // Restaurar select original
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
        
        if (!empty($this->where)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->where);
        }
        
        $sql .= $this->orderBy . $this->limit;
        
        return $sql;
    }

    private function validateOperator(string $operator): void
    {
        $validOperators = ['=', '!=', '<>', '<', '>', '<=', '>=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN'];
        if (!in_array(strtoupper($operator), $validOperators)) {
            throw new \InvalidArgumentException("Operador inválido: {$operator}");
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
        if (($_ENV['APP_DEBUG'] ?? false) || ($_ENV['DB_DEBUG'] ?? false)) {
            error_log("[QB DEBUG] " . $message);
        }
    }
}

// =============================================================================
// 4. CORE/DATABASE/MODEL.PHP - MELHORADO
// =============================================================================

namespace Core\Database;

abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $guarded = [];
    protected array $attributes = [];
    protected bool $exists = false;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public function query(): QueryBuilder
    {
        return new QueryBuilder($this->table);
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

    public function delete(): bool
    {
        if (!$this->exists) {
            return false;
        }
        
        $database = Database::getInstance();
        $affected = $database->delete($this->table, "{$this->primaryKey} = ?", [$this->getAttribute($this->primaryKey)]);
        
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
        if (!empty($this->fillable)) {
            return in_array($key, $this->fillable);
        }
        
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
        $affected = $database->update(
            $this->table,
            $this->attributes,
            "{$this->primaryKey} = ?",
            [$this->getAttribute($this->primaryKey)]
        );
        
        return $affected > 0;
    }

    protected function newFromBuilder(object $attributes): self
    {
        $instance = new static();
        $instance->exists = true;
        $instance->attributes = (array) $attributes;
        
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