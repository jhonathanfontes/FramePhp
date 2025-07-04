<?php
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
            // Garante que a aplicação pare se a conexão com o BD falhar no construtor
            ErrorHandler::handleException($e);
            exit(1); // Usar exit(1) para indicar erro
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            try {
                self::$instance = new self();
            } catch (\Exception $e) {
                // Em caso de falha na criação da instância, loga e encerra
                ErrorHandler::handleException($e);
                exit(1);
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
            if (empty(trim($sql))) {
                throw new \InvalidArgumentException("SQL não pode estar vazio.");
            }

            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $this->logError($sql, $params, $e);
            throw $e; // Re-lança a exceção para ser tratada em um nível superior
        }
    }

    public function insert(string $table, array $data): int
    {
        $this->validateTableName($table);
        $this->validateData($data);

        $columns = '`' . implode('`, `', array_keys($data)) . '`';
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO `{$table}` ({$columns}) VALUES ({$placeholders})";

        $this->query($sql, array_values($data));
        return (int) $this->getConnection()->lastInsertId();
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

            $sql = "SELECT {$columns} FROM `{$table}` WHERE {$where} LIMIT 1";

            $stmt = $this->query($sql, $whereParams);
            $result = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch como array associativo por padrão

            return $result !== false ? $result : null;
        } catch (PDOException $e) {
            // Usar o operador null coalescing para $sql caso não esteja definido
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
                throw new \InvalidArgumentException("Limit deve ser maior que zero.");
            }
            $sql .= " LIMIT {$limit}";

            if ($offset !== null) {
                if ($offset < 0) {
                    throw new \InvalidArgumentException("Offset não pode ser negativo.");
                }
                $sql .= " OFFSET {$offset}";
            }
        }

        $stmt = $this->query($sql, $whereParams);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch como array associativo por padrão
    }

    // Métodos de transação
    public function beginTransaction(): void
    {
        $this->getConnection()->beginTransaction();
    }

    public function commit(): void
    {
        $this->getConnection()->commit();
    }

    public function rollback(): void
    {
        $this->getConnection()->rollback();
    }

    public function inTransaction(): bool
    {
        return $this->getConnection()->inTransaction();
    }

    // Métodos de validação
    private function validateTableName(string $table): void
    {
        // Garante que o nome da tabela contenha apenas caracteres válidos (a-z, A-Z, 0-9, _)
        if (empty($table) || !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $table)) {
            throw new \InvalidArgumentException("Nome de tabela inválido: '{$table}'");
        }
    }

    private function validateData(array $data): void
    {
        if (empty($data)) {
            throw new \InvalidArgumentException("Dados não podem estar vazios.");
        }
    }

    private function validateWhereClause(string $where): void
    {
        if (empty(trim($where))) {
            throw new \InvalidArgumentException("Cláusula WHERE não pode estar vazia.");
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

}