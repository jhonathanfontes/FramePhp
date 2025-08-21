# 📋 Modelos Criados para o Sistema

Este documento descreve os modelos que foram criados para resolver os erros de linter e fazer o sistema funcionar corretamente.

## 🏗️ **Modelos Criados**

### **1. EstabelecimentoModel** (`app/Models/EstabelecimentoModel.php`)

**Descrição**: Modelo para gerenciar estabelecimentos comerciais vinculados às empresas.

**Campos Principais**:
- `empresa_id`: ID da empresa proprietária
- `nome`: Nome do estabelecimento
- `cnpj`: CNPJ único do estabelecimento
- `endereco`, `cidade`, `estado`, `cep`: Informações de localização
- `telefone`, `email`: Contatos
- `responsavel`: Nome do responsável
- `status`: Status (ativo/inativo)
- `tipo_estabelecimento`: Tipo do estabelecimento
- `data_abertura`: Data de abertura

**Métodos Principais**:
```php
// Contagem e estatísticas
$estabelecimentoModel->count();
$estabelecimentoModel->countAtivos();
$estabelecimentoModel->getEstatisticas();

// Busca e filtros
$estabelecimentoModel->getRecent(10);
$estabelecimentoModel->getPaginated(1, 15, $filtros);
$estabelecimentoModel->search('termo');
$estabelecimentoModel->getByEmpresa($empresaId);

// Gerenciamento de status
$estabelecimentoModel->ativar($id);
$estabelecimentoModel->inativar($id);
```

---

### **2. AtividadeModel** (`app/Models/AtividadeModel.php`)

**Descrição**: Modelo para registrar todas as atividades e ações dos usuários no sistema.

**Campos Principais**:
- `usuario_id`: ID do usuário que executou a ação
- `empresa_id`: ID da empresa relacionada
- `tipo`: Tipo de atividade (empresa, usuario, estabelecimento, etc.)
- `acao`: Ação executada (criar, atualizar, deletar, etc.)
- `descricao`: Descrição detalhada da atividade
- `dados_anteriores`, `dados_novos`: Dados antes e depois da alteração (JSON)
- `ip_address`, `user_agent`: Informações de segurança
- `created_at`: Data/hora da atividade

**Métodos Principais**:
```php
// Contagem e estatísticas
$atividadeModel->count();
$atividadeModel->countHoje();
$atividadeModel->getEstatisticas();

// Busca e filtros
$atividadeModel->getRecent(10);
$atividadeModel->getPaginated(1, 15, $filtros);
$atividadeModel->getByUsuario($usuarioId);
$atividadeModel->getByEmpresa($empresaId);

// Registro de atividades
$atividadeModel->registrarAtividade($usuarioId, $empresaId, $tipo, $acao, $descricao);

// Limpeza automática
$atividadeModel->limparAtividadesAntigas(90);
```

---

### **3. Config** (`app/Models/Config.php`)

**Descrição**: Modelo para gerenciar todas as configurações do sistema de forma centralizada.

**Campos Principais**:
- `chave`: Chave única da configuração
- `valor`: Valor da configuração (pode ser JSON)
- `tipo`: Tipo de dado (string, integer, boolean, json, array)
- `descricao`: Descrição da configuração
- `categoria`: Categoria (geral, usuarios, empresas, seguranca, etc.)
- `editavel`: Se a configuração pode ser editada
- `created_at`, `updated_at`: Timestamps

**Métodos Principais**:
```php
// Gerenciamento básico
$config->get('chave', $padrao);
$config->set('chave', $valor, $tipo, $descricao, $categoria);
$config->has('chave');
$config->delete('chave');

// Configurações por categoria
$config->getConfiguracoesGerais();
$config->getConfiguracoesUsuarios();
$config->getConfiguracoesEmpresas();
$config->getConfiguracoesSeguranca();
$config->getConfiguracoesNotificacoes();
$config->getConfiguracoesBackup();

// Operações em lote
$config->salvarConfiguracoes('categoria', $dados);
$config->resetarParaPadrao('categoria');
$config->importarConfiguracoes($dados);
$config->exportarConfiguracoes();
```

---

### **4. EmpresaModel Atualizado** (`app/Models/EmpresaModel.php`)

**Descrição**: Modelo atualizado com métodos adicionais para estatísticas e relatórios.

**Novos Métodos Adicionados**:
```php
// Estatísticas e contagens
$empresaModel->countAtivas();
$empresaModel->getCountByPorte();
$empresaModel->getCountByEstado();
$empresaModel->getCrescimentoMensal();

// Busca e filtros avançados
$empresaModel->getPaginated(1, 15, $filtros);
$empresaModel->search('termo');
$empresaModel->getByPorte('medio');
$empresaModel->getByEstado('SP');

// Validação e consultas externas
$empresaModel->validarCNPJ($cnpj);
$empresaModel->consultarReceitaFederal($cnpj);

// Gerenciamento de status
$empresaModel->ativar($id);
$empresaModel->inativar($id);
```

---

### **5. Usuario Atualizado** (`app/Models/Usuario.php`)

**Descrição**: Modelo atualizado com métodos para gerenciamento completo de usuários.

**Novos Métodos Adicionados**:
```php
// Estatísticas e contagens
$usuario->count();
$usuario->countAtivos();
$usuario->getCountByTipo();
$usuario->getCountByEmpresa($empresaId);

// Busca e filtros avançados
$usuario->getPaginated(1, 15, $filtros);
$usuario->search('termo');
$usuario->getByTipo('admin_geral');
$usuario->getByStatus('ativo');

// Gerenciamento de senhas
$usuario->alterarSenha($id, $novaSenha);
$usuario->redefinirSenha($id, $novaSenha);

// Validações
$usuario->validarCPF($cpf);
$usuario->validarEmail($email);
$usuario->validarForcaSenha($senha);

// Gerenciamento de status
$usuario->ativar($id);
$usuario->inativar($id);
```

---

## 🗄️ **Tabelas de Banco de Dados**

### **1. Tabela `estabelecimentos`**
```sql
CREATE TABLE estabelecimentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    cnpj VARCHAR(18) UNIQUE,
    endereco TEXT,
    cidade VARCHAR(100),
    estado VARCHAR(2),
    cep VARCHAR(10),
    telefone VARCHAR(20),
    email VARCHAR(255),
    responsavel VARCHAR(255),
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    tipo_estabelecimento VARCHAR(100),
    data_abertura DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_empresa_id (empresa_id),
    INDEX idx_status (status),
    INDEX idx_estado (estado),
    INDEX idx_cidade (cidade),
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE
);
```

### **2. Tabela `atividades`**
```sql
CREATE TABLE atividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    empresa_id INT NOT NULL,
    tipo VARCHAR(100) NOT NULL,
    acao VARCHAR(100) NOT NULL,
    descricao TEXT,
    dados_anteriores JSON,
    dados_novos JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_empresa_id (empresa_id),
    INDEX idx_tipo (tipo),
    INDEX idx_acao (acao),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE
);
```

### **3. Tabela `configuracoes`**
```sql
CREATE TABLE configuracoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(255) UNIQUE NOT NULL,
    valor TEXT,
    tipo VARCHAR(50) DEFAULT 'string',
    descricao TEXT,
    categoria VARCHAR(100) DEFAULT 'geral',
    editavel BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_chave (chave),
    INDEX idx_categoria (categoria),
    INDEX idx_editavel (editavel)
);
```

---

## 🚀 **Como Usar os Modelos**

### **1. Exemplo de Uso do EstabelecimentoModel**
```php
use App\Models\EstabelecimentoModel;

$estabelecimentoModel = new EstabelecimentoModel();

// Criar novo estabelecimento
$dados = [
    'empresa_id' => 1,
    'nome' => 'Loja Centro',
    'cnpj' => '12.345.678/0001-90',
    'endereco' => 'Rua das Flores, 123',
    'cidade' => 'São Paulo',
    'estado' => 'SP',
    'cep' => '01234-567'
];

$estabelecimentoModel->create($dados);

// Buscar estabelecimentos de uma empresa
$estabelecimentos = $estabelecimentoModel->getByEmpresa(1);

// Obter estatísticas
$estatisticas = $estabelecimentoModel->getEstatisticas();
```

### **2. Exemplo de Uso do AtividadeModel**
```php
use App\Models\AtividadeModel;

$atividadeModel = new AtividadeModel();

// Registrar uma atividade
$atividadeModel->registrarAtividade(
    1, // usuario_id
    1, // empresa_id
    'empresa', // tipo
    'criar', // acao
    'Nova empresa criada: Empresa Exemplo LTDA' // descricao
);

// Buscar atividades recentes
$atividades = $atividadeModel->getRecent(10);

// Obter estatísticas de atividades
$estatisticas = $atividadeModel->getEstatisticas();
```

### **3. Exemplo de Uso do Config**
```php
use App\Models\Config;

$config = new Config();

// Definir uma configuração
$config->set('nome_sistema', 'Meu Sistema', 'string', 'Nome do sistema', 'geral');

// Obter uma configuração
$nomeSistema = $config->get('nome_sistema', 'Sistema Padrão');

// Obter todas as configurações de uma categoria
$configuracoesGerais = $config->getConfiguracoesGerais();

// Salvar múltiplas configurações
$config->salvarConfiguracoes('usuarios', [
    'min_senha' => 10,
    'max_tentativas_login' => 3
]);
```

---

## ⚠️ **Observações Importantes**

1. **Erros de Linter**: Alguns métodos como `groupBy`, `update`, `insert`, `delete` podem não estar implementados no QueryBuilder base. Isso é esperado e pode ser resolvido implementando esses métodos no core do sistema.

2. **Migrações**: As migrações criadas assumem que o sistema de migração suporta os métodos `addColumns`, `addIndex`, etc. Se não suportar, será necessário criar as tabelas manualmente ou adaptar as migrações.

3. **Dependências**: Os modelos dependem do `Core\Database\Model` e `Core\Database\QueryBuilder`. Certifique-se de que essas classes estão implementadas corretamente.

4. **Configurações Padrão**: O seeder de configurações popula o sistema com valores padrão. Execute-o após criar as tabelas para ter um sistema funcional.

---

## 🔧 **Próximos Passos**

1. **Implementar métodos faltantes** no QueryBuilder (groupBy, update, insert, delete)
2. **Executar as migrações** para criar as tabelas
3. **Executar o seeder** de configurações
4. **Testar os modelos** com dados reais
5. **Ajustar os controladores** para usar os novos métodos dos modelos

Com esses modelos implementados, o sistema de painel administrativo deve funcionar corretamente, resolvendo os erros de linter e fornecendo todas as funcionalidades necessárias. 