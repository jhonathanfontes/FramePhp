# Estrutura da Loja - FramePhp

## Visão Geral

A loja foi reestruturada com uma arquitetura robusta, modular e de fácil manutenção, seguindo as melhores práticas de desenvolvimento web.

## Estrutura de Diretórios

```
app/Views/loja/
├── layouts/
│   └── base.html.twig          # Layout base com sistema de temas
├── components/
│   ├── header.html.twig        # Header reutilizável
│   ├── footer.html.twig        # Footer reutilizável
│   ├── messages.html.twig      # Componente de mensagens flash
│   ├── hero.html.twig          # Seção hero da home
│   ├── categorias-destaque.html.twig
│   ├── produtos-destaque.html.twig
│   ├── banner-promocional.html.twig
│   ├── produtos-mais-vendidos.html.twig
│   ├── depoimentos.html.twig
│   ├── newsletter-home.html.twig
│   └── marcas-parceiras.html.twig
└── home/
    ├── home.html.twig          # Página home principal
    ├── home.css.twig           # CSS específico da home
    └── home.js.twig            # JavaScript específico da home
```

## Características Principais

### 1. Sistema de Temas Personalizáveis
- Cores configuráveis via session/cookies
- Múltiplos temas disponíveis
- Personalização por empresa
- Variáveis CSS dinâmicas

### 2. Componentes Reutilizáveis
- Header com menu dinâmico
- Footer com informações da empresa
- Sistema de mensagens flash
- Componentes modulares para cada seção

### 3. Estrutura de Páginas Separada
- HTML, CSS e JS separados por funcionalidade
- Fácil manutenção e personalização
- Carregamento otimizado de recursos

### 4. Dados Fake para Visualização
- Produtos, categorias e empresas simuladas
- Facilita o desenvolvimento e testes
- Estrutura realista de dados

## Rotas Organizadas

### Rotas Públicas
- `/` - Home da loja
- `/produtos` - Lista de produtos
- `/produto/{id}` - Detalhes do produto
- `/categoria/{id}` - Produtos por categoria
- `/busca` - Busca de produtos
- `/sobre`, `/contato` - Páginas estáticas

### Rotas de Autenticação
- `/loja/login` - Login
- `/loja/cadastro` - Cadastro
- `/loja/esqueci-senha` - Recuperação de senha

### Rotas Protegidas
- `/loja/perfil` - Perfil do usuário
- `/loja/pedidos` - Histórico de pedidos
- `/loja/favoritos` - Produtos favoritos
- `/loja/enderecos` - Gerenciar endereços

### Rotas do Carrinho
- `/carrinho` - Visualizar carrinho
- `/carrinho/adicionar` - Adicionar produto
- `/carrinho/atualizar` - Atualizar quantidade
- `/carrinho/remover` - Remover produto

### Rotas de API
- `/api/loja/cep/{cep}` - Consulta CEP
- `/api/loja/validar/cpf` - Validação CPF
- `/api/loja/validar/cnpj` - Validação CNPJ

## Serviços Criados

### ValidationService
- Validação de CPF e CNPJ
- Validação de e-mail e telefone
- Formatação de documentos
- Validação de senha forte

### CepService
- Consulta CEP via APIs
- Autocomplete de endereço
- Cálculo de frete
- Formatação de CEP

## JavaScript Avançado

### Funcionalidades Implementadas
- Autocomplete de CEP
- Validação em tempo real
- Máscaras de input
- Notificações dinâmicas
- Animações suaves
- Lazy loading de imagens

### Validações
- CPF com algoritmo oficial
- CNPJ com validação completa
- E-mail com regex
- Telefone brasileiro
- Senha com indicador de força

## Sistema de Temas

### Variáveis CSS Dinâmicas
```css
:root {
    --primary-color: {{ empresa.cor_primaria }};
    --secondary-color: {{ empresa.cor_secundaria }};
    --accent-color: {{ empresa.cor_destaque }};
    --text-color: {{ empresa.cor_texto }};
    --bg-color: {{ empresa.cor_fundo }};
    --font-family: {{ empresa.fonte }};
}
```

### Temas Disponíveis
- `default` - Tema padrão
- `dark` - Tema escuro
- `minimal` - Tema minimalista
- `colorful` - Tema colorido

## Componentes Principais

### Header
- Logo da empresa
- Menu de navegação
- Busca com autocomplete
- Carrinho com contador
- Menu do usuário

### Footer
- Informações da empresa
- Links úteis
- Redes sociais
- Newsletter
- Links legais

### Produto Card
- Imagem do produto
- Nome e descrição
- Preço e desconto
- Avaliações
- Botões de ação

## Melhorias de UX

### Acessibilidade
- ARIA labels
- Navegação por teclado
- Contraste adequado
- Textos alternativos

### Performance
- Lazy loading
- CSS e JS otimizados
- Imagens responsivas
- Cache de recursos

### Responsividade
- Design mobile-first
- Breakpoints definidos
- Componentes adaptáveis
- Touch-friendly

## Estrutura de Dados

### Empresa
```php
[
    'nome_fantasia' => 'Loja Exemplo',
    'cor_primaria' => '#007bff',
    'cor_secundaria' => '#6c757d',
    'cor_destaque' => '#28a745',
    'logo' => '/assets/images/logo.png',
    'descricao' => 'Sua loja online de confiança'
]
```

### Produto
```php
[
    'id' => 1,
    'nome' => 'Smartphone Galaxy S21',
    'preco' => 2999.99,
    'preco_antigo' => 3499.99,
    'imagem' => '/assets/images/produtos/smartphone-1.jpg',
    'categoria' => ['id' => 1, 'nome' => 'Eletrônicos'],
    'avaliacao' => 4.5,
    'estoque' => 15,
    'promocao' => '15% OFF'
]
```

## Próximos Passos

1. **Implementar Controllers**
   - Criar controllers para todas as rotas
   - Implementar lógica de negócio
   - Conectar com banco de dados

2. **Criar Páginas Adicionais**
   - Página de produtos
   - Página de categoria
   - Página de produto individual
   - Páginas de autenticação

3. **Sistema de Pagamento**
   - Integração com gateways
   - Processamento de pedidos
   - Webhooks de pagamento

4. **Administração**
   - Painel administrativo
   - Gestão de produtos
   - Relatórios de vendas

## Tecnologias Utilizadas

- **PHP 8+** - Backend
- **Twig** - Template engine
- **JavaScript ES6+** - Frontend
- **CSS3** - Estilos
- **HTML5** - Estrutura
- **cURL** - APIs externas
- **Regex** - Validações

## Benefícios da Nova Estrutura

1. **Manutenibilidade** - Código organizado e modular
2. **Escalabilidade** - Fácil adição de novas funcionalidades
3. **Performance** - Otimizações implementadas
4. **UX** - Interface moderna e responsiva
5. **Acessibilidade** - Padrões WCAG seguidos
6. **Flexibilidade** - Sistema de temas personalizável 