# 🏗️ Arquitetura do Sistema de Vendas Multiempresa

## 📋 Visão Geral

Este sistema foi desenvolvido como uma solução SaaS (Software as a Service) para gestão de vendas, permitindo que múltiplas empresas utilizem a mesma plataforma de forma isolada e segura.

## 🎯 Níveis de Acesso

### 1. **Loja (Cliente Final)**
- **Acesso**: Público ou via login
- **Usuários**: Clientes que compram produtos
- **Origem**: Tabela `pessoas`
- **Funcionalidades**:
  - Visualização de produtos
  - Carrinho de compras
  - Checkout
  - Histórico de pedidos
  - Perfil do cliente

### 2. **Admin (Painel de Gestão da Empresa)**
- **Acesso**: Login obrigatório
- **Usuários**: Administradores da empresa
- **Origem**: Tabela `usuarios` (tipo: admin_empresa)
- **Funcionalidades**:
  - Dashboard com estatísticas
  - Gestão de produtos e categorias
  - Controle de vendas e estoque
  - Cadastro de clientes
  - Relatórios da empresa
  - Configurações da empresa

### 3. **Painel (Admin Geral)**
- **Acesso**: Login obrigatório
- **Usuários**: Administradores do sistema
- **Origem**: Tabela `usuarios` (tipo: admin_geral)
- **Funcionalidades**:
  - Gestão de empresas
  - Controle de usuários do sistema
  - Relatórios gerais
  - Configurações do sistema

## 🗄️ Modelagem do Banco de Dados

### Tabelas Principais

#### `empresas`
```sql
- id (PK)
- nome_fantasia
- razao_social
- cnpj (unique)
- email (unique)
- telefone
- endereco
- cidade
- estado
- cep
- logo
- cor_primaria
- cor_secundaria
- ativo (boolean)
- data_cadastro
- data_atualizacao
```

#### `usuarios`
```sql
- id (PK)
- empresa_id (FK, nullable)
- nome
- email (unique)
- senha (hash)
- tipo (admin_empresa, admin_geral)
- status (ativo, inativo, pendente)
- ultimo_acesso
- data_cadastro
- data_atualizacao
```

#### `pessoas`
```sql
- id (PK)
- empresa_id (FK)
- nome
- email
- cpf_cnpj
- telefone
- celular
- endereco
- cidade
- estado
- cep
- tipo (fisica, juridica)
- ativo (boolean)
- data_cadastro
- data_atualizacao
```

#### `categorias`
```sql
- id (PK)
- empresa_id (FK)
- nome
- descricao
- imagem
- ativo (boolean)
- ordem
- data_cadastro
- data_atualizacao
```

#### `produtos`
```sql
- id (PK)
- empresa_id (FK)
- categoria_id (FK)
- codigo (unique)
- nome
- descricao
- preco
- preco_promocional
- estoque
- estoque_minimo
- imagem
- ativo (boolean)
- destaque (boolean)
- peso, altura, largura, comprimento
- data_cadastro
- data_atualizacao
```

#### `vendas`
```sql
- id (PK)
- empresa_id (FK)
- pessoa_id (FK, nullable)
- usuario_id (FK, nullable)
- numero_pedido (unique)
- status (pendente, aprovado, em_preparo, enviado, entregue, cancelado)
- forma_pagamento
- subtotal
- desconto
- frete
- total
- observacoes
- endereco_entrega
- cidade_entrega
- estado_entrega
- cep_entrega
- data_venda
- data_atualizacao
```

#### `venda_itens`
```sql
- id (PK)
- venda_id (FK)
- produto_id (FK)
- quantidade
- preco_unitario
- desconto
- total
- observacoes
```

## 🏛️ Arquitetura de Controllers

### Organização Modular

```
app/Controllers/
├── Loja/           # Controllers da loja (clientes)
│   ├── HomeController.php
│   ├── CarrinhoController.php
│   ├── CheckoutController.php
│   └── PerfilController.php
├── Admin/          # Controllers do painel admin
│   ├── DashboardController.php
│   ├── ProdutosController.php
│   ├── VendasController.php
│   ├── PessoasController.php
│   └── RelatoriosController.php
├── Painel/         # Controllers do painel geral
│   ├── DashboardController.php
│   ├── EmpresasController.php
│   ├── UsuariosController.php
│   └── RelatoriosController.php
└── Auth/           # Controllers de autenticação
    └── AuthController.php
```

## 🔐 Sistema de Autenticação

### Guards (Provedores de Autenticação)

1. **loja**: Para clientes da loja
2. **admin**: Para administradores da empresa
3. **painel**: Para administradores do sistema

### Middleware de Autenticação

```php
// Verifica se o usuário está logado
Auth::check($guard)

// Obtém o usuário logado
Auth::user($guard)

// Faz login
Auth::login($user, $guard)

// Faz logout
Auth::logout($guard)
```

## 🛣️ Estrutura de Rotas

### Organização por Módulos

```
routes/
├── loja.php      # Rotas da loja (clientes)
├── admin.php     # Rotas do painel admin
└── painel.php    # Rotas do painel geral
```

### Exemplos de Rotas

#### Loja
```php
GET  /                    # Home da loja
GET  /produtos           # Lista de produtos
GET  /produto/{id}       # Detalhes do produto
GET  /carrinho           # Carrinho de compras
POST /carrinho/adicionar # Adicionar ao carrinho
GET  /checkout           # Finalizar compra
```

#### Admin
```php
GET  /admin/dashboard    # Dashboard da empresa
GET  /admin/produtos     # Gestão de produtos
GET  /admin/vendas       # Gestão de vendas
GET  /admin/pessoas      # Gestão de clientes
GET  /admin/relatorios   # Relatórios da empresa
```

#### Painel
```php
GET  /painel/dashboard   # Dashboard geral
GET  /painel/empresas    # Gestão de empresas
GET  /painel/usuarios    # Gestão de usuários
GET  /painel/relatorios  # Relatórios gerais
```

## 🔒 Segurança e Boas Práticas

### 1. **Isolamento de Dados**
- Todas as consultas filtram por `empresa_id`
- Middleware verifica permissões por empresa
- Sessões independentes para cada guard

### 2. **Validação de Dados**
```php
$this->validate($dados, [
    'nome' => 'required|max:100',
    'email' => 'required|email|unique:usuarios,email',
    'senha' => 'required|min:6|confirmed'
]);
```

### 3. **Hash de Senhas**
```php
// Criptografar senha
$usuario->setSenha($senha);

// Verificar senha
$usuario->verificarSenha($senha);
```

### 4. **Proteção CSRF**
- Tokens CSRF em todos os formulários
- Middleware de verificação automática

### 5. **Rate Limiting**
- Limitação de tentativas de login
- Proteção contra ataques de força bruta

## 📊 Relatórios e Estatísticas

### Dashboard Admin (Empresa)
- Vendas do dia/mês
- Receita total
- Produtos em baixo estoque
- Vendas recentes
- Produtos mais vendidos

### Dashboard Painel (Geral)
- Total de empresas
- Total de usuários
- Vendas gerais do sistema
- Empresas mais ativas
- Relatórios financeiros

## 🚀 Funcionalidades Principais

### Loja
- ✅ Catálogo de produtos
- ✅ Carrinho de compras
- ✅ Checkout seguro
- ✅ Histórico de pedidos
- ✅ Perfil do cliente
- ✅ Busca e filtros
- ✅ Responsivo (mobile-first)

### Admin
- ✅ Dashboard com estatísticas
- ✅ Gestão completa de produtos
- ✅ Controle de vendas
- ✅ Gestão de clientes
- ✅ Controle de estoque
- ✅ Relatórios detalhados
- ✅ Configurações da empresa

### Painel
- ✅ Gestão de empresas
- ✅ Controle de usuários
- ✅ Relatórios gerais
- ✅ Backup do sistema
- ✅ Logs de auditoria
- ✅ Configurações globais

## 🔧 Configurações

### Arquivo `config/routes.php`
- Configurações de autenticação
- Middlewares padrão
- Configurações de sessão
- Configurações de upload
- Configurações de paginação

### Variáveis de Ambiente
```env
DB_HOST=localhost
DB_NAME=framephp
DB_USER=root
DB_PASS=

APP_URL=http://localhost
APP_NAME="Sistema de Vendas"

MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
```

## 📝 Próximos Passos

1. **Implementar Views Twig**
   - Templates para cada módulo
   - Layouts responsivos
   - Componentes reutilizáveis

2. **Sistema de Pagamentos**
   - Integração com gateways
   - Múltiplas formas de pagamento
   - Notificações automáticas

3. **Sistema de Notificações**
   - Email automático
   - SMS para status de pedidos
   - Push notifications

4. **API REST**
   - Endpoints para integração
   - Documentação Swagger
   - Autenticação via tokens

5. **Testes Automatizados**
   - Testes unitários
   - Testes de integração
   - Testes de interface

## 🎨 Interface e UX

### Design System
- Cores personalizáveis por empresa
- Componentes consistentes
- Responsivo para todos os dispositivos
- Acessibilidade (WCAG 2.1)

### Performance
- Cache de consultas
- Otimização de imagens
- Lazy loading
- CDN para assets

---

**Desenvolvido com ❤️ usando FramePHP** 