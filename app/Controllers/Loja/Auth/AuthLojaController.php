<?php

namespace App\Controllers\Loja\Auth;

use Core\Controller\BaseController;
use App\Models\CadUsuarioModel;
use Core\Auth\Auth;
use Core\Http\Response;
use Core\Session\Session;
use Core\Validation\Validator; // Importe o Validator

class AuthLojaController extends BaseController
{
    // A dependência é declarada como uma propriedade da classe
    private CadUsuarioModel $userModel;

    /**
     * O contêiner de DI irá injetar automaticamente uma instância de CadUsuarioModel aqui.
     * Não há mais "new CadUsuarioModel()" dentro do controlador.
     */
    public function __construct(CadUsuarioModel $userModel)
    {
        $this->userModel = $userModel;
    }

    public function loginForm()
    {
        return $this->render('pages/auth/login');
    }

    /**
     * Método de login refatorado para usar a classe Validator.
     */
    public function login()
    {
        $data = $_POST;
        $validator = Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->render('pages/auth/login', [
                'errors' => $validator->getErrors(),
                'old' => $data
            ]);
        }

        $user = $this->userModel->findByEmail($data['email']);

        if ($user && password_verify($data['password'], $user->use_password)) {

            $userData = [
                'id' => $user->id_usuario,
                'name' => $user->use_nome,
                'username' => $user->use_username,
                'email' => $user->use_email,
                'role' => $user->permissao_id == 1 ? 'admin' : 'user',
                'avatar' => $user->use_avatar,
                'status' => $user->status,
                'type' => 'admin',
            ];

            Auth::login($userData);

            return Response::redirectResponse(base_url('admin/dashboard'));
        }

        return $this->render('pages/auth/login', [
            'error' => 'Credenciais inválidas.',
            'old' => $data
        ]);
    }

    public function forgotPasswordForm()
    {
        echo $this->render('pages/auth/forgot-password', [
            'title' => 'Recuperar Senha'
        ]);
    }

    public function forgotPassword()
    {
        $email = $_POST['email'] ?? '';

        if (empty($email)) {
            echo $this->render('pages/auth/forgot-password', [
                'title' => 'Recuperar Senha',
                'error' => 'O e-mail é obrigatório'
            ]);
            return;
        }

        // Verificar se o e-mail existe
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            echo $this->render('pages/auth/forgot-password', [
                'title' => 'Recuperar Senha',
                'error' => 'Não encontramos um usuário com este e-mail'
            ]);
            return;
        }

        try {
            // Gerar token de recuperação
            $token = bin2hex(random_bytes(32));
            $this->userModel->createPasswordReset($email, $token);

            // Enviar e-mail de recuperação
            // $emailService = new EmailService(TwigManager::getInstance());
            // $emailService->sendPasswordResetEmail($email, $user['name'], $token);

            echo $this->render('pages/auth/forgot-password', [
                'title' => 'Recuperar Senha',
                'success' => 'Enviamos um e-mail com instruções para recuperar sua senha.'
            ]);
        } catch (\Exception $e) {
            error_log("Erro no processo de recuperação de senha: " . $e->getMessage());
            echo $this->render('pages/auth/forgot-password', [
                'title' => 'Recuperar Senha',
                'error' => 'Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente.'
            ]);
        }
    }


    public function registerForm()
    {
        return $this->render('pages/auth/register');
    }

    /**
     * Método de registro refatorado para usar a classe Validator.
     */
    public function register()
    {
        $data = $_POST;
        $validator = Validator::make($data, [
            'name' => 'required|min:3',
            'email' => 'required|email', // Futuramente: |unique:cad_usuario,use_email
            'password' => 'required|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return $this->render('pages/auth/register', [
                'errors' => $validator->getErrors(),
                'old' => $data
            ]);
        }

        $user = $this->userModel->findByEmail($data['email']);

        if ($user) {
            return $this->render('pages/auth/register', [
                'error_message' => 'E-mail já cadastrado.',
                'old' => $data
            ]);
        }

        $userId = $this->userModel->create([
            'use_nome' => $data['name'],
            'use_email' => $data['email'],
            'use_password' => $data['password'],
            'use_username' => $data['email'],
            'status' => 1,
            'permissao_id' => 2 // Padrão para novo usuário
        ]);

        if ($userId) {

            $user = $this->userModel->find($userId);

            $userData = [
                'id' => $user['id_usuario'],
                'name' => $user['use_nome'],
                'username' => $user['use_username'],
                'email' => $user['use_email'],
                'role' => $user['permissao_id'] == 1 ? 'admin' : 'user',
                'avatar' => $user['use_avatar'],
                'status' => $user['status'],
                'type' => 'admin',
            ];

            Auth::login($userData);
        }

        return Response::redirectResponse(base_url('admin/dashboard'));
    }

    public function logout()
    {
        Session::destroy();
        return Response::redirectResponse(base_url('auth/login'));
    }
}