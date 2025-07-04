<?php

if (!function_exists('env')) {
    /**
     * Obtém o valor de uma variável de ambiente
     *
     * @param string $chave
     * @param mixed $padrao
     * @return mixed
     */
    function env(string $chave, $padrao = null)
    {
        if (isset($_ENV[$chave])) {
            $valor = $_ENV[$chave];

            switch (strtolower($valor)) {
                case 'true':
                case '(true)':
                    return true;
                case 'false':
                case '(false)':
                    return false;
                case 'null':
                case '(null)':
                    return null;
                case 'empty':
                case '(empty)':
                    return '';
            }

            return $valor;
        }

        return $padrao;
    }

    function dd($params, $exit = true)
    {
        echo '<pre>';
        var_dump($params);
        echo '</pre>';

        if ($exit) {
            exit;
        }
    }

}