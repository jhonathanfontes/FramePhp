# Guia de Contribuição - FramePhp

## Visão Geral

Este guia detalha os padrões de código e a estrutura do projeto FramePhp para novos desenvolvedores.

## Estrutura do Projeto

```
FramePhp/
├── app/
│   ├── Controllers/          # Controladores da aplicação
│   │   ├── Loja/            # Controladores da loja virtual
│   │   ├── Backend/         # Controladores de backend
│   │   ├── Admin/           # Controladores administrativos
│   │   └── Site/            # Controladores do site
│   ├── Models/              # Modelos de dados
│   ├── Services/            # Serviços da aplicação
│   ├── Middleware/          # Middlewares
│   ├── Views/               # Views/Templates
│   │   └── loja/           # Views da loja virtual
│   │       ├── components/  # Componentes reutilizáveis
│   │       ├── layouts/     # Layouts de página
│   │       └── pages/       # Páginas organizadas por diretório
│   │           ├── carrinho/ # Página do carrinho
│   │           │   ├── carrinho.css.twig
│   │           │   └── carrinho.js.twig
│   │           └── produtos/ # Página de produtos
│   │               ├── produtos.css.twig
│   │               └── produtos.js.twig
│   └── Policies/            # Políticas de autorização
├── core/                    # Core do framework
├── config/                  # Configurações
├── routes/                  # Definição de rotas
├── public/                  # Arquivos públicos
├── database/                # Migrações e seeds
└── docs/                    # Documentação
```

## Padrões de Código

### 1. Nomenclatura

#### Classes
```php
// ✅ Correto
class CarrinhoController extends BaseController
class ValidationService
class CadProdutoModel extends Model

// ❌ Incorreto
class carrinhoController
class validation_service
class produto_model
```

#### Métodos
```php
// ✅ Correto
public function adicionarProduto()
public function calcularFrete()
public function getEmpresaData()

// ❌ Incorreto
public function addProduct()
public function calcFrete()
public function empresa_data()
```

#### Variáveis
```php
// ✅ Correto
$produtoId = 1;
$totalValor = 1299.99;
$empresa = $this->getEmpresaData();

// ❌ Incorreto
$produto_id = 1;
$total_valor = 1299.99;
$empresa = $this->get_empresa_data();
```

### 2. Estrutura de Controladores

```php
<?php

namespace App\Controllers\Loja;

use App\Controllers\BaseController;
use App\Services\ValidationService;

/**
 * Controlador do Carrinho de Compras
 * Gerencia todas as operações relacionadas ao carrinho
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
        $total = $subtotal + $frete;
        
        return $this->view('loja/carrinho', [
            'empresa' => $empresa,
            'carrinho' => $carrinho,
            'subtotal' => $subtotal,
            'frete' => $frete,
            'total' => $total
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
        
        // Validação
        if (!$produtoId || $quantidade <= 0) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Dados inválidos'
            ]);
        }
        
        // Lógica de negócio...
        
        return $this->jsonResponse([
            'success' => true,
            'message' => 'Produto adicionado ao carrinho'
        ]);
    }
    
    /**
     * Dados da empresa (dados falsos)
     */
    private function getEmpresaData()
    {
        return [
            'id' => 1,
            'nome_fantasia' => 'Loja Exemplo',
            'razao_social' => 'Loja Exemplo Ltda',
            // ... mais dados
        ];
    }
}
```

### 3. Estrutura de Views

```twig
{% extends "loja/layout.twig" %}

{% block title %}Carrinho - {{ empresa.nome_fantasia }}{% endblock %}

{% block styles %}
    <link rel="stylesheet" href="/assets/css/carrinho.css">
{% endblock %}

{% block content %}
    <div class="cart-page">
        <header class="page-header">
            <h1 class="page-title">🛒 Seu Carrinho</h1>
            <p class="page-subtitle">Revise seus produtos antes de finalizar a compra</p>
        </header>
        
        {% if itens %}
            <!-- Conteúdo do carrinho -->
        {% else %}
            <!-- Carrinho vazio -->
        {% endif %}
    </div>
{% endblock %}

{% block scripts %}
    <script src="/assets/js/carrinho.js"></script>
{% endblock %}
```

### 4. Estrutura de CSS

```css
/* ===== CART PAGE ===== */
.cart-page {
    max-width: 1400px;
    margin: 0 auto;
}

.page-header {
    text-align: center;
    margin-bottom: var(--spacing-xl);
}

.page-title {
    font-size: clamp(1.8rem, 4vw, 2.5rem);
    color: var(--text-color);
    margin-bottom: var(--spacing-sm);
}

/* ===== RESPONSIVIDADE ===== */
@media (max-width: 768px) {
    .cart-item {
        grid-template-columns: 80px 1fr;
        gap: var(--spacing-sm);
    }
}
```

### 5. Estrutura de JavaScript

```javascript
/**
 * Carrinho de Compras - JavaScript
 * Gerencia as funcionalidades do carrinho de compras
 */
class CarrinhoController {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // Event listeners
    }

    /**
     * Altera a quantidade de um produto no carrinho
     */
    alterarQuantidade(produtoId, delta) {
        const button = event.target;
        const originalText = button.innerHTML;
        
        // Feedback visual
        button.classList.add('loading');
        button.innerHTML = '⏳';
        
        fetch('/carrinho/atualizar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getCsrfToken()
            },
            body: JSON.stringify({
                produto_id: produtoId,
                delta: delta
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            button.classList.remove('loading');
            button.innerHTML = originalText;
            this.mostrarNotificacao('Erro ao atualizar quantidade: ' + error.message, 'error');
        });
    }
}

// Inicializa o controlador
document.addEventListener('DOMContentLoaded', () => {
    window.carrinhoController = new CarrinhoController();
});
```

## Nova Estrutura de Diretórios de Views

### 1. Organização de Páginas
**Localização:** `app/Views/loja/pages/{nome-da-pagina}/`
**Estrutura:** Cada página tem seu próprio diretório com CSS e JS dedicados

```
app/Views/loja/pages/
├── carrinho/
│   ├── carrinho.css.twig    # Estilos específicos do carrinho
│   └── carrinho.js.twig     # JavaScript específico do carrinho
├── produtos/
│   ├── produtos.css.twig    # Estilos específicos dos produtos
│   └── produtos.js.twig     # JavaScript específico dos produtos
└── checkout/
    ├── checkout.css.twig    # Estilos específicos do checkout
    └── checkout.js.twig     # JavaScript específico do checkout
```

### 2. Componentes Reutilizáveis
**Localização:** `app/Views/loja/components/`
**Responsabilidade:** Elementos reutilizáveis em múltiplas páginas

```
app/Views/loja/components/
├── header.html.twig         # Cabeçalho da loja
├── footer.html.twig         # Rodapé da loja
├── product-card.html.twig   # Card de produto
└── cart-item.html.twig      # Item do carrinho
```

### 3. Layouts de Página
**Localização:** `app/Views/loja/layouts/`
**Responsabilidade:** Estruturas base para páginas

```
app/Views/loja/layouts/
├── base.html.twig           # Layout base da loja
└── checkout.html.twig       # Layout específico do checkout
```

### 4. Inclusão de Estilos e Scripts

```twig
{# Em uma view .twig #}
{% block styles %}
    <style>
        {% include "loja/pages/carrinho/carrinho.css.twig" %}
    </style>
{% endblock %}

{% block scripts %}
    <script>
        {% include "loja/pages/carrinho/carrinho.js.twig" %}
    </script>
{% endblock %}
```

### 5. Vantagens da Nova Estrutura

- **Organização:** Cada página tem seus arquivos CSS/JS dedicados
- **Manutenibilidade:** Fácil localização e edição de estilos/scripts
- **Reutilização:** Componentes podem ser usados em múltiplas páginas
- **Performance:** CSS/JS específicos carregados apenas quando necessário
- **Escalabilidade:** Fácil adição de novas páginas seguindo o padrão

## Arquitetura de Controladores

### 1. Controladores de Views
**Localização:** `app/Controllers/Loja/`
**Responsabilidade:** Renderizar páginas e gerenciar interface

```php
// Exemplo: CarrinhoController
class CarrinhoController extends BaseController
{
    public function index() // Renderiza página do carrinho
    public function adicionar() // Adiciona produto via AJAX
    public function atualizar() // Atualiza quantidade via AJAX
    public function remover() // Remove produto via AJAX
}
```

### 2. Controladores de Backend
**Localização:** `app/Controllers/Backend/Loja/`
**Responsabilidade:** Lógica de negócio e processamento

```php
// Exemplo: BackendCarrinhoController
class BackendCarrinhoController extends BaseController
{
    public function adicionar() // Processa adição de produto
    public function atualizar() // Processa atualização
    public function remover() // Processa remoção
    public function limpar() // Processa limpeza do carrinho
}
```

## Padrões de Dados

### 1. Dados Falsos
Sempre use dados falsos realistas nos controladores de views:

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

### 2. Estrutura de Produtos
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
        ]
    ];
}
```

## Padrões de Validação

### 1. Validação de Dados
Use os serviços de validação existentes:

```php
use App\Services\ValidationService;
use App\Services\CepService;

// Validação de CPF
if (!ValidationService::validarCpf($cpf)) {
    return $this->jsonResponse([
        'success' => false,
        'message' => 'CPF inválido'
    ]);
}

// Validação de CEP
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
```

### 2. Respostas JSON
Sempre padronize as respostas JSON:

```php
// Sucesso
return $this->jsonResponse([
    'success' => true,
    'message' => 'Operação realizada com sucesso',
    'data' => $dados
]);

// Erro
return $this->jsonResponse([
    'success' => false,
    'message' => 'Erro na operação',
    'errors' => $erros
]);
```

## Padrões de CSS

### 1. Organização
- Separe CSS por funcionalidade
- Use comentários para seções
- Mantenha responsividade
- Use variáveis CSS

### 2. Nomenclatura
```css
/* Classes principais */
.cart-page { }
.cart-layout { }
.cart-items { }

/* Estados */
.btn.loading { }
.cart-item:hover { }

/* Responsividade */
@media (max-width: 768px) { }
```

## Padrões de JavaScript

### 1. Organização
- Use classes para organizar código
- Mantenha funções globais para compatibilidade
- Documente métodos importantes

### 2. Tratamento de Erros
```javascript
.catch(error => {
    console.error('Erro:', error);
    button.classList.remove('loading');
    button.innerHTML = originalText;
    this.mostrarNotificacao('Erro: ' + error.message, 'error');
});
```

## Checklist para Novas Funcionalidades

### 1. Controlador
- [ ] Criar controlador seguindo padrão de nomenclatura
- [ ] Implementar métodos necessários
- [ ] Adicionar dados falsos realistas
- [ ] Implementar validações
- [ ] Adicionar comentários descritivos

### 2. View
- [ ] Criar view seguindo estrutura Twig
- [ ] Remover CSS inline (usar arquivo externo)
- [ ] Remover JavaScript inline (usar arquivo externo)
- [ ] Implementar responsividade
- [ ] Adicionar acessibilidade

### 3. CSS
- [ ] Criar arquivo CSS específico
- [ ] Organizar por seções
- [ ] Implementar responsividade
- [ ] Usar variáveis CSS
- [ ] Testar em diferentes dispositivos

### 4. JavaScript
- [ ] Criar arquivo JS específico
- [ ] Implementar classe organizada
- [ ] Adicionar tratamento de erros
- [ ] Manter funções globais para compatibilidade
- [ ] Documentar métodos

### 5. Rotas
- [ ] Adicionar rotas no arquivo correto
- [ ] Seguir padrão de nomenclatura
- [ ] Adicionar middlewares necessários
- [ ] Documentar rota

### 6. Testes
- [ ] Testar funcionalidade completa
- [ ] Verificar responsividade
- [ ] Testar validações
- [ ] Verificar acessibilidade
- [ ] Testar em diferentes navegadores

## Comandos Úteis

### 1. Estrutura de Arquivos
```bash
# Criar controlador
touch app/Controllers/Loja/NovoController.php

# Criar view
touch app/Views/loja/nova-view.twig

# Criar CSS
touch public/assets/css/nova-funcionalidade.css

# Criar JavaScript
touch public/assets/js/nova-funcionalidade.js
```

### 2. Padrões de Commit
```bash
# Adicionar nova funcionalidade
git add .
git commit -m "feat: adiciona funcionalidade de carrinho"

# Corrigir bug
git commit -m "fix: corrige validação de CPF"

# Melhorar documentação
git commit -m "docs: atualiza guia de contribuição"
```

## Observações Importantes

1. **Consistência**: Sempre siga os padrões estabelecidos
2. **Documentação**: Comente código complexo
3. **Testes**: Teste sempre antes de commitar
4. **Responsividade**: Sempre implemente design responsivo
5. **Acessibilidade**: Considere usuários com necessidades especiais
6. **Performance**: Otimize código quando necessário
7. **Segurança**: Valide sempre dados de entrada
8. **Manutenibilidade**: Escreva código limpo e organizado
