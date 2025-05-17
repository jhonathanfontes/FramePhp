<?php

namespace Core\Database;

use PDO;
use PDOException;
use Core\Error\ErrorHandler;

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $database = $_ENV['DB_DATABASE'] ?? 'framephp';
        $username = $_ENV['DB_USERNAME'] ?? 'root';
        $password = $_ENV['DB_PASSWORD'] ?? '';

        try {
            $this->connection = new PDO(
                "mysql:host={$host};port={$port};dbname={$database}",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]
            );
        } catch (PDOException $e) {
            // Em vez de encerrar a execução, lançamos uma exceção personalizada
           // throw new \Exception("Erro de conexão com o banco de dados: " . $e->getMessage());
            // Usar o ErrorHandler para exibir o erro
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
                // Usar o ErrorHandler para exibir o erro
                ErrorHandler::handleException($e);
                exit;
            }
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        $this->query($sql, array_values($data));
        return (int) $this->connection->lastInsertId();
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

    public function find(string $table, string $where, array $whereParams = [], string $columns = '*'): ?array
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

    public function findAll(string $table, string $where = '1', array $whereParams = [], string $columns = '*', string $orderBy = null, int $limit = null, int $offset = null): array
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
}