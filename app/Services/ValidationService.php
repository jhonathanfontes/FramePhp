<?php

namespace App\Services;

class ValidationService
{
    /**
     * Valida CPF
     */
    public static function validarCpf($cpf)
    {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        // Verifica se tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }
        
        // Verifica se todos os dígitos são iguais
        if (preg_match('/^(\d)\1+$/', $cpf)) {
            return false;
        }
        
        // Calcula o primeiro dígito verificador
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += $cpf[$i] * (10 - $i);
        }
        $resto = $soma % 11;
        $dv1 = ($resto < 2) ? 0 : 11 - $resto;
        
        // Calcula o segundo dígito verificador
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += $cpf[$i] * (11 - $i);
        }
        $soma += $dv1 * 2;
        $resto = $soma % 11;
        $dv2 = ($resto < 2) ? 0 : 11 - $resto;
        
        // Verifica se os dígitos verificadores estão corretos
        return ($cpf[9] == $dv1 && $cpf[10] == $dv2);
    }
    
    /**
     * Valida CNPJ
     */
    public static function validarCnpj($cnpj)
    {
        // Remove caracteres não numéricos
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        
        // Verifica se tem 14 dígitos
        if (strlen($cnpj) != 14) {
            return false;
        }
        
        // Verifica se todos os dígitos são iguais
        if (preg_match('/^(\d)\1+$/', $cnpj)) {
            return false;
        }
        
        // Calcula o primeiro dígito verificador
        $soma = 0;
        $pesos = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        
        for ($i = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $pesos[$i];
        }
        
        $resto = $soma % 11;
        $dv1 = ($resto < 2) ? 0 : 11 - $resto;
        
        // Calcula o segundo dígito verificador
        $soma = 0;
        $pesos = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        
        for ($i = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $pesos[$i];
        }
        
        $resto = $soma % 11;
        $dv2 = ($resto < 2) ? 0 : 11 - $resto;
        
        // Verifica se os dígitos verificadores estão corretos
        return ($cnpj[12] == $dv1 && $cnpj[13] == $dv2);
    }
    
    /**
     * Valida e-mail
     */
    public static function validarEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Valida telefone brasileiro
     */
    public static function validarTelefone($telefone)
    {
        // Remove caracteres não numéricos
        $telefone = preg_replace('/[^0-9]/', '', $telefone);
        
        // Verifica se tem entre 10 e 11 dígitos
        if (strlen($telefone) < 10 || strlen($telefone) > 11) {
            return false;
        }
        
        // Verifica se começa com 9 (celular) ou 2-8 (fixo)
        if (strlen($telefone) == 11) {
            return in_array($telefone[2], ['6', '7', '8', '9']);
        }
        
        return in_array($telefone[2], ['2', '3', '4', '5', '6', '7', '8']);
    }
    
    /**
     * Valida CEP brasileiro
     */
    public static function validarCep($cep)
    {
        // Remove caracteres não numéricos
        $cep = preg_replace('/[^0-9]/', '', $cep);
        
        // Verifica se tem 8 dígitos
        return strlen($cep) == 8;
    }
    
    /**
     * Formata CPF
     */
    public static function formatarCpf($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }
    
    /**
     * Formata CNPJ
     */
    public static function formatarCnpj($cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        return substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);
    }
    
    /**
     * Formata telefone
     */
    public static function formatarTelefone($telefone)
    {
        $telefone = preg_replace('/[^0-9]/', '', $telefone);
        
        if (strlen($telefone) == 11) {
            return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 5) . '-' . substr($telefone, 7, 4);
        }
        
        return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 4) . '-' . substr($telefone, 6, 4);
    }
    
    /**
     * Formata CEP
     */
    public static function formatarCep($cep)
    {
        $cep = preg_replace('/[^0-9]/', '', $cep);
        return substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
    }
    
    /**
     * Valida senha forte
     */
    public static function validarSenha($senha)
    {
        // Mínimo 8 caracteres, pelo menos uma letra maiúscula, uma minúscula e um número
        return strlen($senha) >= 8 && 
               preg_match('/[A-Z]/', $senha) && 
               preg_match('/[a-z]/', $senha) && 
               preg_match('/[0-9]/', $senha);
    }
    
    /**
     * Valida data
     */
    public static function validarData($data, $formato = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($formato, $data);
        return $d && $d->format($formato) === $data;
    }
    
    /**
     * Valida se é maior de idade
     */
    public static function validarMaiorIdade($dataNascimento)
    {
        $hoje = new \DateTime();
        $nascimento = new \DateTime($dataNascimento);
        $idade = $hoje->diff($nascimento)->y;
        
        return $idade >= 18;
    }
    
    /**
     * Sanitiza string
     */
    public static function sanitizarString($string)
    {
        return htmlspecialchars(strip_tags(trim($string)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Valida URL
     */
    public static function validarUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Valida IP
     */
    public static function validarIp($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }
    
    /**
     * Valida se é um número inteiro
     */
    public static function validarInteiro($numero)
    {
        return filter_var($numero, FILTER_VALIDATE_INT) !== false;
    }
    
    /**
     * Valida se é um número decimal
     */
    public static function validarDecimal($numero)
    {
        return filter_var($numero, FILTER_VALIDATE_FLOAT) !== false;
    }
    
    /**
     * Valida se está dentro de um range
     */
    public static function validarRange($numero, $min, $max)
    {
        return $numero >= $min && $numero <= $max;
    }
    
    /**
     * Valida tamanho de string
     */
    public static function validarTamanho($string, $min, $max)
    {
        $tamanho = strlen($string);
        return $tamanho >= $min && $tamanho <= $max;
    }
    
    /**
     * Valida se contém apenas letras
     */
    public static function validarApenasLetras($string)
    {
        return preg_match('/^[a-zA-ZÀ-ÿ\s]+$/', $string);
    }
    
    /**
     * Valida se contém apenas números
     */
    public static function validarApenasNumeros($string)
    {
        return preg_match('/^[0-9]+$/', $string);
    }
    
    /**
     * Valida se contém apenas letras e números
     */
    public static function validarAlfanumerico($string)
    {
        return preg_match('/^[a-zA-Z0-9]+$/', $string);
    }
} 