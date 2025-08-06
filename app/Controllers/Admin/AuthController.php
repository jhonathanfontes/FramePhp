<?php

namespace App\Controllers\Admin;

use Core\Controller\BaseController;
use Core\Http\Response;
use Core\Session\Session;

class AuthController extends BaseController
{

    public function loginForm()
    {
        return $this->render('pages/auth/login');
    }

    public function forgotPasswordForm()
    {
        echo $this->render('pages/auth/forgot-password', [
            'title' => 'Recuperar Senha'
        ]);
    }

    public function registerForm()
    {
        return $this->render('pages/auth/register');
    }

    public function logout()
    {
        Session::destroy();
        return Response::redirectResponse(base_url('auth/login'));
    }
}