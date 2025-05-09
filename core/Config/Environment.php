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
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

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