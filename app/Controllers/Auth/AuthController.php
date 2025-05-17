<?php

namespace App\Controllers\Auth;

use Core\Controller\BaseController;
use App\Models\User;
use Core\Auth\Auth;
use Core\Http\Response;

class AuthController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function loginForm()
    { 
        echo $this->render('auth/login', [
            'title' => 'Login'
        ]);
    }

    public function login()
    {
        try {
            // Validar credenciais
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Log para debug
            error_log("Tentativa de login - Email: " . $email);

            if (empty($email) || empty($password)) {
                error_log("Email ou senha vazios");
                echo $this->render('auth/login', [
                    'title' => 'Login',
                    'error' => 'Email e senha são obrigatórios',
                    'email' => $email
                ]);
                return;
            }

            // Buscar usuário pelo e-mail
            $user = $this->userModel->findByEmail($email);

            // Log para debug
            error_log("Usuário encontrado: " . ($user ? "Sim" : "Não"));
            if ($user) {
                error_log("Dados do usuário: " . json_encode($user));
            }

            // Verificar se o usuário existe e a senha está correta
            if ($user && password_verify($password, $user['password'])) {
                // Log para debug
                error_log("Senha verificada com sucesso para o usuário: " . $user['email']);

                // Preparar dados do usuário para a sessão
                $userData = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ];

                // Autenticar o usuário usando a classe Auth
                Auth::login($userData);

                // Log para debug
                error_log("Usuário autenticado: " . json_encode($userData));
                
                // Redirecionar para o dashboard
                header('Location: ' . base_url('dashboard'));
                exit;
            }
            
            // Log para debug
            error_log("Falha no login - Email: " . $email);
            if ($user) {
                error_log("Senha incorreta para o usuário");
            } else {
                error_log("Usuário não encontrado");
            }
            
            // Credenciais inválidas
            echo $this->render('auth/login', [
                'title' => 'Login',
                'error' => 'Credenciais inválidas',
                'email' => $email
            ]);
        } catch (\Exception $e) {
            error_log("Erro durante o login: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            echo $this->render('auth/login', [
                'title' => 'Login',
                'error' => 'Ocorreu um erro durante o login. Por favor, tente novamente.',
                'email' => $email ?? ''
            ]);
        }
    }

    public function registerForm()
    {
        echo $this->render('auth/register', [
            'title' => 'Cadastro'
        ]);
    }

    public function register()
    {
        // Validar dados do formulário
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';

        // Validações básicas
        $errors = [];
        if (empty($name)) {
            $errors['name'] = 'O nome é obrigatório';
        }
        if (empty($email)) {
            $errors['email'] = 'O e-mail é obrigatório';
        } elseif ($this->userModel->findByEmail($email)) {
            $errors['email'] = 'Este e-mail já está em uso';
        }
        if (empty($password)) {
            $errors['password'] = 'A senha é obrigatória';
        }
        if ($password !== $passwordConfirmation) {
            $errors['password_confirmation'] = 'As senhas não conferem';
        }

        if (!empty($errors)) {
            echo $this->render('auth/register', [
                'title' => 'Cadastro',
                'errors' => $errors,
                'name' => $name,
                'email' => $email
            ]);
            return;
        }

        // Criar o usuário
        $userId = $this->userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => 'user'
        ]);

        // Redirecionar para o login com mensagem de sucesso
        echo $this->render('auth/login', [
            'title' => 'Login',
            'success' => 'Cadastro realizado com sucesso! Faça login para continuar.',
            'email' => $email
        ]);
    }

    public function forgotPasswordForm()
    {
        echo $this->render('auth/forgot-password', [
            'title' => 'Recuperar Senha'
        ]);
    }

    public function forgotPassword()
    {
        $email = $_POST['email'] ?? '';

        if (empty($email)) {
            echo $this->render('auth/forgot-password', [
                'title' => 'Recuperar Senha',
                'error' => 'O e-mail é obrigatório'
            ]);
            return;
        }

        // Verificar se o e-mail existe
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            echo $this->render('auth/forgot-password', [
                'title' => 'Recuperar Senha',
                'error' => 'Não encontramos um usuário com este e-mail'
            ]);
            return;
        }

        // Gerar token de recuperação
        $token = bin2hex(random_bytes(32));
        $this->userModel->createPasswordReset($email, $token);

        // Em produção, enviar e-mail com o link de recuperação
        // $resetLink = base_url("reset-password/{$token}");
        // mail($email, 'Recuperação de Senha', "Clique no link para redefinir sua senha: {$resetLink}");

        echo $this->render('auth/forgot-password', [
            'title' => 'Recuperar Senha',
            'success' => 'Enviamos um e-mail com instruções para recuperar sua senha.'
        ]);
    }

    public function resetPasswordForm($token)
    {
        // Verificar se o token é válido
        $reset = $this->userModel->findPasswordReset($token);
        if (!$reset) {
            header('Location: ' . base_url('forgot-password'));
            exit;
        }

        echo $this->render('auth/reset-password', [
            'title' => 'Redefinir Senha',
            'token' => $token
        ]);
    }

    public function resetPassword()
    {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';

        // Verificar se o token é válido
        $reset = $this->userModel->findPasswordReset($token);
        if (!$reset) {
            header('Location: ' . base_url('forgot-password'));
            exit;
        }

        // Validações básicas
        $errors = [];
        if (empty($password)) {
            $errors['password'] = 'A senha é obrigatória';
        }
        if ($password !== $passwordConfirmation) {
            $errors['password_confirmation'] = 'As senhas não conferem';
        }

        if (!empty($errors)) {
            echo $this->render('auth/reset-password', [
                'title' => 'Redefinir Senha',
                'token' => $token,
                'errors' => $errors
            ]);
            return;
        }

        // Atualizar a senha do usuário
        $user = $this->userModel->findByEmail($reset['email']);
        if ($user) {
            $this->userModel->update($user['id'], ['password' => $password]);
            $this->userModel->deletePasswordReset($reset['email']);
        }

        echo $this->render('auth/login', [
            'title' => 'Login',
            'success' => 'Senha redefinida com sucesso! Faça login para continuar.'
        ]);
    }

    public function logout()
    {
        session_start();
        session_destroy();
        header('Location: ' . base_url('auth/login'));
        exit;
    }

    public function unauthorized()
    {
        echo $this->render('auth/unauthorized', [
            'title' => 'Acesso Não Autorizado'
        ]);
    }

    public function showLoginForm()
    {
        // Verifica se o usuário já está autenticado
        if (Auth::check()) {
            // Redireciona para o dashboard se já estiver logado
            return Response::redirectResponse('/dashboard');
        }
        
        // Se não estiver autenticado, exibe o formulário de login normalmente
        return $this->render('auth/login');
    }
}