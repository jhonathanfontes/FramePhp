<?php

namespace App\Controllers\Backend\Painel;

use Core\Controller\BaseController;
use Core\Http\Request;
use Core\Http\Response;
use Core\Validation\Validator;
use App\Models\EmpresaModel;
use App\Models\EstabelecimentoModel;

class EmpresaController extends BaseController
{
    private $empresaModel;
    private $estabelecimentoModel;

    public function __construct()
    {
        $this->empresaModel = new EmpresaModel();
        $this->estabelecimentoModel = new EstabelecimentoModel();
    }

    /**
     * API para listar empresas com paginação e filtros
     */
    public function index(Request $request): Response
    {
        try {
            $pagina = $request->get('pagina', 1);
            $porPagina = $request->get('por_pagina', 10);
            $filtros = $request->get('filtros', []);
            $ordenacao = $request->get('ordenacao', 'id');
            $direcao = $request->get('direcao', 'DESC');

            $empresas = $this->empresaModel->getPaginated($pagina, $porPagina, $filtros, $ordenacao, $direcao);
            $total = $this->empresaModel->countWithFilters($filtros);

            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'empresas' => $empresas,
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
                'message' => 'Erro ao carregar empresas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para obter empresa específica
     */
    public function show($id): Response
    {
        try {
            $empresa = $this->empresaModel->find($id);
            
            if (!$empresa) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Empresa não encontrada'
                ], 404);
            }

            // Carregar estabelecimentos da empresa
            $estabelecimentos = $this->estabelecimentoModel->getByEmpresa($id);

            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'empresa' => $empresa,
                    'estabelecimentos' => $estabelecimentos
                ]
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao carregar empresa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para criar empresa
     */
    public function store(Request $request): Response
    {
        try {
            $dados = $request->all();
            
            // Validação
            $validator = Validator::make($dados, [
                'cnpj_raiz' => 'required|size:14|unique:empresas,cnpj_raiz',
                'razao_social' => 'required|min:3|max:200',
                'capital_social' => 'numeric|min:0',
                'responsavel_federativo' => 'max:100',
                'porte_id' => 'integer',
                'natureza_id' => 'integer',
                'qualificacao_id' => 'integer'
            ]);

            if ($validator->fails()) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->getErrors()
                ], 422);
            }

            // Validar CNPJ
            if (!$this->validarCNPJ($dados['cnpj_raiz'])) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'CNPJ inválido'
                ], 422);
            }

            // Criar empresa
            $empresa = $this->empresaModel->create($dados);

            return $this->jsonResponse([
                'success' => true,
                'message' => 'Empresa criada com sucesso',
                'data' => $empresa
            ], 201);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao criar empresa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para atualizar empresa
     */
    public function update(Request $request, $id): Response
    {
        try {
            $empresa = $this->empresaModel->find($id);
            
            if (!$empresa) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Empresa não encontrada'
                ], 404);
            }

            $dados = $request->all();
            
            // Validação
            $regras = [
                'razao_social' => 'required|min:3|max:200',
                'capital_social' => 'numeric|min:0',
                'responsavel_federativo' => 'max:100',
                'porte_id' => 'integer',
                'natureza_id' => 'integer',
                'qualificacao_id' => 'integer'
            ];

            // CNPJ é único, mas pode ser o mesmo da empresa
            if (isset($dados['cnpj_raiz']) && $dados['cnpj_raiz'] !== $empresa->cnpj_raiz) {
                $regras['cnpj_raiz'] = 'required|size:14|unique:empresas,cnpj_raiz,' . $id;
                
                if (!$this->validarCNPJ($dados['cnpj_raiz'])) {
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

            // Atualizar empresa
            $this->empresaModel->update($id, $dados);

            return $this->jsonResponse([
                'success' => true,
                'message' => 'Empresa atualizada com sucesso'
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao atualizar empresa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para excluir empresa
     */
    public function destroy($id): Response
    {
        try {
            $empresa = $this->empresaModel->find($id);
            
            if (!$empresa) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Empresa não encontrada'
                ], 404);
            }

            // Verificar se tem estabelecimentos
            $totalEstabelecimentos = $this->estabelecimentoModel->countByEmpresa($id);
            if ($totalEstabelecimentos > 0) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Não é possível excluir empresa com estabelecimentos cadastrados'
                ], 422);
            }

            // Verificar se tem usuários
            $totalUsuarios = $this->empresaModel->countUsuarios($id);
            if ($totalUsuarios > 0) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Não é possível excluir empresa com usuários cadastrados'
                ], 422);
            }

            // Excluir empresa
            $this->empresaModel->delete($id);

            return $this->jsonResponse([
                'success' => true,
                'message' => 'Empresa excluída com sucesso'
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao excluir empresa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para alterar status da empresa
     */
    public function alterarStatus(Request $request, $id): Response
    {
        try {
            $empresa = $this->empresaModel->find($id);
            
            if (!$empresa) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Empresa não encontrada'
                ], 404);
            }

            $novoStatus = $request->get('status');
            
            if (!in_array($novoStatus, ['ativo', 'inativo', 'suspenso'])) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Status inválido'
                ], 422);
            }

            $this->empresaModel->update($id, ['status' => $novoStatus]);

            return $this->jsonResponse([
                'success' => true,
                'message' => 'Status da empresa alterado com sucesso'
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao alterar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para consultar dados da Receita Federal
     */
    public function consultarReceita(Request $request): Response
    {
        try {
            $cnpj = $request->get('cnpj');
            
            if (!$cnpj || strlen($cnpj) !== 14) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'CNPJ inválido'
                ], 422);
            }

            // Implementar consulta à Receita Federal
            // Por enquanto, retorna dados mockados
            $dadosReceita = $this->consultarReceitaMock($cnpj);

            return $this->jsonResponse([
                'success' => true,
                'data' => $dadosReceita
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao consultar Receita Federal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para obter estatísticas de empresas
     */
    public function getEstatisticas(): Response
    {
        try {
            $estatisticas = [
                'total' => $this->empresaModel->count(),
                'ativas' => $this->empresaModel->countByStatus('ativo'),
                'inativas' => $this->empresaModel->countByStatus('inativo'),
                'suspensas' => $this->empresaModel->countByStatus('suspenso'),
                'por_porte' => $this->empresaModel->getCountByPorte(),
                'por_estado' => $this->empresaModel->getCountByEstado(),
                'por_natureza' => $this->empresaModel->getCountByNatureza(),
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
     * API para exportar empresas
     */
    public function exportar(Request $request): Response
    {
        try {
            $formato = $request->get('formato', 'json');
            $filtros = $request->get('filtros', []);
            
            $empresas = $this->empresaModel->getAllWithFilters($filtros);

            switch ($formato) {
                case 'csv':
                    return $this->exportarCSV($empresas);
                case 'excel':
                    return $this->exportarExcel($empresas);
                case 'pdf':
                    return $this->exportarPDF($empresas);
                default:
                    return $this->jsonResponse([
                        'success' => true,
                        'data' => $empresas
                    ]);
            }

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao exportar empresas: ' . $e->getMessage()
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

    private function consultarReceitaMock($cnpj): array
    {
        // Dados mockados para demonstração
        return [
            'cnpj' => $cnpj,
            'razao_social' => 'EMPRESA EXEMPLO LTDA',
            'nome_fantasia' => 'EMPRESA EXEMPLO',
            'data_abertura' => '01/01/2020',
            'situacao' => 'ATIVA',
            'tipo' => 'MATRIZ',
            'porte' => 'MEDIA EMPRESA',
            'natureza_juridica' => '206-2 - LTDA',
            'capital_social' => '100000.00',
            'endereco' => [
                'logradouro' => 'RUA EXEMPLO',
                'numero' => '123',
                'complemento' => 'SALA 1',
                'bairro' => 'CENTRO',
                'municipio' => 'SAO PAULO',
                'uf' => 'SP',
                'cep' => '01001-000'
            ]
        ];
    }

    private function getCrescimentoMensal()
    {
        $meses = [];
        $valores = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $data = date('Y-m', strtotime("-$i months"));
            $meses[] = date('M/Y', strtotime("-$i months"));
            $valores[] = $this->empresaModel->countByMonth($data);
        }

        return [
            'labels' => $meses,
            'data' => $valores
        ];
    }

    private function exportarCSV($empresas): Response
    {
        $csv = "CNPJ,Razão Social,Nome Fantasia,Status,Porte,Natureza\n";
        
        foreach ($empresas as $empresa) {
            $csv .= "{$empresa->cnpj_raiz},{$empresa->razao_social},{$empresa->nome_fantasia},{$empresa->status},{$empresa->porte_desc},{$empresa->natureza_desc}\n";
        }

        return new Response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="empresas.csv"'
        ]);
    }

    private function exportarExcel($empresas): Response
    {
        // Implementar exportação para Excel
        return new Response('Exportação Excel em desenvolvimento', 200, [
            'Content-Type' => 'text/plain'
        ]);
    }

    private function exportarPDF($empresas): Response
    {
        // Implementar exportação para PDF
        return new Response('Exportação PDF em desenvolvimento', 200, [
            'Content-Type' => 'text/plain'
        ]);
    }

    private function jsonResponse($dados, $statusCode = 200): Response
    {
        return new Response(json_encode($dados), $statusCode, [
            'Content-Type' => 'application/json'
        ]);
    }
} 