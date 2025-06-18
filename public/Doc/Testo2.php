Páginas Twig com Exemplos de Uso dos Componentes
 Estrutura de Pastas
 pages/
 ├── layout/
 │   └── base.html.twig
 ├── home/
 │   └── index.html.twig
 ├── auth/
 │   ├── login.html.twig
 │   └── register.html.twig
 ├── dashboard/
 │   └── index.html.twig
 ├── products/
 │   ├── index.html.twig
 │   ├── show.html.twig
 │   └── create.html.twig
 └── profile/
 └── edit.html.twig
 1. Layout Base (layout/base.html.twig)
twig
<!DOCTYPE html>
 <html lang="pt-BR">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{% block title %}Meu Site{% endblock %}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="s
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel=
    
    {% block stylesheets %}{% endblock %}
 </head>
 <body>
    <!-- Header com Menu -->
    {% include 'components/navigation/menu.html.twig' with {
        'brand': {'text': 'MeuSite', 'url': '/'},
        'items': [
            {'text': 'Home', 'url': '/', 'active': current_route == 'home'},
            {'text': 'Produtos', 'url': '/produtos', 'active': current_route starts with 'produ
            {'text': 'Dashboard', 'url': '/dashboard', 'active': current_route == 'dashboard'},
            {
                'text': 'Conta',
                'dropdown': [
                    {'text': 'Perfil', 'url': '/perfil'},
                    {'text': 'Configurações', 'url': '/configuracoes'},
                    {'divider': true},
                    {'text': 'Sair', 'url': '/logout'}
                ]
            }
        ],
        'class': 'navbar-expand-lg navbar-dark bg-primary'
    } %}
    <!-- Conteúdo Principal -->
    <main class="{% block main_class %}container my-4{% endblock %}">
        {% block content %}{% endblock %}
    </main>
    <!-- Footer -->
    <footer class="bg-light text-center py-3 mt-5">
        <div class="container">
            <p class="mb-0">&copy; 2025 MeuSite. Todos os direitos reservados.</p>
        </div>
    </footer>
<!-- Bootstrap JS -->
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
 {% block javascripts %}{% endblock %}
 </body>
 </html>
 2. Página Home (home/index.html.twig)
twig
{% extends 'pages/layout/base.html.twig' %}
 {% block title %}Home - MeuSite{% endblock %}
 {% block content %}
    <div class="hero-section text-center mb-5">
        <h1 class="display-4 mb-3">Bem-vindo ao MeuSite!</h1>
        <p class="lead">Descubra nossos produtos incríveis e ofertas especiais</p>
    </div>
    <!-- Cards de Destaque -->
    <div class="row mb-5">
        <div class="col-md-4 mb-4">
            {% include 'components/ui/card.html.twig' with {
                'image': {'src': '/images/produto1.jpg', 'alt': 'Produto 1'},
                'title': 'Produto em Destaque',
                'subtitle': 'Oferta especial',
                'body': '<p>Descrição do produto mais vendido com desconto especial para novos 
                'actions': [
                    {'text': 'Ver Detalhes', 'url': '/produtos/1', 'class': 'btn-primary'},
                    {'text': 'Comprar', 'url': '/carrinho/add/1', 'class': 'btn-success'}
                ]
            } %}
        </div>
        
        <div class="col-md-4 mb-4">
            {% include 'components/ui/card.html.twig' with {
                'title': 'Novidades',
                'body': '<p>Confira os últimos lançamentos da nossa loja com as melhores tecnol
                'actions': [
                    {'text': 'Explorar', 'url': '/produtos/novidades', 'class': 'btn-outline-pr
                ]
            } %}
        </div>
        
        <div class="col-md-4 mb-4">
            {% include 'components/ui/card.html.twig' with {
                'title': 'Suporte',
                'body': '<p>Precisa de ajuda? Nossa equipe está pronta para atender você.</p>',
                'actions': [
                    {'text': 'Contato', 'url': '/contato', 'class': 'btn-info'}
                ]
            } %}
        </div>
    </div>
3. Página de Login (auth/login.html.twig)
    <!-- Modal de Newsletter -->
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#newsl
        <i class="fas fa-envelope me-2"></i>Assinar Newsletter
    </button>
    {% include 'components/ui/modal.html.twig' with {
        'id': 'newsletterModal',
        'title': 'Assinar Newsletter',
        'body': '
            <p>Receba as melhores ofertas e novidades em seu e-mail!</p>
            <form id="newsletterForm">
                <div class="mb-3">
                    <input type="email" class="form-control" placeholder="Seu e-mail" required>
                </div>
            </form>
        ',
        'actions': [
            {'text': 'Cancelar', 'class': 'btn-secondary', 'dismiss': true},
            {'text': 'Assinar', 'class': 'btn-success', 'onclick': 'submitNewsletter()'}
        ]
    } %}
 {% endblock %}
 {% block javascripts %}
 <script>
 function submitNewsletter() {
    // Lógica para enviar newsletter
    alert('Newsletter assinada com sucesso!');
    bootstrap.Modal.getInstance(document.getElementById('newsletterModal')).hide();
 }
 </script>
 {% endblock %}
twig
{% extends 'pages/layout/base.html.twig' %}
 {% block title %}Login - MeuSite{% endblock %}
 {% block main_class %}container-fluid{% endblock %}
 {% block content %}
 <div class="row justify-content-center align-items-center min-vh-100">
    <div class="col-md-6 col-lg-4">
        {% include 'components/ui/card.html.twig' with {
            'header': '<h4 class="text-center mb-0"><i class="fas fa-sign-in-alt me-2"></i>Entr
            'body': '
                <form method="POST" action="/login">
                    ' ~ include('components/form/input.html.twig', {
                        'name': 'email',
                        'label': 'E-mail',
                        'type': 'email',
                        'placeholder': 'Digite seu e-mail',
                        'required': true,
                        'value': form_data.email|default('')
                    }) ~ '
                    
                    ' ~ include('components/form/input.html.twig', {
                        'name': 'password',
                        'label': 'Senha',
                        'type': 'password',
                        'placeholder': 'Digite sua senha',
                        'required': true
                    }) ~ '
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="remember" name="rem
                        <label class="form-check-label" for="remember">
                            Lembrar-me
                        </label>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Entrar
                        </button>
                    </div>
                </form>
                
                <hr>
                
4. Página de Registro (auth/register.html.twig)
                <div class="text-center">
                    <p><a href="/esqueci-senha">Esqueceu sua senha?</a></p>
                    <p>Não tem conta? <a href="/registro">Cadastre-se</a></p>
                </div>
            '
        } %}
    </div>
 </div>
 {% endblock %}
twig
{% extends 'pages/layout/base.html.twig' %}
 {% block title %}Cadastro - MeuSite{% endblock %}
 {% block content %}
 <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        {% include 'components/ui/card.html.twig' with {
            'header': '<h4 class="text-center mb-0"><i class="fas fa-user-plus me-2"></i>Criar 
            'body': '
                <form method="POST" action="/register">
                    <div class="row">
                        <div class="col-md-6">
                            ' ~ include('components/form/input.html.twig', {
                                'name': 'first_name',
                                'label': 'Nome',
                                'placeholder': 'Seu nome',
                                'required': true,
                                'value': form_data.first_name|default('')
                            }) ~ '
                        </div>
                        <div class="col-md-6">
                            ' ~ include('components/form/input.html.twig', {
                                'name': 'last_name',
                                'label': 'Sobrenome',
                                'placeholder': 'Seu sobrenome',
                                'required': true,
                                'value': form_data.last_name|default('')
                            }) ~ '
                        </div>
                    </div>
                    
                    ' ~ include('components/form/input.html.twig', {
                        'name': 'email',
                        'label': 'E-mail',
                        'type': 'email',
                        'placeholder': 'seu@email.com',
                        'required': true,
                        'value': form_data.email|default(''),
                        'help': 'Nunca compartilharemos seu e-mail'
                    }) ~ '
                    
                    ' ~ include('components/form/input.html.twig', {
                        'name': 'password',
                        'label': 'Senha',
                        'type': 'password',
5. Dashboard (dashboard/index.html.twig)
                        'placeholder': 'Mínimo 8 caracteres',
                        'required': true,
                        'help': 'Deve conter pelo menos 8 caracteres'
                    }) ~ '
                    
                    ' ~ include('components/form/input.html.twig', {
                        'name': 'password_confirmation',
                        'label': 'Confirmar Senha',
                        'type': 'password',
                        'placeholder': 'Repita sua senha',
                        'required': true
                    }) ~ '
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="terms" name="terms"
                        <label class="form-check-label" for="terms">
                            Aceito os <a href="/termos">termos de uso</a> e <a href="/privacida
                        </label>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-user-plus me-2"></i>Criar Conta
                        </button>
                    </div>
                </form>
                
                <hr>
                
                <div class="text-center">
                    <p>Já tem conta? <a href="/login">Faça login</a></p>
                </div>
            '
        } %}
    </div>
 </div>
 {% endblock %}
twig
{% extends 'pages/layout/base.html.twig' %}
 {% block title %}Dashboard - MeuSite{% endblock %}
 {% block content %}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Dashboard</h1>
        <span class="text-muted">Bem-vindo, {{ user.name }}!</span>
    </div>
    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            {% include 'components/ui/card.html.twig' with {
                'body': '
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">{{ stats.total_orders }}</h5>
                            <small class="text-muted">Total de Pedidos</small>
                        </div>
                    </div>
                ',
                'class': 'bg-light'
            } %}
        </div>
        
        <div class="col-md-3 mb-3">
            {% include 'components/ui/card.html.twig' with {
                'body': '
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-dollar-sign fa-2x text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">R$ ' ~ stats.total_revenue ~ '</h5>
                            <small class="text-muted">Receita Total</small>
                        </div>
                    </div>
                ',
                'class': 'bg-light'
            } %}
        </div>
        
        <div class="col-md-3 mb-3">
            {% include 'components/ui/card.html.twig' with {
                'body': '
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users fa-2x text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">{{ stats.total_customers }}</h5>
                            <small class="text-muted">Clientes</small>
                        </div>
                    </div>
                ',
                'class': 'bg-light'
            } %}
        </div>
        
        <div class="col-md-3 mb-3">
            {% include 'components/ui/card.html.twig' with {
                'body': '
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-box fa-2x text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">{{ stats.total_products }}</h5>
                            <small class="text-muted">Produtos</small>
                        </div>
                    </div>
                ',
                'class': 'bg-light'
            } %}
        </div>
    </div>
    <!-- Pedidos Recentes -->
    <div class="row">
        <div class="col-md-8">
            {% include 'components/ui/card.html.twig' with {
                'title': 'Pedidos Recentes',
                'body': '
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                {% for order in recent_orders %}
                                <tr>
                                    <td>#{{ order.id }}</td>
                                    <td>{{ order.customer_name }}</td>
                                    <td>R$ {{ order.total }}</td>
                                    <td>
                                        <span class="badge bg-{{ order.status == 'completed' ? 
                                            {{ order.status }}
                                        </span>
                                    </td>
                                    <td>{{ order.created_at|date('d/m/Y') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick=
                                            Ver
                                        </button>
                                    </td>
                                </tr>
                                {% endfor %}
                            </tbody>
                        </table>
                    </div>
                ',
                'actions': [
                    {'text': 'Ver Todos', 'url': '/pedidos', 'class': 'btn-primary'}
                ]
            } %}
        </div>
        
        <div class="col-md-4">
            {% include 'components/ui/card.html.twig' with {
                'title': 'Ações Rápidas',
                'body': '
                    <div class="d-grid gap-2">
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="
                            <i class="fas fa-plus me-2"></i>Novo Produto
                        </button>
                        <a href="/pedidos/novo" class="btn btn-primary">
                            <i class="fas fa-shopping-bag me-2"></i>Novo Pedido
                        </a>
                        <a href="/relatorios" class="btn btn-info">
                            <i class="fas fa-chart-bar me-2"></i>Relatórios
                        </a>
                        <a href="/configuracoes" class="btn btn-secondary">
                            <i class="fas fa-cog me-2"></i>Configurações
                        </a>
                    </div>
                '
            } %}
        </div>
    </div>
    <!-- Modal Novo Produto -->
    {% include 'components/ui/modal.html.twig' with {
        'id': 'newProductModal',
        'title': 'Novo Produto',
        'size': 'lg',
        'body': '
            <form id="newProductForm">
                ' ~ include('components/form/input.html.twig', {
                    'name': 'name',
                    'label': 'Nome do Produto',
                    'placeholder': 'Digite o nome do produto',
                    'required': true
                }) ~ '
                
                ' ~ include('components/form/input.html.twig', {
                    'name': 'price',
                    'label': 'Preço',
                    'type': 'number',
                    'placeholder': '0.00',
                    'required': true,
                    'attributes': {'step': '0.01', 'min': '0'}
                }) ~ '
                
                <div class="form-group">
                    <label class="form-label">Descrição</label>
                    <textarea class="form-control" name="description" rows="3" placeholder="Des
                </div>
            </form>
        ',
        'actions': [
            {'text': 'Cancelar', 'class': 'btn-secondary', 'dismiss': true},
            {'text': 'Salvar', 'class': 'btn-success', 'onclick': 'saveProduct()'}
        ]
    } %}
 {% endblock %}
{% block javascripts %}
 <script>
 function viewOrder(orderId) {
 window.location.href = '/pedidos/' + orderId;
 }
 function saveProduct() {
 // Lógica para salvar produto
 alert('Produto salvo com sucesso!');
 bootstrap.Modal.getInstance(document.getElementById('newProductModal')).hide();
 }
 </script>
 {% endblock %}
 6. Lista de Produtos (products/index.html.twig)
twig
{% extends 'pages/layout/base.html.twig' %}
 {% block title %}Produtos - MeuSite{% endblock %}
 {% block content %}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Produtos</h1>
        <a href="/produtos/novo" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Novo Produto
        </a>
    </div>
    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-md-12">
            {% include 'components/ui/card.html.twig' with {
                'body': '
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            ' ~ include('components/form/input.html.twig', {
                                'name': 'search',
                                'placeholder': 'Buscar produtos...',
                                'value': filters.search|default('')
                            }) ~ '
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="category">
                                <option value="">Todas as categorias</option>
                                {% for category in categories %}
                                    <option value="{{ category.id }}" {{ filters.category == ca
                                        {{ category.name }}
                                    </option>
                                {% endfor %}
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="status">
                                <option value="">Todos os status</option>
                                <option value="active" {{ filters.status == 'active' ? 'selecte
                                <option value="inactive" {{ filters.status == 'inactive' ? 'sel
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                    </form>
                '
            } %}
        </div>
    </div>
    <!-- Lista de Produtos -->
    <div class="row">
        {% for product in products %}
            <div class="col-md-4 mb-4">
                {% include 'components/ui/card.html.twig' with {
                    'image': product.image ? {'src': product.image, 'alt': product.name} : null
                    'title': product.name,
                    'subtitle': product.category.name,
                    'body': '
                        <p class="text-muted">' ~ product.description|slice(0, 100) ~ '...</p>
                        <h5 class="text-success">R$ ' ~ product.price ~ '</h5>
                        <p class="mb-0">
                            <span class="badge bg-' ~ (product.status == 'active' ? 'success' :
                                ' ~ product.status ~ '
                            </span>
                        </p>
                    ',
                    'actions': [
                        {'text': 'Ver', 'url': '/produtos/' ~ product.id, 'class': 'btn-primary
                        {'text': 'Editar', 'url': '/produtos/' ~ product.id ~ '/editar', 'class
                        {'text': 'Excluir', 'class': 'btn-outline-danger', 'onclick': 'confirmD
                    ]
                } %}
            </div>
        {% else %}
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Nenhum produto encontrado</h4>
                    <p class="text-muted">Tente ajustar os filtros ou adicione um novo produto.
                </div>
            </div>
        {% endfor %}
    </div>
    <!-- Modal de Confirmação -->
    {% include 'components/ui/modal.html.twig' with {
        'id': 'confirmDeleteModal',
        'title': 'Confirmar Exclusão',
        'body': '<p>Tem certeza que deseja excluir este produto? Esta ação não pode ser desfeit
7. Editar Perfil (profile/edit.html.twig)
        'actions': [
            {'text': 'Cancelar', 'class': 'btn-secondary', 'dismiss': true},
            {'text': 'Excluir', 'class': 'btn-danger', 'onclick': 'deleteProduct()'}
        ]
    } %}
 {% endblock %}
 {% block javascripts %}
 <script>
 let productToDelete = null;
 function confirmDelete(productId) {
    productToDelete = productId;
    new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
 }
 function deleteProduct() {
    if (productToDelete) {
        // Aqui você faria a requisição para excluir o produto
        window.location.href = '/produtos/' + productToDelete + '/excluir';
    }
 }
 </script>
 {% endblock %}
twig
{% extends 'pages/layout/base.html.twig' %}
 {% block title %}Editar Perfil - MeuSite{% endblock %}
 {% block content %}
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="mb-4">Editar Perfil</h1>
            
            <div class="row">
                <div class="col-md-8">
                    {% include 'components/ui/card.html.twig' with {
                        'title': 'Informações Pessoais',
                        'body': '
                            <form method="POST" action="/perfil/atualizar" enctype="multipart/f
                                <div class="row">
                                    <div class="col-md-6">
                                        ' ~ include('components/form/input.html.twig', {
                                    'name': 'new_password_confirmation',
                                    'label': 'Confirmar Nova Senha',
                                    'type': 'password',
                                    'required': true
                                }) ~ '
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-key me-2"></i>Alterar Senha
                                    </button>
                                </div>
                            </form>
                        '
                    } %}
                    
                    <!-- Card de Configurações -->
                    {% include 'components/ui/card.html.twig' with {
                        'title': 'Configurações',
                        'body': '
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="
                                    <i class="fas fa-bell me-2"></i>Notificações
                                </button>
                                <button class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-shield-alt me-2"></i>Privacidade
                                </button>
                                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="m
                                    <i class="fas fa-trash me-2"></i>Excluir Conta
                                </button>
                            </div>
                        '
                    } %}
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de Notificações -->
    {% include 'components/ui/modal.html.twig' with {
        'id': 'notificationModal',
        'title': 'Configurações de Notificação',
        'body': '
            <form id="notificationForm">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="emailNotifications" che
                    <label class="form-check-label" for="emailNotifications">
                        Notificações por E-mail
                    </label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="smsNotifications">
                    <label class="form-check-label" for="smsNotifications">
                        Notificações por SMS
                    </label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="pushNotifications" chec
                    <label class="form-check-label" for="pushNotifications">
                        Notificações Push
                    </label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="marketingEmails">
                    <label class="form-check-label" for="marketingEmails">
                        E-mails promocionais
                    </label>
                </div>
            </form>
        ',
        'actions': [
            {'text': 'Cancelar', 'class': 'btn-secondary', 'dismiss': true},
            {'text': 'Salvar', 'class': 'btn-primary', 'onclick': 'saveNotifications()'}
        ]
    } %}
    <!-- Modal de Exclusão de Conta -->
    {% include 'components/ui/modal.html.twig' with {
        'id': 'deleteAccountModal',
        'title': 'Excluir Conta',
        'body': '
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Atenção!</strong> Esta ação é irreversível e todos os seus dados serão 
            </div>
            <p>Para confirmar a exclusão da sua conta, digite sua senha:</p>
            <form id="deleteAccountForm">
                ' ~ include('components/form/input.html.twig', {
                    'name': 'password',
                    'label': 'Senha',
                    'type': 'password',
                    'required': true,
                    'placeholder': 'Digite sua senha para confirmar'
                }) ~ '
            </form>
        ',
        'actions': [
            {'text': 'Cancelar', 'class': 'btn-secondary', 'dismiss': true},
            {'text': 'Excluir Conta', 'class': 'btn-danger', 'onclick': 'deleteAccount()'}
        ]
    } %}
 {% endblock %}
 {% block javascripts %}
 <script>
 function saveNotifications() {
    // Lógica para salvar configurações de notificação
    alert('Configurações salvas com sucesso!');
    bootstrap.Modal.getInstance(document.getElementById('notificationModal')).hide();
 }
 function deleteAccount() {
    const password = document.querySelector('#deleteAccountForm input[name="password"]').value;
    if (!password) {
        alert('Por favor, digite sua senha para confirmar.');
        return;
    }
    
    if (confirm('Tem certeza absoluta que deseja excluir sua conta? Esta ação não pode ser desf
        // Aqui você faria a requisição para excluir a conta
        alert('Conta excluída com sucesso. Você será redirecionado.');
        window.location.href = '/logout';
    }
}
 </script>
 {% endblock %}
 8. Página de Criar Produto (products/create.html.twig)
twig
{% extends 'pages/layout/base.html.twig' %}
 {% block title %}Novo Produto - MeuSite{% endblock %}
 {% block content %}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Novo Produto</h1>
        <a href="/produtos" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar
        </a>
    </div>
    <form method="POST" action="/produtos" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-8">
                {% include 'components/ui/card.html.twig' with {
                    'title': 'Informações Básicas',
                    'body': '
                        ' ~ include('components/form/input.html.twig', {
                            'name': 'name',
                            'label': 'Nome do Produto',
                            'placeholder': 'Digite o nome do produto',
                            'required': true,
                            'value': form_data.name|default('')
                        }) ~ '
                        
                        <div class="form-group">
                            <label class="form-label">Descrição</label>
                            <textarea class="form-control" name="description" rows="5" placehol
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                ' ~ include('components/form/input.html.twig', {
                                    'name': 'price',
                                    'label': 'Preço',
                                    'type': 'number',
                                    'placeholder': '0.00',
                                    'required': true,
                                    'attributes': {'step': '0.01', 'min': '0'},
                                    'value': form_data.price|default('')
                                }) ~ '
                            </div>
                            <div class="col-md-6">
                                ' ~ include('components/form/input.html.twig', {
                                    'name': 'stock',
                                    'label': 'Estoque',
                                    'type': 'number',
                                    'placeholder': '0',
                                    'required': true,
                                    'attributes': {'min': '0'},
                                    'value': form_data.stock|default('')
                                }) ~ '
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Categoria</label>
                            <select class="form-select" name="category_id" required>
                                <option value="">Selecione uma categoria</option>
                                {% for category in categories %}
                                    <option value="{{ category.id }}" {{ form_data.category_id 
                                        {{ category.name }}
                                    </option>
                                {% endfor %}
                            </select>
                        </div>
                    '
                } %}
                {% include 'components/ui/card.html.twig' with {
                    'title': 'Imagens',
                    'body': '
                        <div class="form-group">
                            <label class="form-label">Imagem Principal</label>
                            <input type="file" class="form-control" name="main_image" accept="i
                            <small class="form-text text-muted">Formatos aceitos: JPG, PNG, WEB
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Imagens Adicionais</label>
                            <input type="file" class="form-control" name="additional_images[]" 
                            <small class="form-text text-muted">Você pode selecionar múltiplas 
                        </div>
                        
                        <div id="imagePreview" class="row mt-3"></div>
                    '
                } %}
            </div>
            
            <div class="col-md-4">
                {% include 'components/ui/card.html.twig' with {
                    'title': 'Configurações',
                    'body': '
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="active" {{ form_data.status == 'active' ? 'selec
                                <option value="inactive" {{ form_data.status == 'inactive' ? 's
                                <option value="draft" {{ form_data.status == 'draft' ? 'selecte
                            </select>
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="featured" name=
                            <label class="form-check-label" for="featured">
                                Produto em Destaque
                            </label>
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="track_stock" na
                            <label class="form-check-label" for="track_stock">
                                Controlar Estoque
                            </label>
                        </div>
                        
                        ' ~ include('components/form/input.html.twig', {
                            'name': 'sku',
                            'label': 'SKU',
                            'placeholder': 'Código do produto',
                            'value': form_data.sku|default('')
                        }) ~ '
                        
                        ' ~ include('components/form/input.html.twig', {
                            'name': 'weight',
                            'label': 'Peso (kg)',
                            'type': 'number',
                            'placeholder': '0.000',
                            'attributes': {'step': '0.001', 'min': '0'},
                            'value': form_data.weight|default('')
                        }) ~ '
                    '
                } %}
                {% include 'components/ui/card.html.twig' with {
                    'title': 'SEO',
                    'body': '
                        ' ~ include('components/form/input.html.twig', {
                            'name': 'meta_title',
                            'label': 'Título SEO',
                            'placeholder': 'Título para mecanismos de busca',
                            'value': form_data.meta_title|default('')
                        }) ~ '
                        
                        <div class="form-group">
                            <label class="form-label">Meta Descrição</label>
                            <textarea class="form-control" name="meta_description" rows="3" pla
                        </div>
                    '
                } %}
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Salvar Produto
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="saveAsDraft(
                        <i class="fas fa-file-alt me-2"></i>Salvar como Rascunho
                    </button>
                    <a href="/produtos" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </a>
                </div>
            </div>
        </div>
    </form>
 {% endblock %}
 {% block javascripts %}
 <script>
 // Preview de imagens
 document.querySelector('input[name="main_image"]').addEventListener('change', function(e) {
    previewImages(e.target.files, 'main');
 });
 document.querySelector('input[name="additional_images[]"]').addEventListener('change', function
    previewImages(e.target.files, 'additional');
 });
 function previewImages(files, type) {
    const preview = document.getElementById('imagePreview');
    
    for (let file of files) {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
9. Visualizar Produto (products/show.html.twig)
                div.className = 'col-4 mb-2';
                div.innerHTML = `
                    <img src="${e.target.result}" class="img-thumbnail" style="height: 100px; o
                    <small class="d-block text-center">${type === 'main' ? 'Principal' : 'Adici
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
    }
 }
 function saveAsDraft() {
    // Alterar status para draft antes de enviar
    document.querySelector('select[name="status"]').value = 'draft';
    document.querySelector('form').submit();
 }
 </script>
 {% endblock %}
twig
{% extends 'pages/layout/base.html.twig' %}
 {% block title %}{{ product.name }} - MeuSite{% endblock %}
 {% block content %}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/produtos">Produtos</a></li>
                <li class="breadcrumb-item active">{{ product.name }}</li>
            </ol>
        </nav>
        
        <div class="btn-group">
            <a href="/produtos/{{ product.id }}/editar" class="btn btn-outline-primary">
                <i class="fas fa-edit me-2"></i>Editar
            </a>
            <button class="btn btn-outline-danger" onclick="confirmDelete({{ product.id }})">
                <i class="fas fa-trash me-2"></i>Excluir
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            {% include 'components/ui/card.html.twig' with {
                'body': '
                    <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="' ~ product.main_image ~ '" class="d-block w-100" alt
                            </div>
                            {% for image in product.additional_images %}
                                <div class="carousel-item">
                                    <img src="' ~ image ~ '" class="d-block w-100" alt="' ~ pro
                                </div>
                            {% endfor %}
                        </div>
                        
                        {% if product.additional_images|length > 0 %}
                            <button class="carousel-control-prev" type="button" data-bs-target=
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target=
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        {% endif %}
                    </div>
                '
            } %}
        </div>
        
        <div class="col-md-6">
            {% include 'components/ui/card.html.twig' with {
                'body': '
                    <h1>' ~ product.name ~ '</h1>
                    <p class="text-muted mb-2">SKU: ' ~ product.sku ~ '</p>
                    <h3 class="text-success mb-3">R$ ' ~ product.price ~ '</h3>
                    
                    <div class="mb-3">
                        <span class="badge bg-' ~ (product.status == 'active' ? 'success' : 'se
                            ' ~ product.status|title ~ '
                        </span>
                        {% if product.featured %}
                            <span class="badge bg-warning">Destaque</span>
                        {% endif %}
                    </div>
                    
                    <div class="mb-3">
                        <strong>Categoria:</strong> ' ~ product.category.name ~ '
                    </div>
                    
                    <div class="mb-3">
                        <strong>Estoque:</strong> 
                        <span class="badge bg-' ~ (product.stock > 10 ? 'success' : (product.st
                            ' ~ product.stock ~ ' unidades
                        </span>
                    </div>
                    
                    {% if product.weight %}
                        <div class="mb-3">
                            <strong>Peso:</strong> ' ~ product.weight ~ ' kg
                        </div>
                    {% endif %}
                    
                    <div class="mb-4">
                        <h5>Descrição</h5>
                        <p>' ~ product.description|nl2br ~ '</p>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-success btn-lg" onclick="addToCart(' ~ product.i
                            <i class="fas fa-shopping-cart me-2"></i>Adicionar ao Carrinho
                        </button>
                        <button class="btn btn-outline-primary" onclick="addToWishlist(' ~ prod
                            <i class="fas fa-heart me-2"></i>Adicionar aos Favoritos
                        </button>
                    </div>
                '
            } %}
        </div>
    </div>
    <!-- Informações Adicionais -->
    <div class="row mt-4">
        <div class="col-md-8">
            {% include 'components/ui/card.html.twig' with {
                'title': 'Especificações Técnicas',
                'body': '
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th width="30%">Nome</th>
                                    <td>' ~ product.name ~ '</td>
                                </tr>
                                <tr>
                                    <th>SKU</th>
                                    <td>' ~ product.sku ~ '</td>
                                </tr>
                                <tr>
                                    <th>Categoria</th>
                                    <td>' ~ product.category.name ~ '</td>
                                </tr>
                                <tr>
                                    <th>Peso</th>
                                    <td>' ~ (product.weight ?: 'Não informado') ~ '</td>
                                </tr>
                                <tr>
                                    <th>Data de Criação</th>
                                    <td>' ~ product.created_at|date('d/m/Y H:i') ~ '</td>
                                </tr>
                                <tr>
                                    <th>Última Atualização</th>
                                    <td>' ~ product.updated_at|date('d/m/Y H:i') ~ '</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                '
            } %}
        </div>
        
        <div class="col-md-4">
            {% include 'components/ui/card.html.twig' with {
                'title': 'Ações Rápidas',
                'body': '
                    <div class="d-grid gap-2">
                        <a href="/produtos/{{ product.id }}/editar" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Editar Produto
                        </a>
                        <button class="btn btn-info" onclick="duplicateProduct({{ product.id }}
                            <i class="fas fa-copy me-2"></i>Duplicar
                        </button>
                        <button class="btn btn-warning" onclick="toggleStatus({{ product.id }})
                            <i class="fas fa-toggle-on me-2"></i>Alterar Status
                        </button>
                        <a href="/produtos/{{ product.id }}/historico" class="btn btn-secondary
                            <i class="fas fa-history me-2"></i>Histórico
                        </a>
                    </div>
                '
            } %}
        </div>
    </div>
    <!-- Modal de Confirmação de Exclusão -->
    {% include 'components/ui/modal.html.twig' with {
        'id': 'confirmDeleteModal',
        'title': 'Confirmar Exclusão',
        'body': '
            <p>Tem certeza que deseja excluir o produto <strong>' ~ product.name ~ '</strong>?<
            <p class="text-danger">Esta ação não pode ser desfeita.</p>
        ',
        'actions': [
            {'text': 'Cancelar', 'class': 'btn-secondary', 'dismiss': true},
            {'text': 'Excluir', 'class': 'btn-danger', 'onclick': 'deleteProduct()'}
        ]
    } %}
 {% endblock %}
 {% block javascripts %}
 <script>
 function addToCart(productId) {
    alert('Produto adicionado ao carrinho!');
    // Aqui você faria a requisição para adicionar ao carrinho
Como Usar as Páginas
 1. Configuração do Roteamento
 }
 function addToWishlist(productId) {
    alert('Produto adicionado aos favoritos!');
    // Aqui você faria a requisição para adicionar aos favoritos
 }
 function confirmDelete(productId) {
    new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
 }
 function deleteProduct() {
    window.location.href = '/produtos/{{ product.id }}/excluir';
 }
 function duplicateProduct(productId) {
    if (confirm('Deseja criar uma cópia deste produto?')) {
        window.location.href = '/produtos/' + productId + '/duplicar';
    }
 }
 function toggleStatus(productId) {
    if (confirm('Deseja alterar o status deste produto?')) {
        window.location.href = '/produtos/' + productId + '/toggle-status';
    }
 }
 </script>
 {% endblock %}
 php
 // Exemplo em PHP (pode ser adaptado para outras linguagens)
 $routes = [
    '/' => 'pages/home/index.html.twig',
    '/login' => 'pages/auth/login.html.twig',
    '/registro' => 'pages/auth/register.html.twig',
    '/dashboard' => 'pages/dashboard/index.html.twig',
    '/produtos' => 'pages/products/index.html.twig',
    '/produtos/novo' => 'pages/products/create.html.twig',
    '/produtos/{id}' => 'pages/products/show.html.twig',
    '/perfil/editar' => 'pages/profile/edit.html.twig',
 ];
2. Passagem de Dados
 php
 // Exemplo de como passar dados para as páginas
 $twig->render('pages/products/index.html.twig', [
 'products' => $products,
 'categories' => $categories,
 'filters' => $filters,
 'current_route' => 'products.index'
 ]);
 3. Vantagens desta Estrutura
 Consistência: Todas as páginas usam os mesmos componentes
 Manutenibilidade: Mudanças nos componentes afetam todas as páginas
 Reutilização: Componentes podem ser usados em qualquer página
 Flexibilidade: Fácil personalização através de parâmetros
 Organização: Estrutura clara e hierárquicahtml.twig', { 'name': 'first_name', 'label': 'Nome', 'required':
 true, 'value': user.first_name }) ~ ' </div> <div class="col-md-6"> ' ~
 include('components/form/input.html.twig', { 'name': 'last_name', 'label': 'Sobrenome', 'required': true,
 'value': user.last_name }) ~ ' </div> </div>
                          ' ~ include('components/form/input.html.twig', {
                              'name': 'email',
                              'label': 'E-mail',
                              'type': 'email',
                              'required': true,
                              'value': user.email
                          }) ~ '
                          
                          ' ~ include('components/form/input.html.twig', {
                              'name': 'phone',
                              'label': 'Telefone',
                              'type': 'tel',
                              'value': user.phone|default('')
                          }) ~ '
                          
                          <div class="form-group">
                              <label class="form-label">Bio</label>
                              <textarea class="form-control" name="bio" rows="4" 
placeholder="Conte um pouco sobre você...">{{ user.bio|default('') }}</textarea>
                          </div>
                          
                          <div class="form-group">
                              <label class="form-label">Foto do Perfil</label>
                              <input type="file" class="form-control" name="avatar" 
accept="image/*">
                              <small class="form-text text-muted">Formatos aceitos: JPG, 
PNG. Tamanho máximo: 2MB</small>
                          </div>
                          
                          <div class="d-flex justify-content-between">
                              <a href="/perfil" class="btn btn-secondary">Cancelar</a>
                              <button type="submit" class="btn btn-success">
                                  <i class="fas fa-save me-2"></i>Salvar Alterações
                              </button>
                          </div>
                      </form>
                  '
              } %}
          </div>
          
          <div class="col-md-4">
              {% include 'components/ui/card.html.twig' with {
                  'title': 'Alterar Senha',
                  'body': '
                      <form method="POST" action="/perfil/senha">
                          ' ~ include('components/form/input.html.twig', {
                              'name': 'current_password',
                              'label': 'Senha Atual',
                              'type': 'password',
                              'required': true
                          }) ~ '
                          
                          ' ~ include('components/form/input.html.twig', {
                              'name': 'new_password',
                              'label': 'Nova Senha',
                              'type': 'password',
                              'required': true,
                              'help': 'Mínimo 8 caracteres'
                          }) ~ '
                          
                          ' ~ include('components/form/input.