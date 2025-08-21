<?php

namespace App\Controllers\Backend\Painel;

use App\Lib\TableBuilder;
use App\Models\CadUsuarioModel;
use Core\Controller\BaseController;
use Core\Http\Request;
use Core\Http\Response;
use Core\Validation\Validator;

class UsuarioController extends BaseController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new CadUsuarioModel();
    }

    /**
     * API para listar usuários com paginação e filtros
     */
    public function index(Request $request): Response
    {
        try {
            $pagina = $request->get('pagina', 1);
            $porPagina = $request->get('por_pagina', 10);
            $filtros = $request->get('filtros', []);
            $ordenacao = $request->get('ordenacao', 'id_usuario');
            $direcao = $request->get('direcao', 'DESC');

            $usuarios = $this->usuarioModel->getPaginated($pagina, $porPagina, $filtros, $ordenacao, $direcao);
            $total = $this->usuarioModel->countWithFilters($filtros);

            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'usuarios' => $usuarios,
                    'paginacao' => [
                        'pagina_atual' => $pagina,
                        'por_pagina' => $porPagina,
                        'total' => $total,
                        'total_paginas' => ceil($total / $porPagina)
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao carregar usuários: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para obter usuário específico
     */
    public function show($id): Response
    {
        try {
            $usuario = $this->usuarioModel->find($id);
            
            if (!$usuario) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Usuário não encontrado'
                ], 404);
            }

            return $this->jsonResponse([
                'success' => true,
                'data' => $usuario
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao carregar usuário: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para criar usuário
     */
    public function store(Request $request): Response
    {
        try {
            $dados = $request->all();
            
            // Validação
            $validator = Validator::make($dados, [
                'use_nome' => 'required|min:3|max:100',
                'use_email' => 'required|email|unique:usuarios,use_email',
                'use_username' => 'required|min:3|max:50|unique:usuarios,use_username',
                'use_password' => 'required|min:8',
                'permissao_id' => 'required|integer',
                'empresa_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->getErrors()
                ], 422);
            }

            // Hash da senha
            $dados['use_password'] = password_hash($dados['use_password'], PASSWORD_DEFAULT);
            
            // Criar usuário
            $usuario = $this->usuarioModel->create($dados);

            return $this->jsonResponse([
                'success' => true,
                'message' => 'Usuário criado com sucesso',
                'data' => $usuario
            ], 201);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao criar usuário: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para atualizar usuário
     */
    public function update(Request $request, $id): Response
    {
        try {
            $usuario = $this->usuarioModel->find($id);
            
            if (!$usuario) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Usuário não encontrado'
                ], 404);
            }

            $dados = $request->all();
            
            // Validação
            $regras = [
                'use_nome' => 'required|min:3|max:100',
                'use_email' => 'required|email|unique:usuarios,use_email,' . $id,
                'use_username' => 'required|min:3|max:50|unique:usuarios,use_username,' . $id,
                'permissao_id' => 'required|integer',
                'empresa_id' => 'required|integer'
            ];

            // Senha é opcional na atualização
            if (!empty($dados['use_password'])) {
                $regras['use_password'] = 'min:8';
                $dados['use_password'] = password_hash($dados['use_password'], PASSWORD_DEFAULT);
            } else {
                unset($dados['use_password']);
            }

            $validator = Validator::make($dados, $regras);

            if ($validator->fails()) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->getErrors()
                ], 422);
            }

            // Atualizar usuário
            $this->usuarioModel->update($id, $dados);

            return $this->jsonResponse([
                'success' => true,
                'message' => 'Usuário atualizado com sucesso'
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao atualizar usuário: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para excluir usuário
     */
    public function destroy($id): Response
    {
        try {
            $usuario = $this->usuarioModel->find($id);
            
            if (!$usuario) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Usuário não encontrado'
                ], 404);
            }

            // Verificar se não é o último admin
            if ($usuario->permissao_id == 1) {
                $totalAdmins = $this->usuarioModel->countByPermissao(1);
                if ($totalAdmins <= 1) {
                    return $this->jsonResponse([
                        'success' => false,
                        'message' => 'Não é possível excluir o último administrador'
                    ], 422);
                }
            }

            // Excluir usuário
            $this->usuarioModel->delete($id);

            return $this->jsonResponse([
                'success' => true,
                'message' => 'Usuário excluído com sucesso'
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao excluir usuário: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para alterar status do usuário
     */
    public function alterarStatus(Request $request, $id): Response
    {
        try {
            $usuario = $this->usuarioModel->find($id);
            
            if (!$usuario) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Usuário não encontrado'
                ], 404);
            }

            $novoStatus = $request->get('status');
            
            if (!in_array($novoStatus, ['ativo', 'inativo', 'bloqueado'])) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Status inválido'
                ], 422);
            }

            // Verificar se não está bloqueando o último admin
            if ($novoStatus !== 'ativo' && $usuario->permissao_id == 1) {
                $totalAdminsAtivos = $this->usuarioModel->countByPermissao(1, 'ativo');
                if ($totalAdminsAtivos <= 1) {
                    return $this->jsonResponse([
                        'success' => false,
                        'message' => 'Não é possível bloquear o último administrador ativo'
                    ], 422);
                }
            }

            $this->usuarioModel->update($id, ['status' => $novoStatus]);

            return $this->jsonResponse([
                'success' => true,
                'message' => 'Status do usuário alterado com sucesso'
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao alterar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para redefinir senha do usuário
     */
    public function redefinirSenha(Request $request, $id): Response
    {
        try {
            $usuario = $this->usuarioModel->find($id);
            
            if (!$usuario) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Usuário não encontrado'
                ], 404);
            }

            $novaSenha = $request->get('nova_senha');
            
            if (strlen($novaSenha) < 8) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'A nova senha deve ter pelo menos 8 caracteres'
                ], 422);
            }

            $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $this->usuarioModel->update($id, ['use_password' => $senhaHash]);

            return $this->jsonResponse([
                'success' => true,
                'message' => 'Senha redefinida com sucesso'
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao redefinir senha: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para obter estatísticas de usuários
     */
    public function getEstatisticas(): Response
    {
        try {
            $estatisticas = [
                'total' => $this->usuarioModel->count(),
                'ativos' => $this->usuarioModel->countByStatus('ativo'),
                'inativos' => $this->usuarioModel->countByStatus('inativo'),
                'bloqueados' => $this->usuarioModel->countByStatus('bloqueado'),
                'por_permissao' => $this->usuarioModel->getCountByPermissao(),
                'por_empresa' => $this->usuarioModel->getCountByEmpresa(),
                'crescimento_mensal' => $this->getCrescimentoMensal()
            ];

            return $this->jsonResponse([
                'success' => true,
                'data' => $estatisticas
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao carregar estatísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método privado para calcular crescimento mensal
     */
    private function getCrescimentoMensal()
    {
        $meses = [];
        $valores = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $data = date('Y-m', strtotime("-$i months"));
            $meses[] = date('M/Y', strtotime("-$i months"));
            $valores[] = $this->usuarioModel->countByMonth($data);
        }

        return [
            'labels' => $meses,
            'data' => $valores
        ];
    }

    private function jsonResponse($dados, $statusCode = 200): Response
    {
        return new Response(json_encode($dados), $statusCode, [
            'Content-Type' => 'application/json'
        ]);
    }
}