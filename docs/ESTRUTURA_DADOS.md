# Estrutura de Dados - FramePhp

## Visão Geral

Este documento descreve a estrutura dos modelos de dados e arrays de dados falsos utilizados no projeto FramePhp.

## Modelos de Dados

### 1. CadProdutoModel

```php
class CadProdutoModel extends Model
{
    protected $table = 'produtos';
    
    // Estrutura da tabela
    protected $fillable = [
        'id',
        'nome',
        'descricao',
        'preco',
        'preco_antigo',
        'categoria_id',
        'fabricante_id',
        'codigo',
        'sku',
        'estoque',
        'peso',
        'altura',
        'largura',
        'comprimento',
        'imagem',
        'imagens',
        'status',
        'destaque',
        'promocao',
        'avaliacao',
        'total_avaliacoes',
        'total_vendas',
        'created_at',
        'updated_at'
    ];
}
```

**Campos Principais:**
- `id`: Identificador único do produto
- `nome`: Nome do produto
- `descricao`: Descrição detalhada
- `preco`: Preço atual
- `preco_antigo`: Preço anterior (para promoções)
- `categoria_id`: ID da categoria
- `fabricante_id`: ID do fabricante
- `codigo`: Código interno do produto
- `sku`: Código SKU
- `estoque`: Quantidade em estoque
- `peso`: Peso em kg
- `altura`, `largura`, `comprimento`: Dimensões
- `imagem`: Imagem principal
- `imagens`: Array de imagens adicionais
- `status`: Status do produto (ativo/inativo)
- `destaque`: Se é produto em destaque
- `promocao`: Se está em promoção
- `avaliacao`: Média das avaliações
- `total_avaliacoes`: Número total de avaliações
- `total_vendas`: Número total de vendas

### 2. CadCategoriaModel

```php
class CadCategoriaModel extends Model
{
    protected $table = 'categorias';
    
    protected $fillable = [
        'id',
        'nome',
        'descricao',
        'slug',
        'imagem',
        'parent_id',
        'ordem',
        'status',
        'total_produtos',
        'created_at',
        'updated_at'
    ];
}
```

**Campos Principais:**
- `id`: Identificador único da categoria
- `nome`: Nome da categoria
- `descricao`: Descrição da categoria
- `slug`: URL amigável
- `imagem`: Imagem da categoria
- `parent_id`: ID da categoria pai (para subcategorias)
- `ordem`: Ordem de exibição
- `status`: Status da categoria
- `total_produtos`: Número de produtos na categoria

### 3. CadUsuarioModel

```php
class CadUsuarioModel extends Model
{
    protected $table = 'usuarios';
    
    protected $fillable = [
        'id',
        'nome',
        'email',
        'senha',
        'cpf',
        'telefone',
        'data_nascimento',
        'genero',
        'status',
        'tipo',
        'avatar',
        'email_verificado',
        'email_verificado_em',
        'ultimo_login',
        'created_at',
        'updated_at'
    ];
}
```

**Campos Principais:**
- `id`: Identificador único do usuário
- `nome`: Nome completo
- `email`: Email do usuário
- `senha`: Senha criptografada
- `cpf`: CPF do usuário
- `telefone`: Telefone de contato
- `data_nascimento`: Data de nascimento
- `genero`: Gênero (M/F/O)
- `status`: Status do usuário
- `tipo`: Tipo de usuário (cliente/admin)
- `avatar`: Imagem do avatar
- `email_verificado`: Se email foi verificado
- `ultimo_login`: Data do último login

### 4. EmpresaModel

```php
class EmpresaModel extends Model
{
    protected $table = 'empresas';
    
    protected $fillable = [
        'id',
        'nome_fantasia',
        'razao_social',
        'cnpj',
        'inscricao_estadual',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'telefone',
        'email',
        'site',
        'descricao',
        'descricao_curta',
        'slogan',
        'palavras_chave',
        'cor_primaria',
        'cor_secundaria',
        'cor_destaque',
        'cor_texto',
        'cor_fundo',
        'fonte',
        'logo',
        'favicon',
        'facebook',
        'instagram',
        'whatsapp',
        'status',
        'created_at',
        'updated_at'
    ];
}
```

## Arrays de Dados Falsos

### 1. Dados da Empresa

```php
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
```

### 2. Dados das Categorias

```php
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
        // ... mais categorias
    ];
}
```

### 3. Dados dos Produtos

```php
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
        // ... mais produtos
    ];
}
```

### 4. Dados do Carrinho

```php
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
            // ... mais itens
        ],
        'cupom_aplicado' => null
    ];
}
```

### 5. Dados dos Banners

```php
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
        // ... mais banners
    ];
}
```

### 6. Dados dos Depoimentos

```php
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
        // ... mais depoimentos
    ];
}
```

### 7. Dados das Marcas Parceiras

```php
private function getMarcasParceirasData()
{
    return [
        [
            'id' => 1,
            'nome' => 'Samsung',
            'logo' => '/assets/images/marcas/samsung.png'
        ],
        // ... mais marcas
    ];
}
```

## Relacionamentos

### 1. Produto -> Categoria
```php
// Um produto pertence a uma categoria
$produto->categoria = [
    'id' => 1,
    'nome' => 'Eletrônicos'
];
```

### 2. Produto -> Fabricante
```php
// Um produto pertence a um fabricante
$produto->fabricante = [
    'id' => 1,
    'nome' => 'Samsung'
];
```

### 3. Carrinho -> Itens
```php
// Um carrinho possui múltiplos itens
$carrinho->itens = [
    [
        'id' => 1,
        'produto' => $produto,
        'quantidade' => 2,
        'preco_unitario' => 999.99,
        'total' => 1999.98
    ]
];
```

### 4. Usuário -> Pedidos
```php
// Um usuário possui múltiplos pedidos
$usuario->pedidos = [
    [
        'id' => 1,
        'numero' => 'PED-001',
        'status' => 'entregue',
        'total' => 1299.99,
        'data' => '2024-01-15'
    ]
];
```

## Validações

### 1. Produto
- Nome: obrigatório, mínimo 3 caracteres
- Preço: obrigatório, maior que zero
- Categoria: obrigatório, deve existir
- Estoque: obrigatório, maior ou igual a zero

### 2. Usuário
- Nome: obrigatório, mínimo 2 caracteres
- Email: obrigatório, formato válido, único
- CPF: obrigatório, formato válido, único
- Senha: obrigatório, mínimo 8 caracteres

### 3. Empresa
- Nome fantasia: obrigatório
- CNPJ: obrigatório, formato válido, único
- Email: obrigatório, formato válido

## Observações Importantes

1. **Consistência**: Todos os dados falsos seguem a mesma estrutura dos modelos
2. **Realismo**: Dados simulam cenários reais de e-commerce
3. **Flexibilidade**: Estrutura permite fácil adição de novos campos
4. **Performance**: Arrays otimizados para renderização rápida
5. **Manutenibilidade**: Código bem documentado e organizado
