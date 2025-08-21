<?php

namespace App\Controllers\Painel;

use Core\Controller\BaseController;
use Core\Http\Request;
use Core\Http\Response;
use Core\Auth\Auth;
use App\Models\Usuario;

class PerfilController extends BaseController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    public function index()
    {
        $usuario = Auth::user();
        
        if (!$usuario) {
            return $this->redirect('/painel/auth/login')->with('error', 'Usuário não autenticado');
        }

        // Carregar dados completos do usuário
        $usuarioCompleto = $this->usuarioModel->find($usuario->id);
        
        // Carregar estatísticas do usuário
        $estatisticas = [
            'total_empresas' => $this->usuarioModel->getTotalEmpresas($usuario->id),
            'total_estabelecimentos' => $this->usuarioModel->getTotalEstabelecimentos($usuario->id)
        ];

        // Carregar preferências do usuário
        $preferencias = $this->carregarPreferencias($usuario->id);

        return $this->render('painel/perfil', [
            'active_menu' => 'perfil',
            'usuario' => $usuarioCompleto,
            'estatisticas' => $estatisticas,
            'preferencias' => $preferencias
        ]);
    }

    public function atualizar(Request $request): Response
    {
        $usuario = Auth::user();
        
        if (!$usuario) {
            return $this->redirect('/painel/auth/login')->with('error', 'Usuário não autenticado');
        }

        if ($request->isPost()) {
            $dados = [
                'nome' => $request->get('nome'),
                'email' => $request->get('email'),
                'telefone' => $request->get('telefone'),
                'cpf' => $request->get('cpf'),
                'data_nascimento' => $request->get('data_nascimento'),
                'genero' => $request->get('genero'),
                'endereco' => $request->get('endereco'),
                'cep' => $request->get('cep'),
                'cidade' => $request->get('cidade'),
                'estado' => $request->get('estado'),
                'biografia' => $request->get('biografia')
            ];

            // Validar e-mail único
            if ($dados['email'] !== $usuario->email) {
                $emailExistente = $this->usuarioModel->where('email', $dados['email'])
                    ->where('id', '!=', $usuario->id)
                    ->first();
                
                if ($emailExistente) {
                    return $this->redirect('/painel/perfil')->with('error', 'Este e-mail já está em uso');
                }
            }

            // Atualizar usuário
            if ($this->usuarioModel->update($usuario->id, $dados)) {
                return $this->redirect('/painel/perfil')->with('success', 'Perfil atualizado com sucesso!');
            }

            return $this->redirect('/painel/perfil')->with('error', 'Erro ao atualizar perfil');
        }

        return $this->redirect('/painel/perfil');
    }

    public function alterarSenha(Request $request): Response
    {
        $usuario = Auth::user();
        
        if (!$usuario) {
            return $this->redirect('/painel/auth/login')->with('error', 'Usuário não autenticado');
        }

        if ($request->isPost()) {
            $senhaAtual = $request->get('senha_atual');
            $novaSenha = $request->get('nova_senha');
            $confirmarSenha = $request->get('confirmar_senha');

            // Verificar senha atual
            if (!$usuario->verificarSenha($senhaAtual)) {
                return $this->redirect('/painel/perfil')->with('error', 'Senha atual incorreta');
            }

            // Verificar se as senhas coincidem
            if ($novaSenha !== $confirmarSenha) {
                return $this->redirect('/painel/perfil')->with('error', 'As senhas não coincidem');
            }

            // Validar força da senha
            if (!$this->validarForcaSenha($novaSenha)) {
                return $this->redirect('/painel/perfil')->with('error', 'A nova senha não atende aos requisitos de segurança');
            }

            // Atualizar senha
            $dados = ['senha' => password_hash($novaSenha, PASSWORD_DEFAULT)];
            
            if ($this->usuarioModel->update($usuario->id, $dados)) {
                return $this->redirect('/painel/perfil')->with('success', 'Senha alterada com sucesso!');
            }

            return $this->redirect('/painel/perfil')->with('error', 'Erro ao alterar senha');
        }

        return $this->redirect('/painel/perfil');
    }

    public function preferencias(Request $request): Response
    {
        $usuario = Auth::user();
        
        if (!$usuario) {
            return $this->redirect('/painel/auth/login')->with('error', 'Usuário não autenticado');
        }

        if ($request->isPost()) {
            $dados = [
                'idioma' => $request->get('idioma'),
                'timezone' => $request->get('timezone'),
                'email_notificacoes' => $request->has('email_notificacoes'),
                'sms_notificacoes' => $request->has('sms_notificacoes'),
                'push_notificacoes' => $request->has('push_notificacoes')
            ];

            if ($this->salvarPreferencias($usuario->id, $dados)) {
                return $this->redirect('/painel/perfil')->with('success', 'Preferências salvas com sucesso!');
            }

            return $this->redirect('/painel/perfil')->with('error', 'Erro ao salvar preferências');
        }

        return $this->redirect('/painel/perfil');
    }

    public function uploadFoto(Request $request): Response
    {
        $usuario = Auth::user();
        
        if (!$usuario) {
            return $this->jsonResponse(['success' => false, 'message' => 'Usuário não autenticado']);
        }

        if ($request->isPost() && $request->hasFile('foto')) {
            $arquivo = $request->getFile('foto');
            
            // Validar tipo de arquivo
            $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($arquivo->getMimeType(), $tiposPermitidos)) {
                return $this->jsonResponse(['success' => false, 'message' => 'Tipo de arquivo não permitido']);
            }

            // Validar tamanho (máximo 5MB)
            if ($arquivo->getSize() > 5 * 1024 * 1024) {
                return $this->jsonResponse(['success' => false, 'message' => 'Arquivo muito grande (máximo 5MB)']);
            }

            // Gerar nome único para o arquivo
            $extensao = pathinfo($arquivo->getClientOriginalName(), PATHINFO_EXTENSION);
            $nomeArquivo = 'foto_' . $usuario->id . '_' . time() . '.' . $extensao;
            
            // Diretório de destino
            $diretorio = 'uploads/usuarios/';
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0755, true);
            }

            // Mover arquivo
            if ($arquivo->move($diretorio, $nomeArquivo)) {
                // Atualizar banco de dados
                $caminhoFoto = $diretorio . $nomeArquivo;
                
                if ($this->usuarioModel->update($usuario->id, ['foto' => $caminhoFoto])) {
                    return $this->jsonResponse(['success' => true, 'message' => 'Foto atualizada com sucesso']);
                }
            }

            return $this->jsonResponse(['success' => false, 'message' => 'Erro ao fazer upload da foto']);
        }

        return $this->jsonResponse(['success' => false, 'message' => 'Nenhum arquivo enviado']);
    }

    private function validarForcaSenha($senha): bool
    {
        // Mínimo 8 caracteres
        if (strlen($senha) < 8) {
            return false;
        }

        // Pelo menos 1 letra maiúscula
        if (!preg_match('/[A-Z]/', $senha)) {
            return false;
        }

        // Pelo menos 1 letra minúscula
        if (!preg_match('/[a-z]/', $senha)) {
            return false;
        }

        // Pelo menos 1 número
        if (!preg_match('/\d/', $senha)) {
            return false;
        }

        // Pelo menos 1 caractere especial
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $senha)) {
            return false;
        }

        return true;
    }

    private function carregarPreferencias($usuarioId)
    {
        // Placeholder - implementar carregamento de preferências do banco
        return [
            'idioma' => 'pt_BR',
            'timezone' => 'America/Sao_Paulo',
            'email_notificacoes' => true,
            'sms_notificacoes' => false,
            'push_notificacoes' => true
        ];
    }

    private function salvarPreferencias($usuarioId, $dados): bool
    {
        try {
            // Placeholder - implementar salvamento de preferências no banco
            return true;
        } catch (\Exception $e) {
            error_log("Erro ao salvar preferências: " . $e->getMessage());
            return false;
        }
    }

    private function jsonResponse($dados): Response
    {
        return new Response(json_encode($dados), 200, [
            'Content-Type' => 'application/json'
        ]);
    }
} 