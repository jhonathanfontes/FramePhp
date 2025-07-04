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
            exit(1);
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            try {
                self::$instance = new self();
            } catch (\Exception $e) {
                // Usar o ErrorHandler para exibir o erro
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
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        $this->query($sql, array_values($data));
        return (int) $this->getConnection()->getlastInsertId();
    }

    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $set = [];
        foreach (array_keys($data) as $column) {
            $set[] = "{$column} = ?";
        }
        
        $sql = "UPDATE {$table} SET " . implode(', ', $set) . " WHERE {$where}";
        
        $stmt = $this->query($sql, array_merge(array_values($data), $whereParams));
        return $stmt->rowCount();
    }

    public function delete(string $table, string $where, array $whereParams = []): int
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        
        $stmt = $this->query($sql, $whereParams);
        return $stmt->rowCount();
    }

    public function find(string $table, string $columns = '*', string $where, array $whereParams = []): ?array
    {
        try {
            // Log para debug
            error_log("Executando query FIND - Tabela: {$table}, Where: {$where}");
            
            $sql = "SELECT {$columns} FROM {$table} WHERE {$where} LIMIT 1";
            
            // Log da query
            error_log("SQL: " . $sql);
            error_log("Parâmetros: " . json_encode($whereParams));
            
            $stmt = $this->query($sql, $whereParams);
            $result = $stmt->fetch();
            
            // Log do resultado
            error_log("Resultado: " . ($result ? "Registro encontrado" : "Nenhum registro encontrado"));
            
            return $result !== false ? $result : null;
        } catch (PDOException $e) {
            // Log do erro
            error_log("Erro na query FIND: " . $e->getMessage());
            error_log("SQL: " . $sql);
            error_log("Parâmetros: " . json_encode($whereParams));
            
            // Lançar exceção para ser tratada pelo ErrorHandler
            throw $e;
        }
    }

    public function findAll(string $table, string $columns = '*', string $where = '1', array $whereParams = [], string $orderBy = null, int $limit = null, int $offset = null): array
    {
        $sql = "SELECT {$columns} FROM {$table} WHERE {$where}";
        
        if ($orderBy !== null) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";
            
            if ($offset !== null) {
                $sql .= " OFFSET {$offset}";
            }
        }
        
        $stmt = $this->query($sql, $whereParams);
        return $stmt->fetchAll();
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
        // Verifica se as variáveis de ambiente estão definidas antes de usar
        $appDebug = $_ENV['APP_DEBUG'] ?? false;
        $dbDebug = $_ENV['DB_DEBUG'] ?? false;

        if ($appDebug || $dbDebug) {
            error_log("[DB DEBUG] " . $message);
        }
    }
}