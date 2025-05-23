<?php

/**
 * Obtém o valor de uma variável de ambiente
 *
 * @param string $key Nome da variável
 * @param mixed $default Valor padrão caso a variável não exista
 * @return mixed
 */
function env(string $key, $default = null)
{
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    
    if ($value === false) {
        return $default;
    }
    
    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'empty':
        case '(empty)':
            return '';
        case 'null':
        case '(null)':
            return null;
    }
    
    return $value;
}

/**
 * Obtém um valor de configuração
 *
 * @param string $key Chave de configuração no formato 'arquivo.chave'
 * @param mixed $default Valor padrão caso a configuração não exista
 * @return mixed
 */
function config(string $key, $default = null)
{
    static $config = [];
    
    // Se a configuração já foi carregada, retorna o valor
    if (isset($config[$key])) {
        return $config[$key];
    }
    
    // Divide a chave em arquivo e chave
    $parts = explode('.', $key);
    $file = $parts[0];
    $configKey = $parts[1] ?? null;
    
    // Carrega o arquivo de configuração
    $filePath = BASE_PATH . "/config/{$file}.php";
    
    if (!file_exists($filePath)) {
        return $default;
    }
    
    // Carrega as configurações do arquivo
    $fileConfig = require $filePath;
    
    // Se não há uma chave específica, retorna todas as configurações do arquivo
    if ($configKey === null) {
        $config[$key] = $fileConfig;
        return $fileConfig;
    }
    
    // Armazena o valor para uso futuro
    $config[$key] = $fileConfig[$configKey] ?? $default;
    
    return $config[$key];
}

/**
 * Define o idioma atual
 *
 * @param string $locale Código do idioma
 * @return void
 */
function set_locale(string $locale): void
{
    \Core\Translation\Translator::getInstance()->setLocale($locale);
}

/**
 * Obtém o idioma atual
 *
 * @return string
 */
function get_locale(): string
{
    return \Core\Translation\Translator::getInstance()->getLocale();
}

/**
 * Traduz uma string
 *
 * @param string $key Chave de tradução
 * @param array $parameters Parâmetros para substituição
 * @param string $domain Domínio da tradução
 * @return string
 */
function trans(string $key, array $parameters = [], string $domain = 'messages'): string
{
    return \Core\Translation\Translator::getInstance()->trans($key, $parameters, $domain);
}

/**
 * Alias para a função trans()
 */
function __(string $key, array $parameters = [], string $domain = 'messages'): string
{
    return trans($key, $parameters, $domain);
}

/**
 * Gera uma URL base
 *
 * @param string $path Caminho relativo
 * @return string
 */
function base_url(string $path = ''): string
{
    $baseUrl = rtrim(config('app.url', 'http://localhost'), '/');
    $path = ltrim($path, '/');
    
    return $path ? "{$baseUrl}/{$path}" : $baseUrl;
}

/**
 * Obtém o nome da aplicação
 *
 * @return string
 */
function app_name(): string
{
    return config('app.name', 'FramePhp');
}

/**
 * Obtém a versão da aplicação
 *
 * @return string
 */
function app_version(): string
{
    return config('app.version', '1.0.0');
}


if (!function_exists('redirect')) {
    /**
     * Redireciona para uma URL específica
     *
     * @param string $url URL para redirecionamento
     * @return void
     */
    function redirect(string $url)
    {
        header('Location: ' . $url);
        exit;
    }
}