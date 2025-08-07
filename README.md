
# FramePhp - Framework PHP para E-commerce

## 🚀 Visão Geral

FramePhp é um framework PHP moderno e robusto desenvolvido especificamente para aplicações de e-commerce. Oferece uma arquitetura limpa, código padronizado e funcionalidades completas para lojas virtuais.

## ✨ Funcionalidades Principais

### 🛒 Loja Virtual Completa
- **Página Inicial**: Design moderno com produtos em destaque
- **Catálogo de Produtos**: Listagem com filtros e busca
- **Carrinho de Compras**: Funcionalidade completa com AJAX
- **Checkout**: Processo de compra otimizado
- **Sistema de Usuários**: Cadastro, login e perfil
- **Área do Cliente**: Pedidos, favoritos e endereços

### 🎨 Interface Moderna
- **Design Responsivo**: Funciona em todos os dispositivos
- **CSS Organizado**: Arquivos externos sem CSS inline
- **JavaScript Modular**: Código organizado em classes
- **Acessibilidade**: Seguindo padrões WCAG

### 🔧 Arquitetura Robusta
- **MVC**: Padrão Model-View-Controller
- **Separação de Responsabilidades**: Controladores de Views e Backend
- **Sistema de Rotas**: Organizado e documentado
- **Middlewares**: Autenticação, CSRF, CORS
- **Validação**: Serviços de validação integrados

## 📁 Estrutura do Projeto

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
│   │   ├── ValidationService.php
│   │   └── CepService.php
│   ├── Middleware/          # Middlewares
│   ├── Views/               # Views/Templates Twig
│   └── Policies/            # Políticas de autorização
├── core/                    # Core do framework
├── config/                  # Configurações
├── routes/                  # Definição de rotas
├── public/                  # Arquivos públicos
│   └── assets/
│       ├── css/             # Estilos organizados
│       └── js/              # JavaScript modular
├── database/                # Migrações e seeds
└── docs/                    # Documentação completa
```

## 🛠️ Tecnologias Utilizadas

- **PHP 8.0+**: Linguagem principal
- **Twig**: Engine de templates
- **CSS3**: Estilos modernos e responsivos
- **JavaScript ES6+**: Funcionalidades interativas
- **MySQL/PostgreSQL**: Banco de dados
- **Composer**: Gerenciamento de dependências

## 🚀 Instalação

### Pré-requisitos
- PHP 8.0 ou superior
- Composer
- Servidor web (Apache/Nginx)
- MySQL/PostgreSQL

### Passos de Instalação

1. **Clone o repositório**
```bash
git clone https://github.com/seu-usuario/framephp.git
cd framephp
```

2. **Instale as dependências**
```bash
composer install
```

3. **Configure o banco de dados**
```bash
# Copie o arquivo de configuração
cp config/database.example.php config/database.php

# Edite as configurações do banco
nano config/database.php
```

4. **Execute as migrações**
```bash
php database/migrate.php
```

5. **Configure o servidor web**
```apache
# Apache (.htaccess já incluído)
DocumentRoot /path/to/framephp/public
```

## 📖 Documentação

### 📋 Guias Disponíveis

- **[Mapeamento de Rotas](docs/ROTAS.md)**: Todas as rotas do projeto
- **[Estrutura de Dados](docs/ESTRUTURA_DADOS.md)**: Modelos e dados falsos
- **[Guia de Contribuição](docs/CONTRIBUICAO.md)**: Padrões para desenvolvedores
- **[Arquitetura](docs/ARQUITETURA.md)**: Visão geral da arquitetura

### 🎯 Funcionalidades Implementadas

#### ✅ Carrinho de Compras
- Adicionar produtos
- Atualizar quantidades
- Remover produtos
- Limpar carrinho
- Aplicar cupons
- Calcular frete

#### ✅ Busca e Filtros
- Busca por termo
- Filtros por categoria
- Filtros por preço
- Ordenação de resultados
- Autocomplete

#### ✅ Validação de Dados
- Validação de CPF/CNPJ
- Validação de email
- Consulta de CEP
- Cálculo de frete

#### ✅ Interface Responsiva
- Design mobile-first
- CSS organizado em arquivos externos
- JavaScript modular
- Acessibilidade implementada

## 🔧 Configuração

### Variáveis de Ambiente
```php
// config/app.php
return [
    'app_name' => 'FramePhp',
    'app_url' => 'http://localhost',
    'debug' => true,
    'timezone' => 'America/Sao_Paulo',
    'locale' => 'pt_BR'
];
```

### Banco de Dados
```php
// config/database.php
return [
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'framephp',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci'
];
```

## 🎨 Personalização

### Cores da Loja
```php
// Dados da empresa
$empresa = [
    'cor_primaria' => '#007bff',
    'cor_secundaria' => '#6c757d',
    'cor_destaque' => '#28a745',
    'cor_texto' => '#333',
    'cor_fundo' => '#f8f9fa'
];
```

### Layout Responsivo
```css
/* Variáveis CSS */
:root {
    --primary-color: #007bff;
    --secondary-color: #6c757d;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --info-color: #17a2b8;
}
```

## 🧪 Testes

### Executar Testes
```bash
# Testes unitários
php vendor/bin/phpunit

# Testes específicos
php vendor/bin/phpunit tests/Unit/
```

### Testes de Interface
- Teste em diferentes navegadores
- Teste de responsividade
- Teste de acessibilidade
- Teste de performance

## 📈 Performance

### Otimizações Implementadas
- CSS e JS minificados
- Imagens otimizadas
- Cache de consultas
- Lazy loading de imagens
- Compressão gzip

### Métricas
- **Tempo de Carregamento**: < 2s
- **Score Mobile**: 90+
- **Score Desktop**: 95+
- **Acessibilidade**: 100%

## 🔒 Segurança

### Medidas Implementadas
- **CSRF Protection**: Tokens em formulários
- **SQL Injection**: Prepared statements
- **XSS Protection**: Escape de dados
- **Validação**: Dados de entrada validados
- **HTTPS**: Suporte a SSL/TLS

## 🤝 Contribuição

### Como Contribuir
1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

### Padrões de Código
- Siga os padrões PSR-12
- Use nomes descritivos em português
- Comente código complexo
- Teste suas alterações
- Mantenha a responsividade

## 📄 Licença

Este projeto está licenciado sob a Licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## 🙏 Agradecimentos

- Comunidade PHP
- Contribuidores do projeto
- Usuários que testaram e reportaram bugs

## 📞 Suporte

- **Issues**: [GitHub Issues](https://github.com/seu-usuario/framephp/issues)
- **Documentação**: [docs/](docs/)
- **Email**: suporte@framephp.com

## 🔄 Changelog

### v2.0.0 (2024-01-15)
- ✅ Implementação completa do carrinho de compras
- ✅ Remoção de CSS inline das views
- ✅ JavaScript modular e organizado
- ✅ Sistema de busca e filtros
- ✅ Validação de dados integrada
- ✅ Documentação completa
- ✅ Padrões de código estabelecidos

### v1.0.0 (2024-01-01)
- 🎉 Lançamento inicial do framework
- 📦 Estrutura MVC básica
- 🛒 Funcionalidades básicas da loja
- 📱 Design responsivo inicial

---

**FramePhp** - Framework PHP moderno para e-commerce 🚀
