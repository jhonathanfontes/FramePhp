
# API Reference - Sistema Multi-Empresas

## 📋 Visão Geral

A API do Sistema Multi-Empresas fornece endpoints RESTful para integração com aplicações externas, mobile apps e sistemas terceiros.

### Base URL
```
http://localhost:5000/api
```

### Autenticação
A API utiliza JWT (JSON Web Tokens) para autenticação.

```http
Authorization: Bearer {token}
```

## 🔐 Autenticação

### Login
```http
POST /api/auth/login
```

**Request Body:**
```json
{
    "email": "usuario@email.com",
    "password": "senha123"
}
```

**Response 200:**
```json
{
    "success": true,
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "user": {
        "id": 1,
        "name": "João Silva",
        "email": "usuario@email.com",
        "empresa_id": 1
    },
    "expires_in": 3600
}
```

### Refresh Token
```http
POST /api/auth/refresh
```

**Headers:**
```http
Authorization: Bearer {token}
```

**Response 200:**
```json
{
    "success": true,
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "expires_in": 3600
}
```

### Logout
```http
POST /api/auth/logout
```

## 🏢 Empresas

### Listar Empresas
```http
GET /api/empresas
```

**Parameters:**
- `page` (optional): Número da página (default: 1)
- `limit` (optional): Items por página (default: 20)
- `search` (optional): Busca por nome

**Response 200:**
```json
{
    "data": [
        {
            "id": 1,
            "razao_social": "Empresa LTDA",
            "nome_fantasia": "Empresa",
            "cnpj": "12.345.678/0001-90",
            "email": "contato@empresa.com",
            "telefone": "(11) 1234-5678",
            "ativa": true,
            "created_at": "2024-01-01T10:00:00Z"
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 20,
        "total": 50,
        "total_pages": 3
    }
}
```

### Obter Empresa
```http
GET /api/empresas/{id}
```

**Response 200:**
```json
{
    "id": 1,
    "razao_social": "Empresa LTDA",
    "nome_fantasia": "Empresa",
    "cnpj": "12.345.678/0001-90",
    "email": "contato@empresa.com",
    "telefone": "(11) 1234-5678",
    "endereco": "Rua das Flores, 123",
    "cidade": "São Paulo",
    "estado": "SP",
    "cep": "01234-567",
    "ativa": true,
    "created_at": "2024-01-01T10:00:00Z",
    "updated_at": "2024-01-01T10:00:00Z"
}
```

### Criar Empresa
```http
POST /api/empresas
```

**Request Body:**
```json
{
    "razao_social": "Nova Empresa LTDA",
    "nome_fantasia": "Nova Empresa",
    "cnpj": "98.765.432/0001-10",
    "email": "contato@novaempresa.com",
    "telefone": "(11) 9876-5432",
    "endereco": "Av. Principal, 456",
    "cidade": "Rio de Janeiro",
    "estado": "RJ",
    "cep": "20000-000"
}
```

**Response 201:**
```json
{
    "id": 2,
    "razao_social": "Nova Empresa LTDA",
    "nome_fantasia": "Nova Empresa",
    "message": "Empresa criada com sucesso"
}
```

## 🛍️ Produtos

### Listar Produtos
```http
GET /api/produtos
```

**Parameters:**
- `categoria_id` (optional): Filtrar por categoria
- `empresa_id` (optional): Filtrar por empresa
- `ativo` (optional): true/false
- `search` (optional): Busca por nome ou descrição
- `order_by` (optional): nome, preco, created_at
- `order_direction` (optional): asc, desc

**Response 200:**
```json
{
    "data": [
        {
            "id": 1,
            "nome": "Produto Exemplo",
            "descricao": "Descrição do produto",
            "preco": 99.90,
            "preco_promocional": 79.90,
            "categoria": {
                "id": 1,
                "nome": "Eletrônicos"
            },
            "empresa": {
                "id": 1,
                "nome_fantasia": "Empresa"
            },
            "estoque": 100,
            "ativo": true,
            "imagem_url": "/uploads/produtos/produto1.jpg"
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 20,
        "total": 150,
        "total_pages": 8
    }
}
```

### Obter Produto
```http
GET /api/produtos/{id}
```

**Response 200:**
```json
{
    "id": 1,
    "nome": "Produto Exemplo",
    "descricao": "Descrição detalhada do produto",
    "descricao_longa": "Descrição completa...",
    "preco": 99.90,
    "preco_promocional": 79.90,
    "categoria": {
        "id": 1,
        "nome": "Eletrônicos",
        "descricao": "Produtos eletrônicos"
    },
    "fabricante": {
        "id": 1,
        "nome": "Fabricante X"
    },
    "empresa": {
        "id": 1,
        "nome_fantasia": "Empresa",
        "razao_social": "Empresa LTDA"
    },
    "estoque": 100,
    "peso": 0.5,
    "dimensoes": {
        "altura": 10,
        "largura": 15,
        "profundidade": 20
    },
    "ativo": true,
    "imagens": [
        "/uploads/produtos/produto1_1.jpg",
        "/uploads/produtos/produto1_2.jpg"
    ],
    "especificacoes": {
        "cor": "Azul",
        "material": "Plástico"
    },
    "created_at": "2024-01-01T10:00:00Z",
    "updated_at": "2024-01-01T10:00:00Z"
}
```

### Criar Produto
```http
POST /api/produtos
```

**Request Body:**
```json
{
    "nome": "Novo Produto",
    "descricao": "Descrição do novo produto",
    "preco": 129.90,
    "categoria_id": 1,
    "fabricante_id": 1,
    "estoque": 50,
    "peso": 0.8,
    "ativo": true
}
```

## 🛒 Pedidos

### Listar Pedidos
```http
GET /api/pedidos
```

**Parameters:**
- `status` (optional): pendente, processando, enviado, entregue, cancelado
- `empresa_id` (optional): Filtrar por empresa
- `data_inicio` (optional): YYYY-MM-DD
- `data_fim` (optional): YYYY-MM-DD

**Response 200:**
```json
{
    "data": [
        {
            "id": 1,
            "numero": "PED-2024-0001",
            "cliente": {
                "id": 1,
                "nome": "João Cliente",
                "email": "joao@email.com"
            },
            "empresa": {
                "id": 1,
                "nome_fantasia": "Empresa"
            },
            "status": "processando",
            "total": 199.80,
            "itens_count": 2,
            "data_pedido": "2024-01-01T10:00:00Z",
            "data_entrega_prevista": "2024-01-05T10:00:00Z"
        }
    ]
}
```

### Obter Pedido
```http
GET /api/pedidos/{id}
```

**Response 200:**
```json
{
    "id": 1,
    "numero": "PED-2024-0001",
    "cliente": {
        "id": 1,
        "nome": "João Cliente",
        "email": "joao@email.com",
        "telefone": "(11) 9999-8888"
    },
    "empresa": {
        "id": 1,
        "nome_fantasia": "Empresa",
        "razao_social": "Empresa LTDA"
    },
    "status": "processando",
    "endereco_entrega": {
        "endereco": "Rua A, 123",
        "cidade": "São Paulo",
        "estado": "SP",
        "cep": "01234-567"
    },
    "itens": [
        {
            "id": 1,
            "produto": {
                "id": 1,
                "nome": "Produto A",
                "preco": 99.90
            },
            "quantidade": 2,
            "preco_unitario": 99.90,
            "subtotal": 199.80
        }
    ],
    "subtotal": 199.80,
    "desconto": 0.00,
    "frete": 15.00,
    "total": 214.80,
    "data_pedido": "2024-01-01T10:00:00Z",
    "data_entrega_prevista": "2024-01-05T10:00:00Z"
}
```

### Criar Pedido
```http
POST /api/pedidos
```

**Request Body:**
```json
{
    "cliente_id": 1,
    "empresa_id": 1,
    "endereco_entrega": {
        "endereco": "Rua A, 123",
        "cidade": "São Paulo",
        "estado": "SP",
        "cep": "01234-567"
    },
    "itens": [
        {
            "produto_id": 1,
            "quantidade": 2,
            "preco_unitario": 99.90
        }
    ],
    "observacoes": "Entregar no período da manhã"
}
```

### Atualizar Status do Pedido
```http
PUT /api/pedidos/{id}/status
```

**Request Body:**
```json
{
    "status": "enviado",
    "observacoes": "Pedido enviado via correios"
}
```

## 📊 Relatórios

### Relatório de Vendas
```http
GET /api/relatorios/vendas
```

**Parameters:**
- `data_inicio`: YYYY-MM-DD (required)
- `data_fim`: YYYY-MM-DD (required)
- `empresa_id` (optional): Filtrar por empresa

**Response 200:**
```json
{
    "periodo": {
        "inicio": "2024-01-01",
        "fim": "2024-01-31"
    },
    "resumo": {
        "total_pedidos": 150,
        "total_vendas": 45000.00,
        "ticket_medio": 300.00,
        "produtos_vendidos": 450
    },
    "vendas_por_dia": [
        {
            "data": "2024-01-01",
            "pedidos": 5,
            "valor": 1500.00
        }
    ],
    "produtos_mais_vendidos": [
        {
            "produto_id": 1,
            "nome": "Produto A",
            "quantidade": 50,
            "valor_total": 2500.00
        }
    ]
}
```

### Relatório de Produtos
```http
GET /api/relatorios/produtos
```

**Parameters:**
- `empresa_id` (optional): Filtrar por empresa
- `categoria_id` (optional): Filtrar por categoria

**Response 200:**
```json
{
    "resumo": {
        "total_produtos": 200,
        "produtos_ativos": 180,
        "produtos_sem_estoque": 15,
        "valor_total_estoque": 125000.00
    },
    "produtos": [
        {
            "id": 1,
            "nome": "Produto A",
            "categoria": "Eletrônicos",
            "estoque": 50,
            "preco": 99.90,
            "valor_estoque": 4995.00,
            "vendas_mes": 25,
            "ativo": true
        }
    ]
}
```

## 📋 Categorias

### Listar Categorias
```http
GET /api/categorias
```

**Response 200:**
```json
{
    "data": [
        {
            "id": 1,
            "nome": "Eletrônicos",
            "descricao": "Produtos eletrônicos em geral",
            "ativa": true,
            "produtos_count": 25
        }
    ]
}
```

## 🏪 Lojas

### Listar Lojas por Empresa
```http
GET /api/empresas/{empresa_id}/lojas
```

**Response 200:**
```json
{
    "data": [
        {
            "id": 1,
            "nome": "Loja Centro",
            "endereco": "Rua Central, 100",
            "telefone": "(11) 1111-2222",
            "ativa": true,
            "empresa_id": 1
        }
    ]
}
```

## ❌ Códigos de Erro

### HTTP Status Codes
- `200` - OK
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Unprocessable Entity
- `429` - Too Many Requests
- `500` - Internal Server Error

### Formato de Erro
```json
{
    "error": true,
    "message": "Mensagem de erro",
    "code": "ERROR_CODE",
    "details": {
        "field": ["Campo obrigatório"]
    }
}
```

### Exemplos de Erros

**401 Unauthorized:**
```json
{
    "error": true,
    "message": "Token inválido ou expirado",
    "code": "INVALID_TOKEN"
}
```

**422 Validation Error:**
```json
{
    "error": true,
    "message": "Dados de entrada inválidos",
    "code": "VALIDATION_ERROR",
    "details": {
        "email": ["O campo email é obrigatório"],
        "password": ["A senha deve ter pelo menos 8 caracteres"]
    }
}
```

**429 Rate Limit:**
```json
{
    "error": true,
    "message": "Muitas requisições. Tente novamente em 60 segundos",
    "code": "RATE_LIMIT_EXCEEDED",
    "retry_after": 60
}
```

## 🔄 Rate Limiting

A API possui limitação de requisições:
- **Autenticados**: 1000 requests/hora
- **Não autenticados**: 100 requests/hora
- **Endpoints críticos**: 10 requests/minuto

Headers de resposta:
```http
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 999
X-RateLimit-Reset: 1640995200
```

## 📝 Versionamento

A API utiliza versionamento via header:
```http
Accept: application/vnd.api+json;version=1
```

Versões disponíveis:
- `v1` (atual)

## 🧪 Testando a API

### Postman Collection
Importe a collection Postman disponível em: `docs/postman/Sistema-MultiEmpresas.json`

### cURL Examples

**Login:**
```bash
curl -X POST http://localhost:5000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@email.com","password":"123456"}'
```

**Listar Produtos:**
```bash
curl -X GET http://localhost:5000/api/produtos \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Criar Produto:**
```bash
curl -X POST http://localhost:5000/api/produtos \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"nome":"Produto Teste","preco":99.90,"categoria_id":1}'
```

---

**Para mais informações, consulte a documentação completa ou entre em contato com o suporte.**
