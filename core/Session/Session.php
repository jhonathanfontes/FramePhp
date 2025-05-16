<?php

namespace Core\Session;

class Session
{
    /**
     * Inicializa a sessão se ainda não estiver ativa
     */
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Verifica se uma chave existe na sessão
     *
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Obtém um valor da sessão
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Define um valor na sessão
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Remove um valor da sessão
     *
     * @param string $key
     * @return void
     */
    public static function remove(string $key): void
    {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Limpa todos os dados da sessão
     *
     * @return void
     */
    public static function clear(): void
    {
        self::start();
        $_SESSION = [];
    }

    /**
     * Destrói completamente a sessão
     *
     * @return void
     */
    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];
        
        // Limpa o cookie da sessão
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        session_destroy();
    }

    /**
     * Define um valor flash na sessão (disponível apenas para a próxima requisição)
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function flash(string $key, $value): void
    {
        self::start();
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * Obtém um valor flash da sessão
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getFlash(string $key, $default = null)
    {
        self::start();
        return $_SESSION['_flash'][$key] ?? $default;
    }

    /**
     * Limpa todos os valores flash após serem lidos
     *
     * @return void
     */
    public static function clearFlash(): void
    {
        self::start();
        $_SESSION['_flash'] = [];
    }
}