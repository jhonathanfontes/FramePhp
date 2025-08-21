<?php

namespace App\Controllers\Painel\Auth;

use App\Models\Usuario;
use App\Models\Empresa;
use Core\Controller\BaseController;
use Core\Http\Request;
use Core\Http\Response;
use Core\Session\Session;
use Core\Auth\Auth;

class AuthController extends BaseController
{
    public function loginLoja(Request $request): Response
    {
        if ($request->isPost()) {
            $email = $request->get('email');
            $senha = $request->get('senha');
            $empresaId = $request->get('empresa_id', 1);

            $usuario = Usuario::where('email', $email)
                ->where('empresa_id', $empresaId)
                ->where('status', 'ativo')
                ->first();

            if ($usuario && $usuario->verificarSenha($senha)) {
                Auth::login($usuario, 'loja');
                $usuario->atualizarUltimoAcesso();
                
                return $this->redirect('/loja/perfil')
                    ->with('success', 'Login realizado com sucesso!');
            }

            return $this->redirect('/loja/login')
                ->with('error', 'Email ou senha inválidos');
        }

        return $this->view('loja.auth.login');
    }

    public function loginAdmin(Request $request): Response
    {
        if ($request->isPost()) {
            $email = $request->get('email');
            $senha = $request->get('senha');

            $usuario = Usuario::where('email', $email)
                ->where('tipo', 'admin_empresa')
                ->where('status', 'ativo')
                ->with('empresa')
                ->first();

            if ($usuario && $usuario->verificarSenha($senha) && $usuario->empresa->ativo) {
                Auth::login($usuario, 'admin');
                $usuario->atualizarUltimoAcesso();
                
                return $this->redirect('/admin/dashboard')
                    ->with('success', 'Login realizado com sucesso!');
            }

            return $this->redirect('/admin/login')
                ->with('error', 'Email ou senha inválidos');
        }

        return $this->view('admin.auth.login');
    }

    public function loginPainel(Request $request): Response
    {
        if ($request->isPost()) {
            $email = $request->get('email');
            $senha = $request->get('senha');

            $usuario = Usuario::where('email', $email)
                ->where('tipo', 'admin_geral')
                ->where('status', 'ativo')
                ->first();

            if ($usuario && $usuario->verificarSenha($senha)) {
                Auth::login($usuario, 'painel');
                $usuario->atualizarUltimoAcesso();
                
                return $this->redirect('/painel/dashboard')
                    ->with('success', 'Login realizado com sucesso!');
            }

            return $this->redirect('/painel/login')
                ->with('error', 'Email ou senha inválidos');
        }

        return $this->view('painel.auth.login');
    }

    public function logout()
    {
        Session::destroy();
        return Response::redirectResponse(base_url('auth/login'));
    }

    public function perfil(Request $request): Response
    {
        $usuario = Auth::user();
        
        if ($request->isPost()) {
            $dados = $request->all();
            
            $this->validate($dados, [
                'nome' => 'required|max:100',
                'email' => "required|email|unique:usuarios,email,{$usuario->id}"
            ]);

            if ($dados['senha'] && $dados['senha'] !== '') {
                $this->validate($dados, [
                    'senha' => 'min:6|confirmed'
                ]);
                $usuario->setSenha($dados['senha']);
            }

            $usuario->update([
                'nome' => $dados['nome'],
                'email' => $dados['email']
            ]);

            return $this->redirect()->back()
                ->with('success', 'Perfil atualizado com sucesso!');
        }

        return $this->view('auth.perfil', ['usuario' => $usuario]);
    }

    public function esqueciSenha(Request $request): Response
    {
        if ($request->isPost()) {
            $email = $request->get('email');
            $tipo = $request->get('tipo', 'loja');

            $usuario = Usuario::where('email', $email)->first();

            if ($usuario) {
                // Gerar token de reset
                $token = bin2hex(random_bytes(32));
                $usuario->reset_token = $token;
                $usuario->reset_token_expires = now()->addHours(24);
                $usuario->save();

                // Enviar email (implementar)
                // Mail::send('emails.reset-password', ['usuario' => $usuario, 'token' => $token]);

                return $this->redirect()->back()
                    ->with('success', 'Email de recuperação enviado!');
            }

            return $this->redirect()->back()
                ->with('error', 'Email não encontrado');
        }

        return $this->view('auth.esqueci-senha');
    }

    public function resetSenha(Request $request, $token): Response
    {
        $usuario = Usuario::where('reset_token', $token)
            ->where('reset_token_expires', '>', now())
            ->first();

        if (!$usuario) {
            return $this->redirect('/login')
                ->with('error', 'Token inválido ou expirado');
        }

        if ($request->isPost()) {
            $senha = $request->get('senha');
            
            $this->validate(['senha' => $senha], [
                'senha' => 'required|min:6|confirmed'
            ]);

            $usuario->setSenha($senha);
            $usuario->reset_token = null;
            $usuario->reset_token_expires = null;
            $usuario->save();

            return $this->redirect('/login')
                ->with('success', 'Senha alterada com sucesso!');
        }

        return $this->view('auth.reset-senha', ['token' => $token]);
    }

    // Métodos específicos para o painel administrativo
    public function loginPainelForm()
    {
        return $this->view('painel/login');
    }

    public function esqueciSenhaPainel(Request $request): Response
    {
        if ($request->isPost()) {
            $email = $request->get('email');

            $usuario = Usuario::where('email', $email)
                ->where('tipo', 'admin_geral')
                ->where('status', 'ativo')
                ->first();

            if ($usuario) {
                // Gerar token de reset
                $token = bin2hex(random_bytes(32));
                $usuario->reset_token = $token;
                $usuario->reset_token_expires = now()->addHours(1); // 1 hora para painel
                $usuario->save();

                // Enviar email (implementar)
                // Mail::send('emails.reset-password-painel', ['usuario' => $usuario, 'token' => $token]);

                return $this->redirect('/painel/auth/forgot-password')
                    ->with('success', 'Email de recuperação enviado! Verifique sua caixa de entrada.');
            }

            return $this->redirect('/painel/auth/forgot-password')
                ->with('error', 'Email não encontrado ou usuário sem permissão para acessar o painel');
        }

        return $this->view('painel/esqueci_senha');
    }

    public function resetSenhaPainel(Request $request, $token): Response
    {
        $usuario = Usuario::where('reset_token', $token)
            ->where('reset_token_expires', '>', now())
            ->where('tipo', 'admin_geral')
            ->where('status', 'ativo')
            ->first();

        if (!$usuario) {
            return $this->redirect('/painel/auth/login')
                ->with('error', 'Token inválido, expirado ou usuário sem permissão');
        }

        if ($request->isPost()) {
            $senha = $request->get('password');
            $confirmarSenha = $request->get('password_confirmation');
            
            // Validar senha
            if ($senha !== $confirmarSenha) {
                return $this->redirect()->back()
                    ->with('error', 'As senhas não coincidem');
            }

            if (strlen($senha) < 8) {
                return $this->redirect()->back()
                    ->with('error', 'A senha deve ter pelo menos 8 caracteres');
            }

            // Atualizar senha
            $usuario->senha = password_hash($senha, PASSWORD_DEFAULT);
            $usuario->reset_token = null;
            $usuario->reset_token_expires = null;
            $usuario->save();

            return $this->redirect('/painel/auth/login')
                ->with('success', 'Senha alterada com sucesso! Faça login com sua nova senha.');
        }

        return $this->view('painel/redefinir_senha', [
            'token' => $token,
            'email' => $usuario->email
        ]);
    }

    public function logoutPainel()
    {
        Session::destroy();
        return $this->redirect('/painel/auth/login')
            ->with('success', 'Logout realizado com sucesso!');
    }
}