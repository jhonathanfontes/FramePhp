<?php

namespace Core\Config;

class Environment
{
    private static $variables = [];

    public static function load(string $path)
    {
        if (!file_exists($path)) {
            throw new \Exception("Arquivo .env não encontrado em: {$path}");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Ignora comentários
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Verifica se a linha contém um sinal de igual
            if (strpos($line, '=') === false) {
                continue;
            }

            // Divide a linha em nome e valor
            $parts = explode('=', $line, 2);
            if (count($parts) !== 2) {
                continue;
            }

            $name = trim($parts[0]);
            $value = trim($parts[1]);

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                self::$variables[$name] = $value;
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }

    public static function get(string $key, $default = null)
    {
        return self::$variables[$key] ?? $default;
    }
}