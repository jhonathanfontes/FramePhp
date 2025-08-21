<?php

namespace App\Controllers\Backend\Painel;

use Core\Controller\BaseController;
use Core\Http\Request;
use Core\Http\Response;
use Core\Validation\Validator;
use App\Models\EstabelecimentoModel;
use App\Models\EmpresaModel;

class EstabelecimentoController extends BaseController
{
    private $estabelecimentoModel;
    private $empresaModel;

    public function __construct()
    {
        $this->estabelecimentoModel = new EstabelecimentoModel();
        $this->empresaModel = new EmpresaModel();
    }

    /**
     * API para listar estabelecimentos com paginação e filtros
     */
    public function index(Request $request): Response
    {
        try {
            $pagina = $request->get('pagina', 1);
            $porPagina = $request->get('por_pagina', 10);
            $filtros = $request->get('filtros', []);
            $ordenacao = $request->get('ordenacao', 'id');
            $direcao = $request->get('direcao', 'DESC');

            $estabelecimentos = $this->estabelecimentoModel->getPaginated($pagina, $porPagina, $filtros, $ordenacao, $direcao);
            $total = $this->estabelecimentoModel->countWithFilters($filtros);

            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'estabelecimentos' => $estabelecimentos,
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
                'message' => 'Erro ao carregar estabelecimentos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para obter estabelecimento específico
     */
    public function show($id): Response
    {
        try {
            $estabelecimento = $this->estabelecimentoModel->find($id);
            
            if (!$estabelecimento) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Estabelecimento não encontrado'
                ], 404);
            }

            // Carregar dados da empresa
            $empresa = $this->empresaModel->find($estabelecimento->empresa_id);

            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'estabelecimento' => $estabelecimento,
                    'empresa' => $empresa
                ]
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao carregar estabelecimento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para criar estabelecimento
     */
    public function store(Request $request): Response
    {
        try {
            $dados = $request->all();
            
            // Validação
            $validator = Validator::make($dados, [
                'empresa_id' => 'required|integer|exists:empresas,id',
                'cnpj' => 'required|size:14|unique:estabelecimentos,cnpj',
                'tipo' => 'required|in:matriz,filial',
                'nome_fantasia' => 'required|min:3|max:200',
                'situacao_cadastral' => 'required|in:ATIVA,BAIXADA,SUSPENSA',
                'data_abertura' => 'required|date',
                'telefone1' => 'max:20',
                'telefone2' => 'max:20',
                'email' => 'email|max:100',
                'logradouro' => 'max:100',
                'numero' => 'max:10',
                'complemento' => 'max:100',
                'bairro' => 'max:100',
                'municipio' => 'max:100',
                'uf' => 'size:2',
                'cep' => 'max:10'
            ]);

            if ($validator->fails()) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->getErrors()
                ], 422);
            }

            // Validar CNPJ
            if (!$this->validarCNPJ($dados['cnpj'])) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'CNPJ inválido'
                ], 422);
            }

            // Verificar se a empresa existe e está ativa
            $empresa = $this->empresaModel->find($dados['empresa_id']);
            if (!$empresa || $empresa->status !== 'ativo') {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Empresa não encontrada ou inativa'
                ], 422);
            }

            // Criar estabelecimento
            $estabelecimento = $this->estabelecimentoModel->create($dados);

            return $this->jsonResponse([
                'success' => true,
                'message' => 'Estabelecimento criado com sucesso',
                'data' => $estabelecimento
            ], 201);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao criar estabelecimento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para atualizar estabelecimento
     */
    public function update(Request $request, $id): Response
    {
        try {
            $estabelecimento = $this->estabelecimentoModel->find($id);
            
            if (!$estabelecimento) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Estabelecimento não encontrado'
                ], 404);
            }

            $dados = $request->all();
            
            // Validação
            $regras = [
                'tipo' => 'required|in:matriz,filial',
                'nome_fantasia' => 'required|min:3|max:200',
                'situacao_cadastral' => 'required|in:ATIVA,BAIXADA,SUSPENSA',
                'data_abertura' => 'required|date',
                'telefone1' => 'max:20',
                'telefone2' => 'max:20',
                'email' => 'email|max:100',
                'logradouro' => 'max:100',
                'numero' => 'max:10',
                'complemento' => 'max:100',
                'bairro' => 'max:100',
                'municipio' => 'max:100',
                'uf' => 'size:2',
                'cep' => 'max:10'
            ];

            // CNPJ é único, mas pode ser o mesmo do estabelecimento
            if (isset($dados['cnpj']) && $dados['cnpj'] !== $estabelecimento->cnpj) {
                $regras['cnpj'] = 'required|size:14|unique:estabelecimentos,cnpj,' . $id;
                
                if (!$this->validarCNPJ($dados['cnpj'])) {
                    return $this->jsonResponse([
                        'success' => false,
                        'message' => 'CNPJ inválido'
                    ], 422);
                }
            }

            $validator = Validator::make($dados, $regras);

            if ($validator->fails()) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->getErrors()
                ], 422);
            }

            // Atualizar estabelecimento
            $this->estabelecimentoModel->update($id, $dados);

            return $this->jsonResponse([
                'success' => true,
                'message' => 'Estabelecimento atualizado com sucesso'
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao atualizar estabelecimento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para excluir estabelecimento
     */
    public function destroy($id): Response
    {
        try {
            $estabelecimento = $this->estabelecimentoModel->find($id);
            
            if (!$estabelecimento) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Estabelecimento não encontrado'
                ], 404);
            }

            // Verificar se é matriz
            if ($estabelecimento->tipo === 'matriz') {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Não é possível excluir estabelecimento matriz'
                ], 422);
            }

            // Excluir estabelecimento
            $this->estabelecimentoModel->delete($id);

            return $this->jsonResponse([
                'success' => true,
                'message' => 'Estabelecimento excluído com sucesso'
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao excluir estabelecimento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para alterar status do estabelecimento
     */
    public function alterarStatus(Request $request, $id): Response
    {
        try {
            $estabelecimento = $this->estabelecimentoModel->find($id);
            
            if (!$estabelecimento) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Estabelecimento não encontrado'
                ], 404);
            }

            $novoStatus = $request->get('status');
            
            if (!in_array($novoStatus, ['ATIVA', 'BAIXADA', 'SUSPENSA'])) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Status inválido'
                ], 422);
            }

            $this->estabelecimentoModel->update($id, ['situacao_cadastral' => $novoStatus]);

            return $this->jsonResponse([
                'success' => true,
                'message' => 'Status do estabelecimento alterado com sucesso'
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao alterar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para obter estabelecimentos por empresa
     */
    public function getByEmpresa($empresaId): Response
    {
        try {
            $estabelecimentos = $this->estabelecimentoModel->getByEmpresa($empresaId);

            return $this->jsonResponse([
                'success' => true,
                'data' => $estabelecimentos
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao carregar estabelecimentos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para obter estatísticas de estabelecimentos
     */
    public function getEstatisticas(): Response
    {
        try {
            $estatisticas = [
                'total' => $this->estabelecimentoModel->count(),
                'ativos' => $this->estabelecimentoModel->countByStatus('ATIVA'),
                'baixados' => $this->estabelecimentoModel->countByStatus('BAIXADA'),
                'suspensos' => $this->estabelecimentoModel->countByStatus('SUSPENSA'),
                'matriz' => $this->estabelecimentoModel->countByTipo('matriz'),
                'filiais' => $this->estabelecimentoModel->countByTipo('filial'),
                'por_estado' => $this->estabelecimentoModel->getCountByEstado(),
                'por_empresa' => $this->estabelecimentoModel->getCountByEmpresa(),
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
     * API para buscar estabelecimentos
     */
    public function buscar(Request $request): Response
    {
        try {
            $termo = $request->get('termo', '');
            $tipo = $request->get('tipo', 'todos');
            $estado = $request->get('estado', '');
            $empresa_id = $request->get('empresa_id', '');

            if (empty($termo) && empty($tipo) && empty($estado) && empty($empresa_id)) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Pelo menos um critério de busca deve ser informado'
                ], 422);
            }

            $resultados = $this->estabelecimentoModel->buscar($termo, $tipo, $estado, $empresa_id);

            return $this->jsonResponse([
                'success' => true,
                'data' => $resultados
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro na busca: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Métodos privados auxiliares
     */
    private function validarCNPJ($cnpj): bool
    {
        // Remover caracteres não numéricos
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        
        // Verificar se tem 14 dígitos
        if (strlen($cnpj) != 14) {
            return false;
        }

        // Verificar se todos os dígitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        // Validar dígitos verificadores
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;
        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto)) {
            return false;
        }

        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;
        return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
    }

    private function getCrescimentoMensal()
    {
        $meses = [];
        $valores = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $data = date('Y-m', strtotime("-$i months"));
            $meses[] = date('M/Y', strtotime("-$i months"));
            $valores[] = $this->estabelecimentoModel->countByMonth($data);
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