<?php

namespace App\Controllers\Loja;

use App\Controllers\BaseController;
use App\Services\ValidationService;
use App\Services\CepService;

/**
 * Controlador do Carrinho de Compras
 * Gerencia todas as operações relacionadas ao carrinho de compras
 */
class CarrinhoController extends BaseController
{
    /**
     * Exibe a página do carrinho
     */
    public function index()
    {
        // Dados da empresa
        $empresa = $this->getEmpresaData();
        
        // Dados do carrinho
        $carrinho = $this->getCarrinhoData();
        
        // Calcula totais
        $subtotal = $this->calcularSubtotal($carrinho['itens']);
        $frete = $this->calcularFrete($carrinho['itens']);
        $desconto = $this->calcularDesconto($carrinho);
        $total = $subtotal + $frete - $desconto;
        
        return $this->view('loja/carrinho', [
            'empresa' => $empresa,
            'itens' => $carrinho['itens'],
            'subtotal' => $subtotal,
            'frete' => $frete,
            'desconto' => $desconto,
            'total' => $total,
            'cupom_aplicado' => $carrinho['cupom_aplicado'] ?? null
        ]);
    }
    
    /**
     * Adiciona produto ao carrinho
     */
    public function adicionar()
    {
        $request = $this->request;
        $produtoId = $request->get('produto_id');
        $quantidade = $request->get('quantidade', 1);
        
        // Validação básica
        if (!$produtoId || $quantidade <= 0) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Dados inválidos'
            ]);
        }
        
        // Simula adição ao carrinho
        $carrinho = $this->getCarrinhoData();
        $produto = $this->getProdutoById($produtoId);
        
        if (!$produto) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Produto não encontrado'
            ]);
        }
        
        // Verifica se já existe no carrinho
        $itemExistente = null;
        foreach ($carrinho['itens'] as $item) {
            if ($item['produto']['id'] == $produtoId) {
                $itemExistente = $item;
                break;
            }
        }
        
        if ($itemExistente) {
            // Atualiza quantidade
            $itemExistente['quantidade'] += $quantidade;
            $itemExistente['total'] = $itemExistente['preco_unitario'] * $itemExistente['quantidade'];
        } else {
            // Adiciona novo item
            $carrinho['itens'][] = [
                'id' => count($carrinho['itens']) + 1,
                'produto' => $produto,
                'quantidade' => $quantidade,
                'preco_unitario' => $produto['preco'],
                'total' => $produto['preco'] * $quantidade
            ];
        }
        
        // Atualiza total do carrinho
        $carrinho['total_itens'] = count($carrinho['itens']);
        $carrinho['total_valor'] = $this->calcularSubtotal($carrinho['itens']);
        
        return $this->jsonResponse([
            'success' => true,
            'message' => 'Produto adicionado ao carrinho',
            'carrinho' => $carrinho
        ]);
    }
    
    /**
     * Atualiza quantidade de um produto
     */
    public function atualizar()
    {
        $request = $this->request;
        $produtoId = $request->get('produto_id');
        $delta = $request->get('delta', 0);
        
        if (!$produtoId) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Produto não especificado'
            ]);
        }
        
        $carrinho = $this->getCarrinhoData();
        
        // Encontra o item no carrinho
        foreach ($carrinho['itens'] as &$item) {
            if ($item['produto']['id'] == $produtoId) {
                $novaQuantidade = $item['quantidade'] + $delta;
                
                if ($novaQuantidade <= 0) {
                    // Remove o item se quantidade for 0 ou menor
                    $carrinho['itens'] = array_filter($carrinho['itens'], function($i) use ($produtoId) {
                        return $i['produto']['id'] != $produtoId;
                    });
                } else {
                    // Atualiza quantidade
                    $item['quantidade'] = $novaQuantidade;
                    $item['total'] = $item['preco_unitario'] * $novaQuantidade;
                }
                break;
            }
        }
        
        // Reindexa array
        $carrinho['itens'] = array_values($carrinho['itens']);
        
        // Atualiza totais
        $carrinho['total_itens'] = count($carrinho['itens']);
        $carrinho['total_valor'] = $this->calcularSubtotal($carrinho['itens']);
        
        return $this->jsonResponse([
            'success' => true,
            'message' => 'Quantidade atualizada',
            'carrinho' => $carrinho
        ]);
    }
    
    /**
     * Remove um produto do carrinho
     */
    public function remover()
    {
        $request = $this->request;
        $produtoId = $request->get('produto_id');
        
        if (!$produtoId) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Produto não especificado'
            ]);
        }
        
        $carrinho = $this->getCarrinhoData();
        
        // Remove o item
        $carrinho['itens'] = array_filter($carrinho['itens'], function($item) use ($produtoId) {
            return $item['produto']['id'] != $produtoId;
        });
        
        // Reindexa array
        $carrinho['itens'] = array_values($carrinho['itens']);
        
        // Atualiza totais
        $carrinho['total_itens'] = count($carrinho['itens']);
        $carrinho['total_valor'] = $this->calcularSubtotal($carrinho['itens']);
        
        return $this->jsonResponse([
            'success' => true,
            'message' => 'Produto removido do carrinho',
            'carrinho' => $carrinho
        ]);
    }
    
    /**
     * Limpa todo o carrinho
     */
    public function limpar()
    {
        $carrinho = [
            'total_itens' => 0,
            'total_valor' => 0,
            'itens' => [],
            'cupom_aplicado' => null
        ];
        
        return $this->jsonResponse([
            'success' => true,
            'message' => 'Carrinho limpo',
            'carrinho' => $carrinho
        ]);
    }
    
    /**
     * Aplica cupom de desconto
     */
    public function aplicarCupom()
    {
        $request = $this->request;
        $cupom = $request->get('cupom');
        
        if (!$cupom) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Cupom não especificado'
            ]);
        }
        
        // Valida cupom (simulação)
        $cuponsValidos = [
            'DESCONTO10' => ['tipo' => 'percentual', 'valor' => 10],
            'DESCONTO20' => ['tipo' => 'percentual', 'valor' => 20],
            'FRETE0' => ['tipo' => 'frete', 'valor' => 0],
            'PROMO50' => ['tipo' => 'percentual', 'valor' => 50]
        ];
        
        if (!isset($cuponsValidos[$cupom])) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Cupom inválido'
            ]);
        }
        
        $carrinho = $this->getCarrinhoData();
        $carrinho['cupom_aplicado'] = [
            'codigo' => $cupom,
            'dados' => $cuponsValidos[$cupom]
        ];
        
        return $this->jsonResponse([
            'success' => true,
            'message' => 'Cupom aplicado com sucesso',
            'carrinho' => $carrinho
        ]);
    }
    
    /**
     * Remove cupom aplicado
     */
    public function removerCupom()
    {
        $carrinho = $this->getCarrinhoData();
        $carrinho['cupom_aplicado'] = null;
        
        return $this->jsonResponse([
            'success' => true,
            'message' => 'Cupom removido',
            'carrinho' => $carrinho
        ]);
    }
    
    /**
     * Calcula frete via API
     */
    public function calcularFreteApi()
    {
        $request = $this->request;
        $cep = $request->get('cep');
        
        if (!$cep) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'CEP não especificado'
            ]);
        }
        
        // Valida CEP
        if (!ValidationService::validarCep($cep)) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'CEP inválido'
            ]);
        }
        
        // Consulta CEP
        $resultado = CepService::consultarCep($cep);
        
        if (!$resultado['success']) {
            return $this->jsonResponse([
                'success' => false,
                'message' => $resultado['message']
            ]);
        }
        
        $carrinho = $this->getCarrinhoData();
        $peso = $this->calcularPeso($carrinho['itens']);
        
        // Calcula frete
        $frete = CepService::calcularFrete('01234-567', $cep, $peso);
        
        return $this->jsonResponse([
            'success' => true,
            'frete' => $frete['data'],
            'endereco' => $resultado['data']
        ]);
    }
    
    /**
     * Dados da empresa
     */
    private function getEmpresaData()
    {
        return [
            'id' => 1,
            'nome_fantasia' => 'Loja Exemplo',
            'razao_social' => 'Loja Exemplo Ltda',
            'cnpj' => '12.345.678/0001-90',
            'endereco' => 'Rua das Flores, 123',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => '01234-567',
            'telefone' => '(11) 99999-9999',
            'email' => 'contato@lojaexemplo.com.br',
            'site' => 'https://lojaexemplo.com.br',
            'descricao' => 'Sua loja online de confiança com os melhores produtos e preços imbatíveis.',
            'descricao_curta' => 'Produtos de qualidade com preços imbatíveis',
            'slogan' => 'Qualidade e Preço em Um Só Lugar',
            'palavras_chave' => 'loja, produtos, online, qualidade, preço',
            'cor_primaria' => '#007bff',
            'cor_secundaria' => '#6c757d',
            'cor_destaque' => '#28a745',
            'cor_texto' => '#333',
            'cor_fundo' => '#f8f9fa',
            'fonte' => 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif',
            'logo' => '/assets/images/logo.png',
            'favicon' => '/assets/images/favicon.ico',
            'facebook' => 'https://facebook.com/lojaexemplo',
            'instagram' => 'https://instagram.com/lojaexemplo',
            'whatsapp' => '5511999999999'
        ];
    }
    
    /**
     * Dados do carrinho (simulação)
     */
    private function getCarrinhoData()
    {
        return [
            'total_itens' => 3,
            'total_valor' => 1299.99,
            'itens' => [
                [
                    'id' => 1,
                    'produto' => [
                        'id' => 1,
                        'nome' => 'Smartphone Galaxy S21',
                        'codigo' => 'SM-G991B',
                        'preco' => 999.99,
                        'imagem' => '/assets/images/produtos/smartphone-1.jpg'
                    ],
                    'quantidade' => 1,
                    'preco_unitario' => 999.99,
                    'total' => 999.99
                ],
                [
                    'id' => 2,
                    'produto' => [
                        'id' => 2,
                        'nome' => 'Notebook Dell Inspiron',
                        'codigo' => 'DELL-INS15',
                        'preco' => 299.99,
                        'imagem' => '/assets/images/produtos/notebook-1.jpg'
                    ],
                    'quantidade' => 1,
                    'preco_unitario' => 299.99,
                    'total' => 299.99
                ]
            ],
            'cupom_aplicado' => null
        ];
    }
    
    /**
     * Obtém produto por ID
     */
    private function getProdutoById($id)
    {
        $produtos = [
            1 => [
                'id' => 1,
                'nome' => 'Smartphone Galaxy S21',
                'codigo' => 'SM-G991B',
                'preco' => 999.99,
                'imagem' => '/assets/images/produtos/smartphone-1.jpg'
            ],
            2 => [
                'id' => 2,
                'nome' => 'Notebook Dell Inspiron',
                'codigo' => 'DELL-INS15',
                'preco' => 299.99,
                'imagem' => '/assets/images/produtos/notebook-1.jpg'
            ],
            3 => [
                'id' => 3,
                'nome' => 'Smart TV LG 55"',
                'codigo' => 'LG-55UN7300',
                'preco' => 2499.99,
                'imagem' => '/assets/images/produtos/tv-1.jpg'
            ]
        ];
        
        return $produtos[$id] ?? null;
    }
    
    /**
     * Calcula subtotal do carrinho
     */
    private function calcularSubtotal($itens)
    {
        $subtotal = 0;
        foreach ($itens as $item) {
            $subtotal += $item['total'];
        }
        return $subtotal;
    }
    
    /**
     * Calcula frete
     */
    private function calcularFrete($itens)
    {
        // Simulação de cálculo de frete
        $peso = $this->calcularPeso($itens);
        return $peso * 2.50; // R$ 2,50 por kg
    }
    
    /**
     * Calcula peso total
     */
    private function calcularPeso($itens)
    {
        $peso = 0;
        foreach ($itens as $item) {
            // Simula peso baseado no tipo de produto
            $peso += $item['quantidade'] * 0.5; // 500g por item
        }
        return $peso;
    }
    
    /**
     * Calcula desconto
     */
    private function calcularDesconto($carrinho)
    {
        if (!$carrinho['cupom_aplicado']) {
            return 0;
        }
        
        $cupom = $carrinho['cupom_aplicado'];
        $subtotal = $this->calcularSubtotal($carrinho['itens']);
        
        if ($cupom['dados']['tipo'] == 'percentual') {
            return $subtotal * ($cupom['dados']['valor'] / 100);
        }
        
        return 0;
    }
    
    /**
     * Retorna resposta JSON
     */
    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
} 