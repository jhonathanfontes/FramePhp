<?php

// Definir caminho base
define('BASE_PATH', dirname(__DIR__, 2));

// Carregar o autoloader do Composer
require BASE_PATH . '/vendor/autoload.php';

// Carregar variáveis de ambiente
$dotenv = \Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

// Carregar bootstrap
require_once BASE_PATH . '/bootstrap/app.php';

use Core\Database\Database;

// Conectar ao banco de dados
$db = Database::getInstance();

// Dados dos menus principais
$menus = [
    [
        'nome' => 'Dashboard',
        'icone' => 'fas fa-tachometer-alt',
        'ordem' => 1
    ],
    [
        'nome' => 'Usuários',
        'icone' => 'fas fa-users',
        'ordem' => 2
    ],
    [
        'nome' => 'Produtos',
        'icone' => 'fas fa-box',
        'ordem' => 3
    ],
    [
        'nome' => 'Pedidos',
        'icone' => 'fas fa-shopping-cart',
        'ordem' => 4
    ],
    [
        'nome' => 'Relatórios',
        'icone' => 'fas fa-chart-bar',
        'ordem' => 5
    ],
    [
        'nome' => 'Configurações',
        'icone' => 'fas fa-cog',
        'ordem' => 6
    ]
];

// Inserir menus
foreach ($menus as $menu) {
    $db->query(
        "INSERT INTO menus (nome, icone, ordem) VALUES (?, ?, ?)",
        [$menu['nome'], $menu['icone'], $menu['ordem']]
    );
    echo "Menu inserido: {$menu['nome']}\n";
}

// Obter IDs dos menus inseridos
$menuIds = [];
$result = $db->query("SELECT id, nome FROM menus");
while ($row = $result->fetch()) {
    $menuIds[$row['nome']] = $row['id'];
}

// Dados dos submódulos
$submenus = [
    // Dashboard
    [
        'menu_id' => $menuIds['Dashboard'],
        'nome' => 'Visão Geral',
        'rota' => '/dashboard',
        'ordem' => 1
    ],
    
    // Usuários
    [
        'menu_id' => $menuIds['Usuários'],
        'nome' => 'Listar Usuários',
        'rota' => '/admin/users',
        'ordem' => 1
    ],
    [
        'menu_id' => $menuIds['Usuários'],
        'nome' => 'Adicionar Usuário',
        'rota' => '/admin/users/create',
        'ordem' => 2
    ],
    [
        'menu_id' => $menuIds['Usuários'],
        'nome' => 'Permissões',
        'rota' => '/admin/users/permissions',
        'ordem' => 3
    ],
    
    // Produtos
    [
        'menu_id' => $menuIds['Produtos'],
        'nome' => 'Listar Produtos',
        'rota' => '/admin/products',
        'ordem' => 1
    ],
    [
        'menu_id' => $menuIds['Produtos'],
        'nome' => 'Adicionar Produto',
        'rota' => '/admin/products/create',
        'ordem' => 2
    ],
    [
        'menu_id' => $menuIds['Produtos'],
        'nome' => 'Categorias',
        'rota' => '/admin/products/categories',
        'ordem' => 3
    ],
    
    // Pedidos
    [
        'menu_id' => $menuIds['Pedidos'],
        'nome' => 'Listar Pedidos',
        'rota' => '/admin/orders',
        'ordem' => 1
    ],
    [
        'menu_id' => $menuIds['Pedidos'],
        'nome' => 'Novo Pedido',
        'rota' => '/admin/orders/create',
        'ordem' => 2
    ],
    
    // Relatórios
    [
        'menu_id' => $menuIds['Relatórios'],
        'nome' => 'Vendas',
        'rota' => '/admin/reports/sales',
        'ordem' => 1
    ],
    [
        'menu_id' => $menuIds['Relatórios'],
        'nome' => 'Produtos',
        'rota' => '/admin/reports/products',
        'ordem' => 2
    ],
    [
        'menu_id' => $menuIds['Relatórios'],
        'nome' => 'Usuários',
        'rota' => '/admin/reports/users',
        'ordem' => 3
    ],
    
    // Configurações
    [
        'menu_id' => $menuIds['Configurações'],
        'nome' => 'Geral',
        'rota' => '/admin/settings/general',
        'ordem' => 1
    ],
    [
        'menu_id' => $menuIds['Configurações'],
        'nome' => 'Email',
        'rota' => '/admin/settings/email',
        'ordem' => 2
    ],
    [
        'menu_id' => $menuIds['Configurações'],
        'nome' => 'Backup',
        'rota' => '/admin/settings/backup',
        'ordem' => 3
    ]
];

// Inserir submódulos
foreach ($submenus as $submenu) {
    $db->query(
        "INSERT INTO submenus (menu_id, nome, rota, ordem) VALUES (?, ?, ?, ?)",
        [$submenu['menu_id'], $submenu['nome'], $submenu['rota'], $submenu['ordem']]
    );
    echo "Submenu inserido: {$submenu['nome']}\n";
}

echo "\nSeeder de menus concluído com sucesso!\n"; 