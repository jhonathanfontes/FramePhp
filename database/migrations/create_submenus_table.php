<?php

use Core\Database\Database;

$db = Database::getInstance();

$db->query("
    CREATE TABLE IF NOT EXISTS submenus (
        id INT AUTO_INCREMENT PRIMARY KEY,
        menu_id INT NOT NULL,
        nome VARCHAR(255) NOT NULL,
        rota VARCHAR(255) NOT NULL,
        ordem INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

echo "Tabela 'submenus' criada com sucesso.\n"; 