<?php

namespace Core\Database;

use PDO;
use PDOException;
// use Core\Config\Environment; // Assumindo que a função env() é global ou carregada

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
        // Certifique-se de que a função env() está disponível globalmente ou via um autoloader
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
            // Em vez de uma exceção genérica, pode-se criar uma exceção personalizada aqui
            throw new \RuntimeException("Erro de conexão com o banco de dados: " . $e->getMessage(), (int)$e->getCode(), $e);
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

    public function reconnect(): void
    {
        $this->connect();
    }

    public function isConnected(): bool
    {
        try {
            // Executa uma consulta leve para verificar a conexão
            return $this->pdo->query('SELECT 1')->fetchColumn() === '1';
        } catch (PDOException $e) {
            return false;
        }
    }

    // Previne a clonagem da instância Singleton
    private function __clone()
    {
    }

    // Previne a desserialização da instância Singleton
    public function __wakeup()
    {
    }
}