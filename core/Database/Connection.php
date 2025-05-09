<?php

namespace Core\Database;

use PDO;
use PDOException;
use Core\Config\Environment;

class Connection
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        try {
            $host = Environment::get('DB_HOST', '127.0.0.1');
            $db   = Environment::get('DB_DATABASE', 'framephp');
            $user = Environment::get('DB_USERNAME', 'root');
            $pass = Environment::get('DB_PASSWORD', '');
            $dsn  = "mysql:host={$host};dbname={$db};charset=utf8mb4";

            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
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
}