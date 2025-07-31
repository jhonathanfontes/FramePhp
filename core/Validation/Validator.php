<?php

namespace Core\Validation;

class Validator
{
    private array $data = [];
    private array $rules = [];
    private array $errors = [];
    private array $customMessages = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Método estático para criar uma instância do validador e validar dados
     */
    public static function make(array $data, array $rules, array $messages = []): self
    {
        $validator = new self($data);
        $validator->validate($rules, $messages);
        return $validator;
    }

    /**
     * Verifica se houve falhas na validação
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function validate(array $rules, array $messages = []): bool
    {
        $this->rules = $rules;
        $this->customMessages = $messages;
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $this->validateField($field, $fieldRules);
        }

        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getFirstError(): ?string
    {
        return !empty($this->errors) ? reset($this->errors)[0] : null;
    }

    private function validateField(string $field, string $rules): void
    {
        $rulesArray = explode('|', $rules);
        $value = $this->data[$field] ?? null;

        foreach ($rulesArray as $rule) {
            $this->applyRule($field, $value, $rule);
        }
    }

    private function applyRule(string $field, $value, string $rule): void
    {
        $parameters = [];
        
        if (strpos($rule, ':') !== false) {
            [$rule, $paramString] = explode(':', $rule, 2);
            $parameters = explode(',', $paramString);
        }

        switch ($rule) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->addError($field, "O campo {$field} é obrigatório.");
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "O campo {$field} deve ser um email válido.");
                }
                break;

            case 'min':
                $min = (int)$parameters[0];
                if (!empty($value) && strlen($value) < $min) {
                    $this->addError($field, "O campo {$field} deve ter pelo menos {$min} caracteres.");
                }
                break;

            case 'max':
                $max = (int)$parameters[0];
                if (!empty($value) && strlen($value) > $max) {
                    $this->addError($field, "O campo {$field} deve ter no máximo {$max} caracteres.");
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, "O campo {$field} deve ser numérico.");
                }
                break;

            case 'alpha':
                if (!empty($value) && !ctype_alpha($value)) {
                    $this->addError($field, "O campo {$field} deve conter apenas letras.");
                }
                break;

            case 'alphanumeric':
                if (!empty($value) && !ctype_alnum($value)) {
                    $this->addError($field, "O campo {$field} deve conter apenas letras e números.");
                }
                break;

            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if ($value !== ($this->data[$confirmField] ?? null)) {
                    $this->addError($field, "A confirmação do campo {$field} não confere.");
                }
                break;

            case 'unique':
                // Implementar validação de unicidade no banco
                break;
        }
    }

    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        
        $customMessage = $this->customMessages["{$field}.{$this->getCurrentRule()}"] ?? $message;
        $this->errors[$field][] = $customMessage;
    }

    private function getCurrentRule(): string
    {
        return 'default';
    }
}
