<?php

namespace Core\Auth;

use Core\Session\Session;

class Auth
{
    /**
     * Verifica se o usuário está autenticado
     *
     * @return bool
     */
    public static function check()
    {
        return Session::has('user');
    }

    /**
     * Retorna o usuário autenticado ou null
     *
     * @return mixed
     */
    public static function user()
    {
        return Session::get('user');
    }

    /**
     * Autentica um usuário
     *
     * @param mixed $user
     * @return void
     */
    public static function login($user)
    {
        Session::set('user', $user);
    }

    /**
     * Desautentica o usuário atual
     *
     * @return void
     */
    public static function logout()
    {
        Session::remove('user');
    }
}