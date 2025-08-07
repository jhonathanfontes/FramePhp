# Mapeamento de Rotas - FramePhp

## Visão Geral

Este documento mapeia todas as rotas do projeto FramePhp, detalhando qual URL se conecta a qual controlador e método.

## Estrutura de Rotas

### 1. Rotas da Loja (`routes/loja.php`)

#### Rotas Públicas

| URL | Método | Controlador | Método | Descrição |
|-----|--------|-------------|--------|-----------|
| `/` | GET | `LojaHomeController` | `index` | Página inicial da loja |
| `/produtos` | GET | `LojaProdutoController` | `index` | Listagem de produtos |
| `/produto/{id}` | GET | `LojaProdutoController` | `show` | Detalhes do produto |
| `/produto/{id}/quick-view` | GET | `LojaProdutoController` | `quickView` | Visualização rápida do produto |
| `/categoria/{id}` | GET | `LojaCategoriaController` | `show` | Produtos por categoria |
| `/categorias` | GET | `LojaCategoriaController` | `index` | Lista de categorias |
| `/busca` | GET | `LojaBuscaController` | `index` | Página de busca |
| `/busca/autocomplete` | GET | `LojaBuscaController` | `autocomplete` | Autocomplete da busca |
| `/sobre` | GET | `LojaSobreController` | `index` | Página sobre |
| `/contato` | GET | `LojaContatoController` | `index` | Página de contato |
| `/contato` | POST | `LojaContatoController` | `enviar` | Enviar mensagem de contato |
| `/newsletter` | POST | `LojaNewsletterController` | `cadastrar` | Cadastrar newsletter |

#### Páginas Legais

| URL | Método | Controlador | Método | Descrição |
|-----|--------|-------------|--------|-----------|
| `/termos` | GET | `LojaSobreController` | `termos` | Termos de uso |
| `/privacidade` | GET | `LojaSobreController` | `privacidade` | Política de privacidade |
| `/cookies` | GET | `LojaSobreController` | `cookies` | Política de cookies |
| `/trocas` | GET | `LojaSobreController` | `trocas` | Política de trocas |
| `/frete` | GET | `LojaSobreController` | `frete` | Informações de frete |
| `/pagamento` | GET | `LojaSobreController` | `pagamento` | Formas de pagamento |
| `/faq` | GET | `LojaSobreController` | `faq` | Perguntas frequentes |
| `/depoimentos` | GET | `LojaSobreController` | `depoimentos` | Depoimentos de clientes |
| `/depoimentos` | POST | `LojaSobreController` | `adicionarDepoimento` | Adicionar depoimento |
| `/marcas` | GET | `LojaProdutoController` | `marcas` | Lista de marcas |
| `/marca/{id}` | GET | `LojaProdutoController` | `produtosPorMarca` | Produtos por marca |

#### Rotas de Autenticação

| URL | Método | Controlador | Método | Descrição |
|-----|--------|-------------|--------|-----------|
| `/loja/login` | GET/POST | `SiteAuthController` | `loginLoja` | Login da loja |
| `/loja/logout` | GET | `SiteAuthController` | `logout` | Logout |
| `/loja/cadastro` | GET/POST | `SiteAuthController` | `cadastroLoja` | Cadastro na loja |
| `/loja/esqueci-senha` | GET/POST | `SiteAuthController` | `esqueciSenha` | Recuperar senha |
| `/loja/reset-senha/{token}` | GET/POST | `SiteAuthController` | `resetSenha` | Reset de senha |
| `/loja/verificar-email/{token}` | GET | `SiteAuthController` | `verificarEmail` | Verificar email |
| `/loja/reenviar-verificacao` | POST | `SiteAuthController` | `reenviarVerificacao` | Reenviar verificação |

#### Rotas Protegidas (Autenticadas)

| URL | Método | Controlador | Método | Descrição |
|-----|--------|-------------|--------|-----------|
| `/loja/perfil` | GET | `LojaUsuarioController` | `perfil` | Perfil do usuário |
| `/loja/perfil` | POST | `BackendUsuarioController` | `update` | Atualizar perfil |
| `/loja/perfil/senha` | POST | `BackendUsuarioController` | `updateSenha` | Atualizar senha |
| `/loja/perfil/avatar` | POST | `BackendUsuarioController` | `updateAvatar` | Atualizar avatar |
| `/loja/pedidos` | GET | `LojaUsuarioController` | `pedidos` | Lista de pedidos |
| `/loja/pedido/{id}` | GET | `LojaUsuarioController` | `pedido` | Detalhes do pedido |
| `/loja/pedido/{id}/cancelar` | POST | `LojaUsuarioController` | `cancelarPedido` | Cancelar pedido |
| `/loja/pedido/{id}/avaliar` | POST | `LojaUsuarioController` | `avaliarPedido` | Avaliar pedido |
| `/loja/favoritos` | GET | `LojaFavoritoController` | `index` | Lista de favoritos |
| `/loja/favoritos/toggle` | POST | `LojaFavoritoController` | `toggle` | Adicionar/remover favorito |
| `/loja/favoritos/{id}` | DELETE | `LojaFavoritoController` | `remove` | Remover favorito |
| `/loja/enderecos` | GET | `LojaUsuarioController` | `enderecos` | Lista de endereços |
| `/loja/enderecos` | POST | `LojaUsuarioController` | `adicionarEndereco` | Adicionar endereço |
| `/loja/enderecos/{id}` | PUT | `LojaUsuarioController` | `editarEndereco` | Editar endereço |
| `/loja/enderecos/{id}` | DELETE | `LojaUsuarioController` | `removerEndereco` | Remover endereço |
| `/loja/cartoes` | GET | `LojaUsuarioController` | `cartoes` | Lista de cartões |
| `/loja/cartoes` | POST | `LojaUsuarioController` | `adicionarCartao` | Adicionar cartão |
| `/loja/cartoes/{id}` | DELETE | `LojaUsuarioController` | `removerCartao` | Remover cartão |
| `/loja/notificacoes` | GET | `LojaUsuarioController` | `notificacoes` | Lista de notificações |
| `/loja/notificacoes/{id}/ler` | POST | `LojaUsuarioController` | `marcarNotificacaoLida` | Marcar notificação como lida |
| `/loja/notificacoes/ler-todas` | POST | `LojaUsuarioController` | `marcarTodasNotificacoesLidas` | Marcar todas como lidas |

#### Rotas do Carrinho

| URL | Método | Controlador | Método | Descrição |
|-----|--------|-------------|--------|-----------|
| `/carrinho` | GET | `LojaCarrinhoController` | `index` | Visualizar carrinho |
| `/carrinho/adicionar` | POST | `BackendCarrinhoController` | `adicionar` | Adicionar produto |
| `/carrinho/atualizar` | POST | `BackendCarrinhoController` | `atualizar` | Atualizar quantidade |
| `/carrinho/remover` | POST | `BackendCarrinhoController` | `remover` | Remover produto |
| `/carrinho/limpar` | POST | `BackendCarrinhoController` | `limpar` | Limpar carrinho |
| `/carrinho/cupom` | POST | `BackendCarrinhoController` | `aplicarCupom` | Aplicar cupom |
| `/carrinho/cupom` | DELETE | `BackendCarrinhoController` | `removerCupom` | Remover cupom |
| `/carrinho/calcular-frete` | POST | `BackendCarrinhoController` | `calcularFrete` | Calcular frete |

#### Rotas do Checkout

| URL | Método | Controlador | Método | Descrição |
|-----|--------|-------------|--------|-----------|
| `/checkout` | GET | `BackendCheckoutController` | `index` | Página do checkout |
| `/checkout/processar` | POST | `BackendCheckoutController` | `processar` | Processar pedido |
| `/checkout/sucesso/{id}` | GET | `BackendCheckoutController` | `sucesso` | Página de sucesso |
| `/checkout/cancelado` | GET | `BackendCheckoutController` | `cancelado` | Página de cancelamento |
| `/checkout/erro` | GET | `BackendCheckoutController` | `erro` | Página de erro |
| `/checkout/webhook/pagseguro` | POST | `BackendCheckoutController` | `webhookPagSeguro` | Webhook PagSeguro |
| `/checkout/webhook/mercadopago` | POST | `BackendCheckoutController` | `webhookMercadoPago` | Webhook MercadoPago |
| `/checkout/webhook/paypal` | POST | `BackendCheckoutController` | `webhookPayPal` | Webhook PayPal |

#### Rotas de API

| URL | Método | Controlador | Método | Descrição |
|-----|--------|-------------|--------|-----------|
| `/api/loja/produtos` | GET | `LojaProdutoController` | `apiIndex` | API produtos |
| `/api/loja/produtos/{id}` | GET | `LojaProdutoController` | `apiShow` | API produto específico |
| `/api/loja/categorias` | GET | `LojaCategoriaController` | `apiIndex` | API categorias |
| `/api/loja/busca` | GET | `LojaBuscaController` | `apiBusca` | API busca |
| `/api/loja/cep/{cep}` | GET | `LojaUsuarioController` | `consultarCep` | Consulta CEP |
| `/api/loja/validar/cpf` | POST | `LojaUsuarioController` | `validarCpf` | Validar CPF |
| `/api/loja/validar/cnpj` | POST | `LojaUsuarioController` | `validarCnpj` | Validar CNPJ |
| `/api/loja/validar/email` | POST | `LojaUsuarioController` | `validarEmail` | Validar email |

#### Rotas de Administração da Loja

| URL | Método | Controlador | Método | Descrição |
|-----|--------|-------------|--------|-----------|
| `/admin/loja` | GET | `LojaHomeController` | `admin` | Dashboard admin |
| `/admin/loja/produtos` | GET | `LojaProdutoController` | `adminIndex` | Lista produtos admin |
| `/admin/loja/produtos/criar` | GET | `LojaProdutoController` | `adminCreate` | Criar produto |
| `/admin/loja/produtos` | POST | `LojaProdutoController` | `adminStore` | Salvar produto |
| `/admin/loja/produtos/{id}/editar` | GET | `LojaProdutoController` | `adminEdit` | Editar produto |
| `/admin/loja/produtos/{id}` | PUT | `LojaProdutoController` | `adminUpdate` | Atualizar produto |
| `/admin/loja/produtos/{id}` | DELETE | `LojaProdutoController` | `adminDestroy` | Excluir produto |
| `/admin/loja/pedidos` | GET | `LojaUsuarioController` | `adminPedidos` | Lista pedidos admin |
| `/admin/loja/pedidos/{id}` | GET | `LojaUsuarioController` | `adminPedido` | Detalhes pedido admin |
| `/admin/loja/pedidos/{id}/status` | PUT | `LojaUsuarioController` | `adminUpdateStatusPedido` | Atualizar status |
| `/admin/loja/relatorios/vendas` | GET | `LojaHomeController` | `relatorioVendas` | Relatório vendas |
| `/admin/loja/relatorios/produtos` | GET | `LojaHomeController` | `relatorioProdutos` | Relatório produtos |
| `/admin/loja/relatorios/clientes` | GET | `LojaHomeController` | `relatorioClientes` | Relatório clientes |

### 2. Rotas do Painel (`routes/painel.php`)

| URL | Método | Controlador | Método | Descrição |
|-----|--------|-------------|--------|-----------|
| `/painel` | GET | `PainelDashboardController` | `index` | Dashboard do painel |
| `/painel/empresas` | GET | `PainelEmpresasController` | `index` | Lista de empresas |
| `/painel/usuarios` | GET | `PainelUsuariosController` | `index` | Lista de usuários |

### 3. Rotas do Admin (`routes/admin.php`)

| URL | Método | Controlador | Método | Descrição |
|-----|--------|-------------|--------|-----------|
| `/admin` | GET | `AdminDashboardController` | `index` | Dashboard admin |
| `/admin/login` | GET/POST | `AdminAuthController` | `login` | Login admin |
| `/admin/logout` | GET | `AdminAuthController` | `logout` | Logout admin |
| `/admin/usuarios` | GET | `AdminUsuariosController` | `index` | Gerenciar usuários |
| `/admin/empresas` | GET | `AdminEmpresasController` | `index` | Gerenciar empresas |
| `/admin/menus` | GET | `AdminMenuController` | `index` | Gerenciar menus |
| `/admin/relatorios` | GET | `AdminReportController` | `index` | Relatórios |

### 4. Rotas da API (`routes/api.php`)

| URL | Método | Controlador | Método | Descrição |
|-----|--------|-------------|--------|-----------|
| `/api/auth/login` | POST | `ApiAuthController` | `login` | Login API |
| `/api/auth/logout` | POST | `ApiAuthController` | `logout` | Logout API |
| `/api/users` | GET | `ApiUserController` | `index` | Lista usuários API |
| `/api/users/{id}` | GET | `ApiUserController` | `show` | Usuário específico API |

## Middlewares Aplicados

### Middlewares Globais
- `locale` - Configuração de idioma
- `csrf` - Proteção CSRF
- `cors` - Configuração CORS

### Middlewares Específicos
- `auth:loja` - Autenticação para área da loja
- `auth:admin` - Autenticação para área administrativa
- `guest` - Acesso apenas para usuários não autenticados

## Convenções de Nomenclatura

### URLs
- URLs em português e minúsculas
- Separadores com hífen (-)
- Parâmetros dinâmicos com chaves {}

### Controladores
- Sufixo `Controller`
- Namespace organizado por área (`Loja`, `Admin`, `Painel`)
- Métodos descritivos em português

### Métodos HTTP
- GET: Leitura de dados
- POST: Criação de dados
- PUT: Atualização completa
- PATCH: Atualização parcial
- DELETE: Remoção de dados

## Estrutura de Arquivos de Rota

```
routes/
├── loja.php          # Rotas da loja virtual
├── painel.php        # Rotas do painel
├── admin.php         # Rotas administrativas
├── api.php           # Rotas da API
├── client.php        # Rotas do cliente
├── empresa.php       # Rotas da empresa
└── store.php         # Rotas da loja (alternativo)
```

## Observações Importantes

1. **Segurança**: Todas as rotas sensíveis possuem middleware de autenticação
2. **Validação**: Rotas POST/PUT possuem validação CSRF
3. **Organização**: Rotas organizadas por funcionalidade
4. **Documentação**: Cada rota possui descrição clara
5. **Flexibilidade**: Estrutura permite fácil adição de novas rotas
