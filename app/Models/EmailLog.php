<?php

namespace App\Models;

use Core\Database\Model;
use Core\Database\Database;
use PDO;

class EmailLog extends Model
{
    protected $table = 'email_logs';
    protected $db;
    
    protected $fillable = [
        'template',
        'recipient_email',
        'recipient_name',
        'subject',
        'content',
        'status',
        'error_message'
    ];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create(array $data): bool
    {
        try {
            $fields = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            
            $sql = "INSERT INTO {$this->table} ($fields) VALUES ($placeholders)";
            
            return $this->db->query($sql, array_values($data))->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("Erro ao criar registro de e-mail: " . $e->getMessage());
            return false;
        }
    }

    public function insert(array $data): bool
    {
        return $this->create($data);
    }

    public static function logEmail(array $data): bool
    {
        try {
            $instance = new static();
            return $instance->insert($data);
        } catch (\Exception $e) {
            error_log("Erro ao registrar log de e-mail: " . $e->getMessage());
            return false;
        }
    }
}