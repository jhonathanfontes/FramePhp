# CSS da Loja Online

Esta pasta contém todos os arquivos CSS necessários para a loja online do FramePhp.

## Estrutura de Arquivos

```
loja/
├── base.css          # Estilos base da loja
├── temas/            # Pasta com temas disponíveis
│   ├── default.css   # Tema padrão (azul/verde)
│   ├── dark.css      # Tema escuro
│   └── elegant.css   # Tema elegante (roxo/rosa)
└── README.md         # Esta documentação
```

## Arquivos CSS

### base.css
Estilos fundamentais da loja, incluindo:
- Reset e normalização
- Sistema de grid
- Componentes básicos (botões, cards, formulários)
- Utilitários CSS
- Responsividade
- Animações básicas

### Temas Disponíveis

#### default.css
- **Cores**: Azul (#007bff) e Verde (#28a745)
- **Estilo**: Clássico e profissional
- **Ideal para**: Lojas tradicionais e corporativas

#### dark.css
- **Cores**: Roxo (#6366f1) e Verde (#10b981)
- **Estilo**: Moderno e escuro
- **Ideal para**: Lojas com público mais jovem e tecnológico

#### elegant.css
- **Cores**: Roxo (#8b5cf6) e Rosa (#ec4899)
- **Estilo**: Elegante e sofisticado
- **Ideal para**: Lojas de moda, beleza e produtos premium

## Como Usar

### No Template Base
O template `base.html.twig` já está configurado para carregar automaticamente:

```html
<!-- CSS Base -->
<link rel="stylesheet" href="/assets/css/loja/base.css">

<!-- CSS do Tema -->
<link rel="stylesheet" href="/assets/css/loja/temas/{{ tema }}.css">
```

### Alterando o Tema
Para alterar o tema, modifique a variável `tema` na sessão:

```php
// No controller
$session->set('tema', 'dark'); // ou 'elegant'
```

### Via JavaScript
O JavaScript da loja permite alternar temas dinamicamente:

```javascript
// Alternar entre temas
LojaApp.toggleTheme();
```

## Variáveis CSS

Todos os temas usam variáveis CSS para facilitar a personalização:

```css
:root {
    --primary-color: #007bff;
    --secondary-color: #6c757d;
    --accent-color: #28a745;
    --text-color: #333;
    --bg-color: #f8f9fa;
    --font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
```

## Componentes Disponíveis

### Produtos
```html
<div class="produto-card">
    <img src="..." class="produto-imagem" alt="...">
    <div class="produto-info">
        <h3 class="produto-titulo">Nome do Produto</h3>
        <p class="produto-descricao">Descrição...</p>
        <div class="produto-preco">R$ 99,90</div>
        <button class="btn btn-comprar">Comprar</button>
    </div>
</div>
```

### Carrinho
```html
<div class="carrinho-item">
    <!-- Item do carrinho -->
</div>
<div class="carrinho-total">
    <!-- Total do carrinho -->
</div>
```

### Categorias
```html
<div class="categoria-card">
    <h3>Nome da Categoria</h3>
    <p>Descrição da categoria</p>
</div>
```

### Banner
```html
<div class="banner">
    <h1>Título do Banner</h1>
    <p>Subtítulo ou descrição</p>
</div>
```

## Responsividade

Todos os temas são totalmente responsivos e funcionam em:
- Desktop (1200px+)
- Tablet (768px - 1199px)
- Mobile (até 767px)

## Animações

Os temas incluem várias animações:
- `fade-in`: Animação de entrada suave
- `float`: Animação flutuante
- Hover effects em cards e botões
- Transições suaves em todos os elementos

## Personalização

Para criar um novo tema:

1. Crie um novo arquivo em `temas/nome-do-tema.css`
2. Defina as variáveis CSS no `:root`
3. Adicione classes específicas do tema (ex: `.theme-nome-do-tema`)
4. Atualize o template para incluir o novo tema

## Compatibilidade

- **Navegadores**: Chrome, Firefox, Safari, Edge (versões modernas)
- **Versões mínimas**: 
  - Chrome 60+
  - Firefox 55+
  - Safari 12+
  - Edge 79+

## Performance

- CSS minificado em produção
- Carregamento otimizado
- Animações usando `transform` e `opacity` para melhor performance
- Lazy loading de imagens recomendado
