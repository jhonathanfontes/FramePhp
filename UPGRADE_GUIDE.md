# Guia de Atualização do FramePhp

## 🚀 Atualizações Implementadas Baseadas no SpeedPHP

Este documento descreve as principais melhorias implementadas no FramePhp baseadas na análise do SpeedPHP.

## 📋 Resumo das Atualizações

### 1. **Dependências Atualizadas**
- **PHP**: Atualizado para versão 8.2+
- **Twig**: Atualizado para versão 3.20.0
- **Novas dependências adicionadas**:
  - `firebase/php-jwt` - Autenticação JWT
  - `google/recaptcha` - Proteção reCAPTCHA
  - `performing/twig-components` - Componentes Twig
  - `ramsey/uuid` - Geração de UUIDs
  - `phpoffice/phpspreadsheet` - Manipulação de planilhas

### 2. **Sistema de Alertas Avançado** (`AlertManager`)
- ✅ Alertas com diferentes tipos: `danger`, `warning`, `success`, `info`
- ✅ Sistema de sessão para persistência de alertas
- ✅ Alertas inline e toast
- ✅ Integração automática com templates Twig

### 3. **Sistema de Permissões** (`PermissionManager`)
- ✅ Controle granular de permissões
- ✅ Sistema de papéis (roles)
- ✅ Verificação de múltiplas permissões
- ✅ Redirecionamento automático para acesso negado

### 4. **Validação HTTP** (`HttpValidator`)
- ✅ Validação de métodos HTTP (GET, POST, PUT, DELETE, PATCH)
- ✅ Validação de requisições AJAX e API
- ✅ Proteção CSRF integrada
- ✅ Validação de reCAPTCHA
- ✅ Headers de segurança automáticos

### 5. **BaseController Aprimorado**
- ✅ Integração com todos os novos sistemas
- ✅ Métodos de redirecionamento com mensagens
- ✅ Validação automática de métodos HTTP
- ✅ Sistema de permissões integrado
- ✅ Respostas JSON padronizadas
- ✅ Debug apenas em modo desenvolvimento

### 6. **Sistema de Cache Avançado** (`CacheManager`)
- ✅ Cache em arquivo com subdiretórios
- ✅ TTL configurável
- ✅ Métodos `remember` e `rememberAsync`
- ✅ Incremento/decremento de valores
- ✅ Estatísticas e informações do cache
- ✅ Limpeza por padrões

### 7. **Configuração Avançada**
- ✅ Arquivo de configuração estruturado
- ✅ Configurações de segurança
- ✅ Configurações de cache e sessão
- ✅ Configurações de email e banco de dados
- ✅ Configurações de API e JWT

## 🔧 Como Usar as Novas Funcionalidades

### Sistema de Alertas

```php
class MeuController extends BaseController
{
    public function salvar()
    {
        try {
            // Lógica de salvamento
            $this->redirectSuccess('/lista', 'Registro salvo com sucesso!');
        } catch (Exception $e) {
            $this->redirectError('/form', 'Erro ao salvar: ' . $e->getMessage());
        }
    }
}
```

### Sistema de Permissões

```php
class AdminController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        
        // Define permissões do usuário
        $this->permissionManager->setPermissions([
            'LER' => 1,
            'SALVAR' => 1,
            'ALTERAR' => 1,
            'EXCLUIR' => 0
        ]);
        
        $this->permissionManager->setUserRoles(['admin']);
    }
    
    public function excluir()
    {
        // Verifica permissão antes de executar
        if (!$this->requirePermission('EXCLUIR')) {
            return; // Redirecionamento automático
        }
        
        // Lógica de exclusão
    }
}
```

### Validação HTTP

```php
class ApiController extends BaseController
{
    public function criar()
    {
        // Valida se é POST
        if (!$this->requirePost(true)) {
            return; // Erro 405 automático
        }
        
        // Valida se é API
        if (!$this->httpValidator->requireApi()) {
            return; // Erro 400 automático
        }
        
        $data = $this->jsonParams();
        
        // Resposta JSON
        $this->jsonSuccess('Registro criado', $data);
    }
}
```

### Sistema de Cache

```php
class ProdutoController extends BaseController
{
    public function listar()
    {
        $cache = CacheManager::getInstance();
        
        $produtos = $cache->remember('produtos_lista', function() {
            // Lógica para buscar produtos do banco
            return $this->produtoModel->todos();
        }, 3600); // Cache por 1 hora
        
        return $this->render('produtos/lista', ['produtos' => $produtos]);
    }
}
```

## 📁 Estrutura de Arquivos Atualizada

```
FramePhp/
├── core/
│   ├── Lib/
│   │   ├── AlertManager.php          # ✅ NOVO
│   │   ├── PermissionManager.php     # ✅ NOVO
│   │   └── HttpValidator.php         # ✅ NOVO
│   ├── Cache/
│   │   └── CacheManager.php          # ✅ ATUALIZADO
│   └── Controller/
│       └── BaseController.php        # ✅ ATUALIZADO
├── config/
│   └── app.php                       # ✅ ATUALIZADO
└── composer.json                      # ✅ ATUALIZADO
```

## 🚀 Próximos Passos

### 1. **Instalar Dependências**
```bash
composer update
```

### 2. **Configurar Ambiente**
- Copie `.env.example` para `.env`
- Configure as variáveis de ambiente
- Gere uma nova chave da aplicação

### 3. **Atualizar Controllers Existentes**
- Herde de `BaseController` em vez de `Controller`
- Use os novos métodos de alerta e permissões
- Implemente validação HTTP onde necessário

### 4. **Configurar Sistema de Permissões**
- Implemente lógica de permissões no banco de dados
- Configure permissões por usuário/role
- Use o `PermissionManager` nos controllers

### 5. **Implementar Sistema de Cache**
- Configure o cache para dados frequentemente acessados
- Use o método `remember` para queries complexas
- Monitore estatísticas do cache

## 🔒 Segurança

- ✅ Headers de segurança automáticos
- ✅ Proteção CSRF integrada
- ✅ Validação de métodos HTTP
- ✅ Sistema de permissões granular
- ✅ Validação de reCAPTCHA

## 📊 Performance

- ✅ Sistema de cache avançado
- ✅ Lazy loading de dependências
- ✅ Headers de cache configuráveis
- ✅ Estatísticas de uso de memória

## 🧪 Desenvolvimento

- ✅ Debug apenas em modo desenvolvimento
- ✅ Sistema de logs estruturado
- ✅ Validação de ambiente
- ✅ Headers de desenvolvimento

## 📞 Suporte

Para dúvidas ou problemas com as atualizações:

1. Verifique os logs de erro
2. Consulte a documentação das novas dependências
3. Teste em ambiente de desenvolvimento primeiro
4. Verifique a compatibilidade com PHP 8.2+

---

**FramePhp** - Framework PHP simples e poderoso com funcionalidades avançadas! 🚀
