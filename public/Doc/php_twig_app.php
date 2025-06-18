<?php
// index.php - Arquivo principal da aplicação
session_start();
require_once 'vendor/autoload.php'; // Assuming Twig is installed via Composer
require_once 'includes/Database.php';
require_once 'includes/Auth.php';
require_once 'includes/ApiData.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Configuração do Twig
$loader = new FilesystemLoader('templates');
$twig = new Environment($loader, [
    'cache' => false, // Desabilitar cache para desenvolvimento
    'debug' => true,
]);

// Instanciar classes
$auth = new Auth();
$apiData = new ApiData();

// Roteamento simples
$page = $_GET['page'] ?? 'login';
$action = $_GET['action'] ?? '';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'login':
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $result = $auth->login($email, $password);
            if ($result['success']) {
                header('Location: index.php?page=dashboard');
                exit;
            } else {
                $error = $result['message'];
            }
            break;
        
        case 'logout':
            $auth->logout();
            header('Location: index.php?page=login');
            exit;
            break;
        
        case 'register':
            $userData = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'role' => $_POST['role'] ?? 'user'
            ];
            $result = $auth->register($userData);
            if ($result['success']) {
                $success = 'Usuário cadastrado com sucesso!';
            } else {
                $error = $result['message'];
            }
            break;
    }
}

// Verificar autenticação para páginas protegidas
$protectedPages = ['dashboard', 'users', 'products', 'reports', 'settings'];
if (in_array($page, $protectedPages) && !$auth->isLoggedIn()) {
    header('Location: index.php?page=login');
    exit;
}

// Preparar dados para as páginas
$data = [
    'user' => $auth->getCurrentUser(),
    'page' => $page,
    'error' => $error ?? null,
    'success' => $success ?? null,
];

// Adicionar dados específicos por página
switch ($page) {
    case 'dashboard':
        $data['stats'] = $apiData->getDashboardStats();
        break;
    case 'users':
        $data['users'] = $apiData->getUsers();
        break;
    case 'products':
        $data['products'] = $apiData->getProducts();
        break;
    case 'reports':
        $data['reports'] = $apiData->getReports();
        break;
}

// Renderizar a página
try {
    echo $twig->render("{$page}.twig", $data);
} catch (Exception $e) {
    echo $twig->render('404.twig', $data);
}
?>

<?php
// includes/Auth.php - Classe de autenticação
class Auth {
    private $users = [
        'admin@example.com' => [
            'id' => 1,
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'role' => 'admin',
            'avatar' => 'https://via.placeholder.com/40x40'
        ],
        'manager@example.com' => [
            'id' => 2,
            'name' => 'Gerente Silva',
            'email' => 'manager@example.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'role' => 'manager',
            'avatar' => 'https://via.placeholder.com/40x40'
        ],
        'user@example.com' => [
            'id' => 3,
            'name' => 'Usuário Comum',
            'email' => 'user@example.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'role' => 'user',
            'avatar' => 'https://via.placeholder.com/40x40'
        ]
    ];
    
    public function login($email, $password) {
        if (isset($this->users[$email])) {
            $user = $this->users[$email];
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user;
                return ['success' => true, 'message' => 'Login realizado com sucesso'];
            }
        }
        return ['success' => false, 'message' => 'Email ou senha incorretos'];
    }
    
    public function logout() {
        session_destroy();
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user']);
    }
    
    public function getCurrentUser() {
        return $_SESSION['user'] ?? null;
    }
    
    public function hasRole($role) {
        $user = $this->getCurrentUser();
        return $user && $user['role'] === $role;
    }
    
    public function hasPermission($permission) {
        $user = $this->getCurrentUser();
        if (!$user) return false;
        
        $permissions = [
            'admin' => ['create', 'read', 'update', 'delete', 'manage_users', 'view_reports'],
            'manager' => ['create', 'read', 'update', 'view_reports'],
            'user' => ['read']
        ];
        
        return in_array($permission, $permissions[$user['role']] ?? []);
    }
    
    public function register($userData) {
        // Simular registro (em produção, salvar no banco)
        if (isset($this->users[$userData['email']])) {
            return ['success' => false, 'message' => 'Email já cadastrado'];
        }
        
        return ['success' => true, 'message' => 'Usuário cadastrado com sucesso'];
    }
}
?>

<?php
// includes/ApiData.php - Classe para dados fake (simulando API)
class ApiData {
    public function getDashboardStats() {
        return [
            'total_users' => 1250,
            'total_products' => 89,
            'total_orders' => 456,
            'total_revenue' => 'R$ 123.456,78',
            'recent_orders' => [
                ['id' => 1001, 'customer' => 'João Silva', 'value' => 'R$ 299,90', 'status' => 'pending'],
                ['id' => 1002, 'customer' => 'Maria Santos', 'value' => 'R$ 159,50', 'status' => 'completed'],
                ['id' => 1003, 'customer' => 'Carlos Oliveira', 'value' => 'R$ 89,90', 'status' => 'processing'],
            ]
        ];
    }
    
    public function getUsers() {
        return [
            ['id' => 1, 'name' => 'João Silva', 'email' => 'joao@example.com', 'role' => 'user', 'status' => 'active', 'created_at' => '2024-01-15'],
            ['id' => 2, 'name' => 'Maria Santos', 'email' => 'maria@example.com', 'role' => 'manager', 'status' => 'active', 'created_at' => '2024-02-20'],
            ['id' => 3, 'name' => 'Carlos Oliveira', 'email' => 'carlos@example.com', 'role' => 'user', 'status' => 'inactive', 'created_at' => '2024-03-10'],
            ['id' => 4, 'name' => 'Ana Costa', 'email' => 'ana@example.com', 'role' => 'admin', 'status' => 'active', 'created_at' => '2024-01-05'],
        ];
    }
    
    public function getProducts() {
        return [
            ['id' => 1, 'name' => 'Smartphone XYZ', 'price' => 'R$ 1.299,90', 'stock' => 25, 'category' => 'Eletrônicos', 'status' => 'active'],
            ['id' => 2, 'name' => 'Notebook ABC', 'price' => 'R$ 2.599,90', 'stock' => 8, 'category' => 'Informática', 'status' => 'active'],
            ['id' => 3, 'name' => 'Fone Bluetooth', 'price' => 'R$ 199,90', 'stock' => 0, 'category' => 'Acessórios', 'status' => 'inactive'],
            ['id' => 4, 'name' => 'Tablet Pro', 'price' => 'R$ 899,90', 'stock' => 12, 'category' => 'Eletrônicos', 'status' => 'active'],
        ];
    }
    
    public function getReports() {
        return [
            ['title' => 'Vendas Mensais', 'type' => 'sales', 'date' => '2024-06-01', 'value' => 'R$ 45.678,90'],
            ['title' => 'Relatório de Usuários', 'type' => 'users', 'date' => '2024-06-01', 'value' => '1.250 usuários'],
            ['title' => 'Produtos Mais Vendidos', 'type' => 'products', 'date' => '2024-06-01', 'value' => '89 produtos'],
            ['title' => 'Análise de Desempenho', 'type' => 'performance', 'date' => '2024-06-01', 'value' => '95% uptime'],
        ];
    }
}
?>

<!-- templates/base.twig - Template base -->
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{% block title %}Sistema de Gestão{% endblock %}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
        }
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
        }
        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-2px);
        }
        .login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .login-card {
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.95);
        }
    </style>
</head>
<body>
    {% block body %}{% endblock %}
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    {% block scripts %}{% endblock %}
</body>
</html>

<!-- templates/login.twig - Página de login -->
{% extends "base.twig" %}

{% block title %}Login - Sistema de Gestão{% endblock %}

{% block body %}
<div class="login-container d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-circle fa-3x text-primary mb-3"></i>
                            <h3 class="card-title">Sistema de Gestão</h3>
                            <p class="text-muted">Faça login para continuar</p>
                        </div>
                        
                        {% if error %}
                            <div class="alert alert-danger" role="alert">
                                {{ error }}
                            </div>
                        {% endif %}
                        
                        <form method="POST" action="index.php?action=login">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">Senha</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Entrar
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <small class="text-muted">
                                Usuários de teste:<br>
                                <strong>admin@example.com</strong> (Admin)<br>
                                <strong>manager@example.com</strong> (Gerente)<br>
                                <strong>user@example.com</strong> (Usuário)<br>
                                Senha: <strong>password</strong>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{% endblock %}

<!-- templates/layout.twig - Layout interno da aplicação -->
{% extends "base.twig" %}

{% block body %}
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar">
            <div class="p-3">
                <h5 class="text-white mb-4">
                    <i class="fas fa-chart-line me-2"></i>
                    Sistema
                </h5>
                
                <!-- User Info -->
                <div class="card bg-transparent border-light mb-4">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <img src="{{ user.avatar }}" class="rounded-circle me-2" width="40" height="40">
                            <div>
                                <small class="text-white d-block">{{ user.name }}</small>
                                <small class="text-light">{{ user.role|title }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Menu -->
                <nav class="nav flex-column">
                    <a class="nav-link {% if page == 'dashboard' %}active{% endif %}" href="index.php?page=dashboard">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    
                    {% if user.role in ['admin', 'manager'] %}
                        <a class="nav-link {% if page == 'users' %}active{% endif %}" href="index.php?page=users">
                            <i class="fas fa-users me-2"></i>Usuários
                        </a>
                    {% endif %}
                    
                    <a class="nav-link {% if page == 'products' %}active{% endif %}" href="index.php?page=products">
                        <i class="fas fa-box me-2"></i>Produtos
                    </a>
                    
                    {% if user.role == 'admin' %}
                        <a class="nav-link {% if page == 'reports' %}active{% endif %}" href="index.php?page=reports">
                            <i class="fas fa-chart-bar me-2"></i>Relatórios
                        </a>
                        
                        <a class="nav-link {% if page == 'settings' %}active{% endif %}" href="index.php?page=settings">
                            <i class="fas fa-cog me-2"></i>Configurações
                        </a>
                    {% endif %}
                    
                    <hr class="my-3" style="border-color: rgba(255,255,255,0.3);">
                    
                    <a class="nav-link" href="index.php?action=logout">
                        <i class="fas fa-sign-out-alt me-2"></i>Sair
                    </a>
                </nav>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="p-4">
                {% block content %}{% endblock %}
            </div>
        </div>
    </div>
</div>
{% endblock %}

<!-- templates/dashboard.twig - Dashboard -->
{% extends "layout.twig" %}

{% block title %}Dashboard - Sistema de Gestão{% endblock %}

{% block content %}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Dashboard</h1>
    <span class="badge bg-primary">{{ user.role|title }}</span>
</div>

<!-- Cards de Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-users fa-2x text-primary mb-3"></i>
                <h3>{{ stats.total_users }}</h3>
                <p class="text-muted">Total de Usuários</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-box fa-2x text-success mb-3"></i>
                <h3>{{ stats.total_products }}</h3>
                <p class="text-muted">Produtos</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-shopping-cart fa-2x text-warning mb-3"></i>
                <h3>{{ stats.total_orders }}</h3>
                <p class="text-muted">Pedidos</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-dollar-sign fa-2x text-info mb-3"></i>
                <h3>{{ stats.total_revenue }}</h3>
                <p class="text-muted">Receita</p>
            </div>
        </div>
    </div>
</div>

<!-- Pedidos Recentes -->
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-clock me-2"></i>Pedidos Recentes</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Valor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    {% for order in stats.recent_orders %}
                    <tr>
                        <td>#{{ order.id }}</td>
                        <td>{{ order.customer }}</td>
                        <td>{{ order.value }}</td>
                        <td>
                            {% if order.status == 'pending' %}
                                <span class="badge bg-warning">Pendente</span>
                            {% elseif order.status == 'completed' %}
                                <span class="badge bg-success">Concluído</span>
                            {% else %}
                                <span class="badge bg-info">Processando</span>
                            {% endif %}
                        </td>
                    </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </div>
</div>
{% endblock %}

<!-- templates/users.twig - Gestão de usuários -->
{% extends "layout.twig" %}

{% block title %}Usuários - Sistema de Gestão{% endblock %}

{% block content %}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Gestão de Usuários</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="fas fa-plus me-2"></i>Novo Usuário
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Função</th>
                        <th>Status</th>
                        <th>Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    {% for user_item in users %}
                    <tr>
                        <td>{{ user_item.id }}</td>
                        <td>{{ user_item.name }}</td>
                        <td>{{ user_item.email }}</td>
                        <td>
                            {% if user_item.role == 'admin' %}
                                <span class="badge bg-danger">Admin</span>
                            {% elseif user_item.role == 'manager' %}
                                <span class="badge bg-warning">Gerente</span>
                            {% else %}
                                <span class="badge bg-info">Usuário</span>
                            {% endif %}
                        </td>
                        <td>
                            <span class="badge bg-{{ user_item.status == 'active' ? 'success' : 'secondary' }}">
                                {{ user_item.status == 'active' ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td>{{ user_item.created_at }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Adicionar Usuário -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=register">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="product_price" class="form-label">Preço</label>
                        <input type="number" step="0.01" class="form-control" id="product_price" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="product_stock" class="form-label">Estoque</label>
                        <input type="number" class="form-control" id="product_stock" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="product_category" class="form-label">Categoria</label>
                        <select class="form-control" id="product_category" required>
                            <option value="">Selecione uma categoria</option>
                            <option value="Eletrônicos">Eletrônicos</option>
                            <option value="Informática">Informática</option>
                            <option value="Acessórios">Acessórios</option>
                            <option value="Casa">Casa</option>
                            <option value="Roupas">Roupas</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
{% endif %}
{% endblock %}

<!-- templates/reports.twig - Relatórios (apenas admin) -->
{% extends "layout.twig" %}

{% block title %}Relatórios - Sistema de Gestão{% endblock %}

{% block content %}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Relatórios</h1>
    <button class="btn btn-success">
        <i class="fas fa-download me-2"></i>Exportar
    </button>
</div>

<div class="row">
    {% for report in reports %}
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card">
            <div class="card-body text-center">
                {% if report.type == 'sales' %}
                    <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                {% elseif report.type == 'users' %}
                    <i class="fas fa-users fa-3x text-primary mb-3"></i>
                {% elseif report.type == 'products' %}
                    <i class="fas fa-box fa-3x text-warning mb-3"></i>
                {% else %}
                    <i class="fas fa-chart-bar fa-3x text-info mb-3"></i>
                {% endif %}
                
                <h5>{{ report.title }}</h5>
                <p class="text-muted">{{ report.date }}</p>
                <h4 class="text-primary">{{ report.value }}</h4>
                
                <button class="btn btn-outline-primary btn-sm mt-2">
                    <i class="fas fa-eye me-1"></i>Visualizar
                </button>
            </div>
        </div>
    </div>
    {% endfor %}
</div>

<!-- Gráfico de exemplo -->
<div class="card mt-4">
    <div class="card-header">
        <h5><i class="fas fa-chart-area me-2"></i>Vendas dos Últimos 12 Meses</h5>
    </div>
    <div class="card-body">
        <canvas id="salesChart" height="100"></canvas>
    </div>
</div>
{% endblock %}

{% block scripts %}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de vendas
const ctx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
        datasets: [{
            label: 'Vendas (R$)',
            data: [12000, 19000, 15000, 25000, 22000, 30000, 28000, 32000, 27000, 35000, 40000, 45000],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'R$ ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
{% endblock %}

<!-- templates/settings.twig - Configurações (apenas admin) -->
{% extends "layout.twig" %}

{% block title %}Configurações - Sistema de Gestão{% endblock %}

{% block content %}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Configurações do Sistema</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-cog me-2"></i>Configurações Gerais</h5>
            </div>
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <label for="site_name" class="form-label">Nome do Sistema</label>
                        <input type="text" class="form-control" id="site_name" value="Sistema de Gestão">
                    </div>
                    
                    <div class="mb-3">
                        <label for="site_email" class="form-label">Email do Sistema</label>
                        <input type="email" class="form-control" id="site_email" value="admin@sistema.com">
                    </div>
                    
                    <div class="mb-3">
                        <label for="timezone" class="form-label">Fuso Horário</label>
                        <select class="form-control" id="timezone">
                            <option value="America/Sao_Paulo" selected>America/São Paulo</option>
                            <option value="America/New_York">America/New York</option>
                            <option value="Europe/London">Europe/London</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="maintenance_mode">
                            <label class="form-check-label" for="maintenance_mode">
                                Modo de Manutenção
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="email_notifications" checked>
                            <label class="form-check-label" for="email_notifications">
                                Notificações por Email
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Salvar Configurações
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>Informações do Sistema</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-6">Versão:</dt>
                    <dd class="col-sm-6">1.0.0</dd>
                    
                    <dt class="col-sm-6">PHP:</dt>
                    <dd class="col-sm-6">{{ constant('PHP_VERSION') }}</dd>
                    
                    <dt class="col-sm-6">Servidor:</dt>
                    <dd class="col-sm-6">{{ _SERVER['SERVER_SOFTWARE']|default('N/A') }}</dd>
                    
                    <dt class="col-sm-6">Uptime:</dt>
                    <dd class="col-sm-6">15 dias</dd>
                </dl>
                
                <hr>
                
                <div class="text-center">
                    <button class="btn btn-warning btn-sm">
                        <i class="fas fa-sync-alt me-1"></i>Limpar Cache
                    </button>
                    <button class="btn btn-info btn-sm">
                        <i class="fas fa-database me-1"></i>Backup
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-chart-pie me-2"></i>Uso de Recursos</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Uso de CPU</label>
                    <div class="progress">
                        <div class="progress-bar bg-success" style="width: 25%">25%</div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Uso de Memória</label>
                    <div class="progress">
                        <div class="progress-bar bg-warning" style="width: 60%">60%</div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Espaço em Disco</label>
                    <div class="progress">
                        <div class="progress-bar bg-danger" style="width: 80%">80%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{% endblock %}

<!-- templates/404.twig - Página de erro 404 -->
{% extends "base.twig" %}

{% block title %}Página não encontrada{% endblock %}

{% block body %}
<div class="container-fluid vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="text-center">
        <i class="fas fa-exclamation-triangle fa-5x text-warning mb-4"></i>
        <h1 class="display-1 fw-bold">404</h1>
        <h2 class="mb-4">Página não encontrada</h2>
        <p class="lead mb-4">A página que você está procurando não existe ou foi movida.</p>
        <a href="index.php?page=dashboard" class="btn btn-primary">
            <i class="fas fa-home me-2"></i>Voltar ao Dashboard
        </a>
    </div>
</div>
{% endblock %}

<!-- .htaccess - Configuração do Apache (opcional) -->
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Segurança
<Files "*.twig">
    Order Allow,Deny
    Deny from all
</Files>

<Files "includes/*.php">
    Order Allow,Deny
    Deny from all
</Files>

# Compressão
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/x-javascript
</IfModule>

# Cache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
</IfModule>

<!-- composer.json - Dependências do projeto -->
{
    "name": "sistema-gestao/app",
    "description": "Sistema de Gestão com PHP e Twig",
    "type": "project",
    "require": {
        "php": ">=7.4",
        "twig/twig": "^3.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    },
    "require-dev": {
        "symfony/var-dumper": "^5.0"
    }
}

<!-- README.md - Documentação do projeto -->
# Sistema de Gestão com PHP e Twig

## Descrição
Sistema completo de gestão desenvolvido em PHP com templates Twig, incluindo:
- Sistema de autenticação com níveis de acesso
- Dashboard com estatísticas
- Gestão de usuários e produtos
- Relatórios (apenas para admin)
- Interface responsiva com Bootstrap

## Estrutura do Projeto
```
projeto/
├── index.php                 # Arquivo principal
├── includes/
│   ├── Auth.php              # Classe de autenticação
│   ├── ApiData.php           # Dados fake (simula API)
│   └── Database.php          # Conexão com banco (futuro)
├── templates/
│   ├── base.twig             # Template base
│   ├── layout.twig           # Layout interno
│   ├── login.twig            # Página de login
│   ├── dashboard.twig        # Dashboard
│   ├── users.twig            # Gestão de usuários
│   ├── products.twig         # Gestão de produtos
│   ├── reports.twig          # Relatórios
│   ├── settings.twig         # Configurações
│   └── 404.twig              # Página de erro
├── composer.json             # Dependências
├── .htaccess                 # Configuração Apache
└── README.md                 # Documentação
```

## Instalação

1. **Instalar dependências:**
```bash
composer install
```

2. **Configurar servidor web** (Apache/Nginx) apontando para a pasta do projeto

3. **Usuários de teste:**
   - **Admin:** admin@example.com / password
   - **Gerente:** manager@example.com / password  
   - **Usuário:** user@example.com / password

## Funcionalidades

### Níveis de Acesso
- **Admin:** Acesso total (usuários, produtos, relatórios, configurações)
- **Gerente:** Gestão de usuários e produtos
- **Usuário:** Apenas visualização de produtos

### Componentes Twig
- **base.twig:** Template base com Bootstrap e FontAwesome
- **layout.twig:** Layout interno com sidebar e menu dinâmico
- **Componentes modulares:** Cards, formulários, tabelas

### Características
- Interface responsiva
- Menu com controle de acesso
- Formulários com validação
- Dados fake para demonstração
- Sistema de notificações
- Gráficos interativos (Chart.js)

## Próximos Passos
1. Implementar conexão real com banco de dados
2. Adicionar validações do lado servidor
3. Implementar CRUD completo
4. Adicionar sistema de upload de arquivos
5. Implementar logs de auditoria="name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Função</label>
                        <select class="form-control" id="role" name="role">
                            <option value="user">Usuário</option>
                            <option value="manager">Gerente</option>
                            {% if user.role == 'admin' %}
                                <option value="admin">Administrador</option>
                            {% endif %}
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
{% endblock %}

<!-- templates/products.twig - Gestão de produtos -->
{% extends "layout.twig" %}

{% block title %}Produtos - Sistema de Gestão{% endblock %}

{% block content %}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Gestão de Produtos</h1>
    {% if user.role in ['admin', 'manager'] %}
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="fas fa-plus me-2"></i>Novo Produto
        </button>
    {% endif %}
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Estoque</th>
                        <th>Categoria</th>
                        <th>Status</th>
                        {% if user.role in ['admin', 'manager'] %}
                            <th>Ações</th>
                        {% endif %}
                    </tr>
                </thead>
                <tbody>
                    {% for product in products %}
                    <tr>
                        <td>{{ product.id }}</td>
                        <td>{{ product.name }}</td>
                        <td>{{ product.price }}</td>
                        <td>
                            {% if product.stock > 0 %}
                                <span class="badge bg-success">{{ product.stock }}</span>
                            {% else %}
                                <span class="badge bg-danger">Sem estoque</span>
                            {% endif %}
                        </td>
                        <td>{{ product.category }}</td>
                        <td>
                            <span class="badge bg-{{ product.status == 'active' ? 'success' : 'secondary' }}">
                                {{ product.status == 'active' ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        {% if user.role in ['admin', 'manager'] %}
                            <td>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        {% endif %}
                    </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Adicionar Produto -->
{% if user.role in ['admin', 'manager'] %}
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Produto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Nome do Produto</label>
                        <input type="text" class="form-control" id="product_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for