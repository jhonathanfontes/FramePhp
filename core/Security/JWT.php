<?php

namespace Core\Security;

use Core\Config\Environment;

class JWT
{
    private static $key;
    private static $blacklist = [];
    private static $defaultExpiration = 3600; // 1 hora em segundos
    
    public static function init()
    {
        self::$key = Environment::get('APP_KEY', 'sua-chave-secreta-aqui');
    }

    public static function encode(array $payload, int $expiration = null): string
    {
        // Adiciona timestamp de criação se não existir
        if (!isset($payload['iat'])) {
            $payload['iat'] = time();
        }
        
        // Adiciona expiração se não existir
        if (!isset($payload['exp'])) {
            $payload['exp'] = time() + ($expiration ?? self::$defaultExpiration);
        }

        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        $header = base64_encode(json_encode($header));
        $payload = base64_encode(json_encode($payload));
        
        $signature = hash_hmac('sha256', "$header.$payload", self::$key, true);
        $signature = base64_encode($signature);

        return "$header.$payload.$signature";
    }

    public static function decode(string $token)
    {
        if (self::isBlacklisted($token)) {
            throw new \Exception('Token revogado');
        }

        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            throw new \Exception('Token inválido');
        }

        [$header, $payload, $signature] = $parts;

        $validSignature = hash_hmac(
            'sha256',
            "$header.$payload",
            self::$key,
            true
        );
        
        $validSignature = base64_encode($validSignature);

        if ($signature !== $validSignature) {
            throw new \Exception('Assinatura inválida');
        }

        $payload = json_decode(base64_decode($payload), true);

        // Verifica expiração
        if (isset($payload['exp']) && time() > $payload['exp']) {
            throw new \Exception('Token expirado');
        }

        return $payload;
    }

    /**
     * Valida um token JWT e verifica sua expiração
     * 
     * @param string $token O token JWT a ser validado
     * @param bool $checkExpiration Se deve verificar a expiração do token
     * @return bool True se o token for válido, false caso contrário
     */
    public static function validate(string $token, bool $checkExpiration = true): bool
    {
        try {
            if (self::isBlacklisted($token)) {
                return false;
            }

            $parts = explode('.', $token);
            
            if (count($parts) !== 3) {
                return false;
            }

            [$header, $payload, $signature] = $parts;

            $validSignature = hash_hmac(
                'sha256',
                "$header.$payload",
                self::$key,
                true
            );
            
            $validSignature = base64_encode($validSignature);

            if ($signature !== $validSignature) {
                return false;
            }

            if ($checkExpiration) {
                $payload = json_decode(base64_decode($payload), true);
                if (isset($payload['exp']) && time() > $payload['exp']) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function refresh(string $token): string
    {
        $payload = self::decode($token);
        
        // Revoga o token antigo
        self::blacklist($token);
        
        // Remove campos de tempo antigos
        unset($payload['iat']);
        unset($payload['exp']);
        
        // Adiciona nova expiração automaticamente no encode
        return self::encode($payload);
    }

    public static function blacklist(string $token): void
    {
        self::$blacklist[] = $token;
    }

    private static function isBlacklisted(string $token): bool
    {
        return in_array($token, self::$blacklist);
    }
}