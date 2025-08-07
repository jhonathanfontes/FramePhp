<?php

namespace App\Controllers\Loja;

use App\Controllers\BaseController;

/**
 * Controlador de Busca e Filtragem de Produtos
 * Gerencia busca, filtros e autocomplete de produtos
 */
class BuscaController extends BaseController
{
    /**
     * Página de busca de produtos
     */
    public function index()
    {
        $request = $this->request;
        $q = $request->get('q', '');
        $categoria = $request->get('categoria', '');
        $preco_min = $request->get('preco_min', '');
        $preco_max = $request->get('preco_max', '');
        $ordenacao = $request->get('ordenacao', 'relevancia');
        $pagina = $request->get('pagina', 1);
        
        // Dados da empresa
        $empresa = $this->getEmpresaData();
        
        // Categorias para filtros
        $categorias = $this->getCategoriasData();
        
        // Realiza a busca
        $resultados = $this->buscarProdutos($q, $categoria, $preco_min, $preco_max, $ordenacao, $pagina);
        
        return $this->view('loja/busca', [
            'empresa' => $empresa,
            'categorias' => $categorias,
            'resultados' => $resultados,
            'filtros' => [
                'q' => $q,
                'categoria' => $categoria,
                'preco_min' => $preco_min,
                'preco_max' => $preco_max,
                'ordenacao' => $ordenacao,
                'pagina' => $pagina
            ]
        ]);
    }
    
    /**
     * Autocomplete para busca
     */
    public function autocomplete()
    {
        $request = $this->request;
        $q = $request->get('q', '');
        
        if (strlen($q) < 2) {
            return $this->jsonResponse([]);
        }
        
        $produtos = $this->getProdutosData();
        $sugestoes = [];
        
        foreach ($produtos as $produto) {
            if (stripos($produto['nome'], $q) !== false || 
                stripos($produto['descricao'], $q) !== false ||
                stripos($produto['categoria']['nome'], $q) !== false) {
                
                $sugestoes[] = [
                    'id' => $produto['id'],
                    'nome' => $produto['nome'],
                    'categoria' => $produto['categoria']['nome'],
                    'preco' => $produto['preco'],
                    'imagem' => $produto['imagem']
                ];
                
                if (count($sugestoes) >= 5) {
                    break;
                }
            }
        }
        
        return $this->jsonResponse($sugestoes);
    }
    
    /**
     * Busca produtos com filtros
     */
    private function buscarProdutos($q, $categoria, $preco_min, $preco_max, $ordenacao, $pagina)
    {
        $produtos = $this->getProdutosData();
        $resultados = [];
        
        foreach ($produtos as $produto) {
            $incluir = true;
            
            // Filtro por termo de busca
            if ($q && stripos($produto['nome'], $q) === false && 
                stripos($produto['descricao'], $q) === false) {
                $incluir = false;
            }
            
            // Filtro por categoria
            if ($categoria && $produto['categoria']['id'] != $categoria) {
                $incluir = false;
            }
            
            // Filtro por preço mínimo
            if ($preco_min && $produto['preco'] < $preco_min) {
                $incluir = false;
            }
            
            // Filtro por preço máximo
            if ($preco_max && $produto['preco'] > $preco_max) {
                $incluir = false;
            }
            
            if ($incluir) {
                $resultados[] = $produto;
            }
        }
        
        // Ordenação
        $resultados = $this->ordenarProdutos($resultados, $ordenacao);
        
        // Paginação
        $por_pagina = 12;
        $total = count($resultados);
        $total_paginas = ceil($total / $por_pagina);
        $offset = ($pagina - 1) * $por_pagina;
        
        $resultados = array_slice($resultados, $offset, $por_pagina);
        
        return [
            'produtos' => $resultados,
            'total' => $total,
            'pagina_atual' => $pagina,
            'total_paginas' => $total_paginas,
            'por_pagina' => $por_pagina
        ];
    }
    
    /**
     * Ordena produtos
     */
    private function ordenarProdutos($produtos, $ordenacao)
    {
        switch ($ordenacao) {
            case 'preco_menor':
                usort($produtos, function($a, $b) {
                    return $a['preco'] <=> $b['preco'];
                });
                break;
                
            case 'preco_maior':
                usort($produtos, function($a, $b) {
                    return $b['preco'] <=> $a['preco'];
                });
                break;
                
            case 'nome':
                usort($produtos, function($a, $b) {
                    return strcasecmp($a['nome'], $b['nome']);
                });
                break;
                
            case 'avaliacao':
                usort($produtos, function($a, $b) {
                    return $b['avaliacao'] <=> $a['avaliacao'];
                });
                break;
                
            case 'mais_vendidos':
                usort($produtos, function($a, $b) {
                    return $b['total_vendas'] <=> $a['total_vendas'];
                });
                break;
                
            default: // relevancia
                // Mantém ordem original (mais relevantes primeiro)
                break;
        }
        
        return $produtos;
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
     * Dados das categorias
     */
    private function getCategoriasData()
    {
        return [
            [
                'id' => 1,
                'nome' => 'Eletrônicos',
                'descricao' => 'Smartphones, tablets, notebooks e mais',
                'imagem' => '/assets/images/categorias/eletronicos.jpg',
                'total_produtos' => 45,
                'slug' => 'eletronicos'
            ],
            [
                'id' => 2,
                'nome' => 'Informática',
                'descricao' => 'Computadores, periféricos e acessórios',
                'imagem' => '/assets/images/categorias/informatica.jpg',
                'total_produtos' => 32,
                'slug' => 'informatica'
            ],
            [
                'id' => 3,
                'nome' => 'Casa e Jardim',
                'descricao' => 'Decoração, utilidades domésticas',
                'imagem' => '/assets/images/categorias/casa-jardim.jpg',
                'total_produtos' => 28,
                'slug' => 'casa-jardim'
            ],
            [
                'id' => 4,
                'nome' => 'Esportes',
                'descricao' => 'Equipamentos e roupas esportivas',
                'imagem' => '/assets/images/categorias/esportes.jpg',
                'total_produtos' => 19,
                'slug' => 'esportes'
            ],
            [
                'id' => 5,
                'nome' => 'Livros',
                'descricao' => 'Literatura, técnicos e didáticos',
                'imagem' => '/assets/images/categorias/livros.jpg',
                'total_produtos' => 67,
                'slug' => 'livros'
            ],
            [
                'id' => 6,
                'nome' => 'Moda',
                'descricao' => 'Roupas, calçados e acessórios',
                'imagem' => '/assets/images/categorias/moda.jpg',
                'total_produtos' => 89,
                'slug' => 'moda'
            ]
        ];
    }
    
    /**
     * Dados dos produtos
     */
    private function getProdutosData()
    {
        return [
            [
                'id' => 1,
                'nome' => 'Smartphone Galaxy S21',
                'descricao' => 'Smartphone Samsung Galaxy S21 128GB',
                'preco' => 2999.99,
                'preco_antigo' => 3499.99,
                'imagem' => '/assets/images/produtos/smartphone-1.jpg',
                'categoria' => ['id' => 1, 'nome' => 'Eletrônicos'],
                'avaliacao' => 4.5,
                'total_avaliacoes' => 127,
                'estoque' => 15,
                'promocao' => '15% OFF',
                'parcelas' => 12,
                'total_vendas' => 234
            ],
            [
                'id' => 2,
                'nome' => 'Notebook Dell Inspiron',
                'descricao' => 'Notebook Dell Inspiron 15" Intel i5',
                'preco' => 4299.99,
                'preco_antigo' => null,
                'imagem' => '/assets/images/produtos/notebook-1.jpg',
                'categoria' => ['id' => 2, 'nome' => 'Informática'],
                'avaliacao' => 4.8,
                'total_avaliacoes' => 89,
                'estoque' => 8,
                'promocao' => null,
                'parcelas' => 10,
                'total_vendas' => 156
            ],
            [
                'id' => 3,
                'nome' => 'Smart TV LG 55"',
                'descricao' => 'Smart TV LG 55" 4K UHD',
                'preco' => 2499.99,
                'preco_antigo' => 2999.99,
                'imagem' => '/assets/images/produtos/tv-1.jpg',
                'categoria' => ['id' => 1, 'nome' => 'Eletrônicos'],
                'avaliacao' => 4.6,
                'total_avaliacoes' => 203,
                'estoque' => 3,
                'promocao' => '20% OFF',
                'parcelas' => 8,
                'total_vendas' => 89
            ],
            [
                'id' => 4,
                'nome' => 'Fone de Ouvido Sony',
                'descricao' => 'Fone de ouvido Sony WH-1000XM4',
                'preco' => 1299.99,
                'preco_antigo' => null,
                'imagem' => '/assets/images/produtos/fone-1.jpg',
                'categoria' => ['id' => 1, 'nome' => 'Eletrônicos'],
                'avaliacao' => 4.9,
                'total_avaliacoes' => 156,
                'estoque' => 22,
                'promocao' => null,
                'parcelas' => 6,
                'total_vendas' => 312
            ],
            [
                'id' => 5,
                'nome' => 'Câmera Canon EOS',
                'descricao' => 'Câmera DSLR Canon EOS Rebel T7',
                'preco' => 1899.99,
                'preco_antigo' => 2199.99,
                'imagem' => '/assets/images/produtos/camera-1.jpg',
                'categoria' => ['id' => 1, 'nome' => 'Eletrônicos'],
                'avaliacao' => 4.7,
                'total_avaliacoes' => 78,
                'estoque' => 5,
                'promocao' => '15% OFF',
                'parcelas' => 10,
                'total_vendas' => 45
            ],
            [
                'id' => 6,
                'nome' => 'Tênis Nike Air Max',
                'descricao' => 'Tênis Nike Air Max 270',
                'preco' => 599.99,
                'preco_antigo' => 699.99,
                'imagem' => '/assets/images/produtos/tenis-1.jpg',
                'categoria' => ['id' => 6, 'nome' => 'Moda'],
                'avaliacao' => 4.4,
                'total_avaliacoes' => 234,
                'estoque' => 12,
                'promocao' => '10% OFF',
                'parcelas' => 6,
                'total_vendas' => 567
            ],
            [
                'id' => 7,
                'nome' => 'Mouse Gamer Logitech',
                'descricao' => 'Mouse Gamer Logitech G502 HERO',
                'preco' => 299.99,
                'preco_antigo' => null,
                'imagem' => '/assets/images/produtos/mouse-1.jpg',
                'categoria' => ['id' => 2, 'nome' => 'Informática'],
                'avaliacao' => 4.8,
                'total_avaliacoes' => 445,
                'estoque' => 25,
                'promocao' => null,
                'parcelas' => 3,
                'total_vendas' => 789
            ],
            [
                'id' => 8,
                'nome' => 'Livro O Poder do Hábito',
                'descricao' => 'Livro O Poder do Hábito - Charles Duhigg',
                'preco' => 39.99,
                'preco_antigo' => 49.99,
                'imagem' => '/assets/images/produtos/livro-1.jpg',
                'categoria' => ['id' => 5, 'nome' => 'Livros'],
                'avaliacao' => 4.6,
                'total_avaliacoes' => 892,
                'estoque' => 45,
                'promocao' => '20% OFF',
                'parcelas' => 2,
                'total_vendas' => 1234
            ]
        ];
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
