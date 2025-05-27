<?php

namespace App\Controllers\Auth;

use Core\Controller\BaseController;
use App\Models\CadUsuarioModel;
use App\Services\EmailService;
use Core\Auth\Auth;
use Core\Http\Response;
use Core\View\TwigManager;

class AuthController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new CadUsuarioModel();
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
            if ($user && password_verify($password, $user['use_password'])) {
                // Log para debug
                error_log("Senha verificada com sucesso para o usuário: " . $user['use_email']);

                // Preparar dados do usuário para a sessão
                $userData = [
                    'id' => $user['id_usuario'],
                    'name' => $user['use_nome'],
                    'username' => $user['use_username'],
                    'email' => $user['use_email'],
                    'role' => $user['permissao_id'],
                    'avatar' => $user['use_avatar'],
                    'status' => $user['status'],
                    'type' => 'admin',
                ];

                // Autenticar o usuário usando a classe Auth
                // Após autenticar o usuário
                Auth::login($userData);
                
                // Log para debug - verificar se a sessão foi criada corretamente
                error_log("Sessão após login: " . json_encode($_SESSION));
                error_log("Auth::check() após login: " . (Auth::check() ? "true" : "false"));
                
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
        $nome = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';
        $username = $_POST['username'] ?? $email;

        // Validações básicas
        $errors = [];
        $oldInput = [
            'name' => $nome,
            'email' => $email,
            'username' => $username
        ];

        if (empty($nome)) {
            $errors['name'] = 'O nome é obrigatório';
        }
        if (empty($email)) {
            $errors['email'] = 'O e-mail é obrigatório';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'E-mail inválido';
        } elseif ($this->userModel->findByEmail($email)) {
            $errors['email'] = 'Este e-mail já está em uso';
        }
        if (empty($password)) {
            $errors['password'] = 'A senha é obrigatória';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'A senha deve ter no mínimo 6 caracteres';
        }
        if ($password !== $passwordConfirmation) {
            $errors['password_confirmation'] = 'As senhas não conferem';
        }
          
        // Se houver erros, exibir o formulário novamente
        if (!empty($errors)) {
            return $this->render('auth/register', [
                'title' => 'Cadastro',
                'errors' => $errors,
                'old' => $oldInput,
                'error_message' => 'Por favor, corrija os erros abaixo:'
            ]);
        }

        // Criar usuário
        $userData = [
            'use_nome' => $nome,
            'use_apelido' => $nome,
            'use_email' => $email,
            'use_password' => $password,
            'use_username' => $username,
            'status' => 1,
            'permissao_id' => 1
        ];

        try {
            
            $userId = $this->userModel->create($userData);
             // Remova estas duas linhas:
       
            if ($userId) {
                // Autenticar o usuário após o registro
                $user = $this->userModel->findById($userId);
                Auth::login([
                    'id_usuario' => $user['id_usuario'],
                    'use_nome' => $user['use_nome'],
                    'use_username' => $user['use_username'],
                    'use_email' => $user['use_email'],
                    'permissao_id' => $user['permissao_id'],
                    'use_avatar' => $user['use_avatar'] ?? null,
                    'status' => $user['status'],
                    'use_telefone' => $user['use_telefone'] ?? null,
                    'use_sexo' => $user['use_sexo'] ?? null,
                    'type' => 'web'
                ]);
                
                header('Location: ' . base_url('dashboard'));
                exit;
            }
        } catch (\Exception $e) {
            error_log("Erro ao criar usuário: " . $e->getMessage());
            $errors['general'] = 'Erro ao criar usuário. Por favor, tente novamente.';
            
            echo $this->render('auth/register', [
                'title' => 'Cadastro',
                'errors' => $errors,
                'name' => $nome,
                'email' => $email
            ]);
        }
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

        try {
            // Gerar token de recuperação
            $token = bin2hex(random_bytes(32));
            $this->userModel->createPasswordReset($email, $token);

            // Enviar e-mail de recuperação
            $emailService = new EmailService(TwigManager::getInstance());
            $emailService->sendPasswordResetEmail($email, $user['name'], $token);

            echo $this->render('auth/forgot-password', [
                'title' => 'Recuperar Senha',
                'success' => 'Enviamos um e-mail com instruções para recuperar sua senha.'
            ]);
        } catch (\Exception $e) {
            error_log("Erro no processo de recuperação de senha: " . $e->getMessage());
            echo $this->render('auth/forgot-password', [
                'title' => 'Recuperar Senha',
                'error' => 'Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente.'
            ]);
        }
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
        // Usar o método Auth::logout() em vez de session_destroy() diretamente
        Auth::logout();
        
        // Redirecionar para a página de login
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