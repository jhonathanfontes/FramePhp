<?php

namespace Core\Cookie;

class Cookie
{
    /**
     * Set a cookie
     *
     * @param string $name
     * @param string $value
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     * @return void
     */
    public static function set(string $name, string $value, int $expire = 0, string $path = "", string $domain = "", bool $secure = false, bool $httponly = false): void
    {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }

    /**
     * Get a cookie
     *
     * @param string $name
     * @return string|null
     */
    public static function get(string $name): ?string
    {
        return $_COOKIE[$name] ?? null;
    }

    /**
     * Delete a cookie
     *
     * @param string $name
     * @param string $path
     * @param string $domain
     * @return void
     */
    public static function delete(string $name, string $path = "", string $domain = ""): void
    {
        setcookie($name, "", time() - 3600, $path, $domain);
    }
}