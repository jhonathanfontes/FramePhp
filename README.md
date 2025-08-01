
# Sistema Multi-Empresas - FramePhp

Framework PHP moderno para sistema de vendas online multi-empresas com arquitetura MVC, suporte a Twig, autenticação JWT, middleware avançado e geração de relatórios em PDF.

## 📋 Índice

- [Características](#características)
- [Arquitetura](#arquitetura)
- [Requisitos](#requisitos)
- [Instalação](#instalação)
- [Configuração](#configuração)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Funcionalidades](#funcionalidades)
- [API Reference](#api-reference)
- [Middleware](#middleware)
- [Banco de Dados](#banco-de-dados)
- [Relatórios](#relatórios)
- [Testes](#testes)
- [Deployment](#deployment)
- [Contribuição](#contribuição)

## 🚀 Características

### Core Framework
- **Arquitetura MVC** - Separação clara de responsabilidades
- **Sistema de Roteamento** - Rotas organizadas por módulos
- **ORM Integrado** - Manipulação simplificada do banco de dados
- **Template Engine** - Twig para renderização de views
- **Middleware Pipeline** - Controle de acesso e validações
- **Sistema de Autenticação** - JWT e sessões
- **Validação de Dados** - Validador robusto e flexível
- **Sistema de Cache** - Cache inteligente para performance
- **Tratamento de Erros** - Error handling centralizado
- **Internacionalização** - Suporte multi-idioma (PT-BR/EN)

### Funcionalidades do Sistema
- **Multi-Empresas** - Gestão de múltiplas empresas
- **E-commerce** - Sistema completo de vendas online
- **Gestão de Produtos** - Catálogo com categorias e fabricantes
- **Sistema de Pedidos** - Carrinho e processamento de pedidos
- **Relatórios em PDF** - Geração automatizada de relatórios
- **API RESTful** - Endpoints para integração
- **Painel Administrativo** - Interface completa de gestão
- **Sistema de Permissões** - Controle granular de acesso

## 🏗️ Arquitetura

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Presentation  │    │    Business     │    │      Data       │
│     Layer       │    │     Layer       │    │     Layer       │
├─────────────────┤    ├─────────────────┤    ├─────────────────┤
│ Controllers     │◄──►│ Services        │◄──►│ Models          │
│ Views (Twig)    │    │ Middleware      │    │ Database        │
│ Routes          │    │ Validation      │    │ Migrations      │
│ Assets          │    │ Auth/JWT        │    │ Connections     │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

## 📋 Requisitos

- **PHP 8.1+** - Linguagem principal
- **Composer** - Gerenciador de dependências
- **MySQL 5.7+** - Banco de dados
- **Apache/Nginx** - Servidor web (opcional, usa servidor embutido do PHP)

## 🛠️ Instalação

### 1. Clone o repositório
```bash
git clone https://github.com/seu-usuario/sistema-multi-empresas.git
cd sistema-multi-empresas
```

### 2. Instale as dependências
```bash
composer install
```

### 3. Configure o ambiente
```bash
cp .env.example .env
# Edite o arquivo .env com suas configurações
```

### 4. Execute as migrações
```bash
php artisan migrate
```

### 5. Inicie o servidor
```bash
php -S 0.0.0.0:5000 -t public
```

## ⚙️ Configuração

### Arquivo .env
```env
# Aplicação
APP_NAME=Sistema Multi-Empresas
APP_VERSION=1.0.0
APP_DEBUG=true
APP_URL=http://localhost:5000
APP_TIMEZONE=America/Sao_Paulo
APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=en

# Banco de Dados
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=sistema_multiempresas
DB_USERNAME=root
DB_PASSWORD=

# Email
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@sistema.com
MAIL_FROM_NAME="Sistema Multi-Empresas"

# Segurança
APP_KEY=sua_chave_secreta_aqui
JWT_SECRET=sua_chave_jwt_aqui
```

## 📁 Estrutura do Projeto

```
├── app/
│   ├── Controllers/          # Controladores organizados por módulo
│   │   ├── Admin/           # Painel administrativo
│   │   ├── Api/             # Endpoints da API
│   │   ├── Auth/            # Autenticação
│   │   ├── Site/            # Site público
│   │   └── Store/           # Loja virtual
│   ├── Models/              # Modelos de dados
│   ├── Services/            # Serviços da aplicação
│   ├── Middleware/          # Middleware customizado
│   ├── Policies/            # Políticas de autorização
│   └── Views/               # Templates Twig
├── core/                    # Núcleo do framework
│   ├── Database/            # Abstração do banco
│   ├── Http/                # Request/Response
│   ├── Router/              # Sistema de rotas
│   ├── Security/            # JWT e segurança
│   ├── View/                # Gerenciador Twig
│   └── ...
├── config/                  # Configurações
├── database/                # Migrações e seeds
├── public/                  # Ponto de entrada
├── resources/               # Recursos (traduções)
├── routes/                  # Definições de rotas
└── tests/                   # Testes automatizados
```

## 🎯 Funcionalidades

### 1. Sistema Multi-Empresas
- Cadastro e gestão de empresas
- Configuração de lojas por empresa
- Isolamento de dados por empresa
- Middleware de contexto empresarial

### 2. E-commerce
- Catálogo de produtos com categorias
- Sistema de carrinho de compras
- Processamento de pedidos
- Gestão de estoque
- Cálculo de frete e impostos

### 3. Painel Administrativo
- Dashboard com métricas
- Gestão de usuários e permissões
- Relatórios financeiros
- Configurações do sistema

### 4. API RESTful
```
GET    /api/produtos          # Listar produtos
POST   /api/produtos          # Criar produto
GET    /api/produtos/{id}     # Obter produto
PUT    /api/produtos/{id}     # Atualizar produto
DELETE /api/produtos/{id}     # Deletar produto

GET    /api/pedidos           # Listar pedidos
POST   /api/pedidos           # Criar pedido
GET    /api/pedidos/{id}      # Obter pedido
```

## 🔒 Middleware

### Middleware Disponível
- **AuthenticationMiddleware** - Verificação de autenticação
- **EmpresaMiddleware** - Contexto de empresa
- **ApiRateLimitMiddleware** - Limitação de requisições
- **CorsMiddleware** - CORS para APIs
- **CsrfMiddleware** - Proteção CSRF
- **JWTAuthMiddleware** - Autenticação JWT
- **LocaleMiddleware** - Internacionalização
- **SecurityMiddleware** - Headers de segurança

### Configuração de Middleware
```php
// config/middleware.php
return [
    'web' => [
        'security',
        'session',
        'csrf',
        'locale'
    ],
    'api' => [
        'cors',
        'rate_limit',
        'jwt_auth'
    ],
    'admin' => [
        'auth',
        'empresa_context'
    ]
];
```

## 🗄️ Banco de Dados

### Principais Tabelas
- **empresas** - Dados das empresas
- **lojas** - Lojas por empresa
- **usuarios** - Usuários do sistema
- **produtos** - Catálogo de produtos
- **categorias** - Categorias de produtos
- **pedidos** - Pedidos realizados
- **pedido_itens** - Itens dos pedidos

### Migrações
```bash
# Executar migrações
php artisan migrate

# Reverter migrações
php artisan migrate:rollback

# Executar seeds
php artisan seed
```

## 📊 Relatórios

### Tipos de Relatórios
- **Vendas** - Relatório de vendas por período
- **Produtos** - Relatório de produtos mais vendidos
- **Financeiro** - Relatório de receitas e despesas
- **Empresas** - Relatório de empresas cadastradas

### Geração de PDF
```php
// Exemplo de uso do PdfService
$pdfService = new PdfService();
$pdfContent = $pdfService->createReport(
    'Relatório de Vendas',
    $dadosVendas,
    'landscape' // portrait ou landscape
);

// Salvar ou retornar PDF
file_put_contents('relatorio.pdf', $pdfContent);
```

## 🧪 Testes

### Executar Testes
```bash
# Todos os testes
vendor/bin/phpunit

# Testes específicos
vendor/bin/phpunit tests/Unit/MailTest.php

# Com coverage
vendor/bin/phpunit --coverage-html coverage/
```

### Estrutura de Testes
```
tests/
├── Unit/                    # Testes unitários
│   ├── Mail/
│   └── Message/
├── ExampleTest.php
└── RouterTest.php
```

## 🚀 Deployment

### No Replit
1. Configure as variáveis de ambiente
2. Execute as migrações
3. O sistema está pronto na porta 5000

### Configuração de Produção
```bash
# .env para produção
APP_DEBUG=false
APP_URL=https://seu-dominio.com

# Otimizações
composer install --no-dev --optimize-autoloader
```

## 📚 Rotas Principais

### Web (Site Público)
```
GET  /                       # Página inicial
GET  /login                  # Login
POST /login                  # Processar login
GET  /shop                   # Loja
GET  /shop/produto/{id}      # Detalhes do produto
POST /cart/add               # Adicionar ao carrinho
```

### Admin (Painel Administrativo)
```
GET  /admin                  # Dashboard
GET  /admin/empresas         # Gestão de empresas
GET  /admin/reports          # Relatórios
GET  /admin/reports/vendas   # Relatório de vendas
```

### API
```
POST /api/auth/login         # Login API
GET  /api/produtos           # Listar produtos
POST /api/pedidos            # Criar pedido
```

## 🛡️ Segurança

### Medidas Implementadas
- **Autenticação JWT** - Tokens seguros
- **Proteção CSRF** - Tokens CSRF
- **Rate Limiting** - Limitação de requisições
- **Headers de Segurança** - CSP, HSTS, etc.
- **Sanitização** - Validação e sanitização de dados
- **Hash de Senhas** - Bcrypt para senhas

## 📖 Tradução

### Idiomas Suportados
- Português Brasileiro (pt_BR)
- Inglês (en)

### Uso
```php
// No controller
$message = $this->translate('messages.welcome');

// No template Twig
{{ translate('messages.welcome') }}
```

## 🤝 Contribuição

1. **Fork** o projeto
2. **Crie** um branch: `git checkout -b feature/nova-funcionalidade`
3. **Commit** suas mudanças: `git commit -am 'feat: Nova funcionalidade'`
4. **Push** para o branch: `git push origin feature/nova-funcionalidade`
5. **Abra** um Pull Request

### Padrões de Código
- PSR-4 para autoloading
- PSR-12 para estilo de código
- Documentação em português
- Testes para novas funcionalidades

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 📞 Suporte

- **Documentação**: [Wiki do projeto](https://github.com/seu-usuario/sistema-multi-empresas/wiki)
- **Issues**: [GitHub Issues](https://github.com/seu-usuario/sistema-multi-empresas/issues)
- **Email**: suporte@sistema.com

---

**Desenvolvido com ❤️ usando FramePhp**
