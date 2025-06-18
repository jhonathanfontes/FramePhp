<?php

namespace Core\Validation;

use Core\Database\Database;

class Validator
{
    protected array $data;
    protected array $rules;
    protected array $errors = [];
    protected Database $db;

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->db = Database::getInstance();
        $this->validate();
    }

    /**
     * Método de fábrica estático para criar uma instância.
     */
    public static function make(array $data, array $rules): self
    {
        return new self($data, $rules);
    }

    /**
     * Retorna true se a validação falhou.
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Retorna o array de erros.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Itera sobre as regras e executa a validação.
     */
    protected function validate(): void
    {
        foreach ($this->rules as $field => $fieldRules) {
            $rules = explode('|', $fieldRules);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                [$ruleName, $params] = array_pad(explode(':', $rule, 2), 2, '');
                $method = 'validate' . ucfirst($ruleName);
                
                if (method_exists($this, $method)) {
                    $this->$method($field, $value, explode(',', $params));
                }
            }
        }
    }

    protected function addError(string $field, string $message): void
    {
        // Adiciona erro apenas para o primeiro erro encontrado no campo
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    // --- MÉTODOS DE VALIDAÇÃO ---

    protected function validateRequired(string $field, $value): void
    {
        if (is_null($value) || (is_string($value) && trim($value) === '')) {
            $this->addError($field, "Este campo é obrigatório.");
        }
    }

    protected function validateEmail(string $field, $value): void
    {
        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "Por favor, insira um e-mail válido.");
        }
    }

    protected function validateMin(string $field, $value, array $params): void
    {
        $min = $params[0] ?? 0;
        if (strlen($value) < $min) {
            $this->addError($field, "Este campo deve ter no mínimo {$min} caracteres.");
        }
    }

    protected function validateConfirmed(string $field, $value): void
    {
        $confirmationField = $field . '_confirmation';
        if ($value !== ($this->data[$confirmationField] ?? null)) {
            $this->addError($field, 'A confirmação de senha não confere.');
        }
    }
}