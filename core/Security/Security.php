<?php

namespace Core\Security;

class Security
{
    private static $instance = null;
    private $token;

    private function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->regenerateToken();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function regenerateToken(): void
    {
        $this->token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $this->token;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function validateToken(string $token): bool
    {
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    public function validateInput($data, array $rules): array
    {
        $errors = [];
        foreach ($rules as $field => $rule) {
            if (!isset($data[$field])) {
                $errors[$field] = "Campo obrigatório";
                continue;
            }

            $value = $data[$field];
            foreach ($rule as $validation => $param) {
                switch ($validation) {
                    case 'required':
                        if (empty($value)) {
                            $errors[$field] = "Campo obrigatório";
                        }
                        break;
                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field] = "Email inválido";
                        }
                        break;
                    case 'min':
                        if (strlen($value) < $param) {
                            $errors[$field] = "Mínimo de {$param} caracteres";
                        }
                        break;
                    case 'max':
                        if (strlen($value) > $param) {
                            $errors[$field] = "Máximo de {$param} caracteres";
                        }
                        break;
                }
            }
        }
        return $errors;
    }
}