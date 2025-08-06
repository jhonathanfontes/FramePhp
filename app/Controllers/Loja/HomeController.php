<?php

namespace App\Controllers\Loja;

use App\Controllers\BaseController;
use App\Models\CadProdutoModel;
use App\Models\CadCategoriaModel;
use App\Models\EmpresaModel;

class HomeController extends BaseController
{
    public function index()
    {
        // Dados da empresa
        $empresa = $this->getEmpresaData();
        
        // Categorias
        $categorias = $this->getCategoriasData();
        
        // Produtos em destaque
        $produtos_destaque = $this->getProdutosDestaqueData();
        
        // Produtos mais vendidos
        $produtos_mais_vendidos = $this->getProdutosMaisVendidosData();
        
        // Banners
        $banners = $this->getBannersData();
        
        // Depoimentos
        $depoimentos = $this->getDepoimentosData();
        
        // Marcas parceiras
        $marcas_parceiras = $this->getMarcasParceirasData();
        
        // Carrinho
        $carrinho = $this->getCarrinhoData();
        
        return $this->view('loja/home/home', [
            'empresa' => $empresa,
            'categorias' => $categorias,
            'produtos_destaque' => $produtos_destaque,
            'produtos_mais_vendidos' => $produtos_mais_vendidos,
            'banners' => $banners,
            'depoimentos' => $depoimentos,
            'marcas_parceiras' => $marcas_parceiras,
            'carrinho' => $carrinho
        ]);
    }
    
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
    
    private function getProdutosDestaqueData()
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
                'parcelas' => 12
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
                'parcelas' => 10
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
                'parcelas' => 8
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
                'parcelas' => 6
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
                'parcelas' => 10
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
                'parcelas' => 6
            ]
        ];
    }
    
    private function getProdutosMaisVendidosData()
    {
        return [
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
                'parcelas' => 3
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
                'parcelas' => 2
            ]
        ];
    }
    
    private function getBannersData()
    {
        return [
            [
                'id' => 1,
                'titulo' => 'Mega Promoção',
                'descricao' => 'Até 50% de desconto em eletrônicos',
                'imagem' => '/assets/images/banners/banner-1.jpg',
                'link' => '/produtos?categoria=1&promocao=1',
                'texto_botao' => 'Ver Ofertas'
            ],
            [
                'id' => 2,
                'titulo' => 'Frete Grátis',
                'descricao' => 'Em compras acima de R$ 100',
                'imagem' => '/assets/images/banners/banner-2.jpg',
                'link' => '/produtos',
                'texto_botao' => 'Comprar Agora'
            ]
        ];
    }
    
    private function getDepoimentosData()
    {
        return [
            [
                'id' => 1,
                'nome' => 'Maria Silva',
                'texto' => 'Excelente atendimento e produtos de qualidade. Recomendo!',
                'avaliacao' => 5,
                'cidade' => 'São Paulo, SP'
            ],
            [
                'id' => 2,
                'nome' => 'João Santos',
                'texto' => 'Entrega rápida e produtos conforme descrição. Muito satisfeito!',
                'avaliacao' => 5,
                'cidade' => 'Rio de Janeiro, RJ'
            ],
            [
                'id' => 3,
                'nome' => 'Ana Costa',
                'texto' => 'Preços imbatíveis e qualidade superior. Já sou cliente fiel!',
                'avaliacao' => 5,
                'cidade' => 'Belo Horizonte, MG'
            ]
        ];
    }
    
    private function getMarcasParceirasData()
    {
        return [
            [
                'id' => 1,
                'nome' => 'Samsung',
                'logo' => '/assets/images/marcas/samsung.png'
            ],
            [
                'id' => 2,
                'nome' => 'Apple',
                'logo' => '/assets/images/marcas/apple.png'
            ],
            [
                'id' => 3,
                'nome' => 'Sony',
                'logo' => '/assets/images/marcas/sony.png'
            ],
            [
                'id' => 4,
                'nome' => 'LG',
                'logo' => '/assets/images/marcas/lg.png'
            ],
            [
                'id' => 5,
                'nome' => 'Nike',
                'logo' => '/assets/images/marcas/nike.png'
            ],
            [
                'id' => 6,
                'nome' => 'Adidas',
                'logo' => '/assets/images/marcas/adidas.png'
            ]
        ];
    }
    
    private function getCarrinhoData()
    {
        return [
            'total_itens' => 3,
            'total_valor' => 1299.99,
            'itens' => [
                [
                    'id' => 1,
                    'produto_id' => 1,
                    'nome' => 'Smartphone Galaxy S21',
                    'preco' => 999.99,
                    'quantidade' => 1,
                    'imagem' => '/assets/images/produtos/smartphone-1.jpg'
                ],
                [
                    'id' => 2,
                    'produto_id' => 2,
                    'nome' => 'Notebook Dell Inspiron',
                    'preco' => 299.99,
                    'quantidade' => 1,
                    'imagem' => '/assets/images/produtos/notebook-1.jpg'
                ]
            ]
        ];
    }
} 