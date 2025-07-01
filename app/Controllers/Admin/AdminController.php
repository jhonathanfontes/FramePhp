<?php

namespace App\Controllers\Admin;  

use App\Models\User;
use Core\Controller\BaseController;

class AdminController extends BaseController
{
    public function dashboard()
    {
        return $this->render('templates/dashboard');
    }

    public function users()
    {
        $usuarios = new User();
        $data = [
            'usuarios' => $usuarios->findAll(),
        ];

         return $this->render('templates/users', $data);
    }

    public function settings()
    {
        return $this->render('templates/settings');
    }

    public function reports(){
        return $this->render('templates/reports');
    }

    public function orders(){
        return $this->render('templates/orders');
    }
    
    public function products(){
        $produtos = [
    ['id' => 1, 'name' => 'Smartphone X', 'category' => 'eletronicos', 'price' => 1299.99, 'stock' => 150, 'status' => 'available', 'actions' => true],
    ['id' => 2, 'name' => 'Fones Bluetooth', 'category' => 'eletronicos', 'price' => 199.50, 'stock' => 500, 'status' => 'available', 'actions' => true],
    ['id' => 3, 'name' => 'Camiseta Algodão', 'category' => 'roupas', 'price' => 59.90, 'stock' => 300, 'status' => 'available', 'actions' => true],
    ['id' => 4, 'name' => 'Calça Jeans Skinny', 'category' => 'roupas', 'price' => 120.00, 'stock' => 0, 'status' => 'out_of_stock', 'actions' => true],
    ['id' => 5, 'name' => 'Arroz Integral 5kg', 'category' => 'alimentos', 'price' => 25.00, 'stock' => 20, 'status' => 'available', 'actions' => true],
    ['id' => 6, 'name' => 'Feijão Carioca 1kg', 'category' => 'alimentos', 'price' => 8.99, 'stock' => 0, 'status' => 'out_of_stock', 'actions' => true],
    ['id' => 7, 'name' => 'Livro: O Senhor dos Anéis', 'category' => 'livros', 'price' => 85.00, 'stock' => 75, 'status' => 'available', 'actions' => true],
    ['id' => 8, 'name' => 'Mouse Gamer', 'category' => 'eletronicos', 'price' => 180.00, 'stock' => 0, 'status' => 'out_of_stock', 'actions' => true],
    ['id' => 9, 'name' => 'Monitor Ultrawide', 'category' => 'eletronicos', 'price' => 2500.00, 'stock' => 10, 'status' => 'available', 'actions' => true],
    ['id' => 10, 'name' => 'Jaqueta de Couro', 'category' => 'roupas', 'price' => 350.00, 'stock' => 5, 'status' => 'available', 'actions' => true],
    ['id' => 11, 'name' => 'Refrigerante Cola 2L', 'category' => 'alimentos', 'price' => 7.50, 'stock' => 120, 'status' => 'available', 'actions' => true],
    ['id' => 12, 'name' => 'Bicicleta MTB', 'category' => 'outros', 'price' => 800.00, 'stock' => 3, 'status' => 'available', 'actions' => true],
    ['id' => 13, 'name' => 'TV 4K Smart', 'category' => 'eletronicos', 'price' => 3500.00, 'stock' => 0, 'status' => 'out_of_stock', 'actions' => true],
    ['id' => 14, 'name' => 'Fone de Ouvido Sem Fio', 'category' => 'eletronicos', 'price' => 450.00, 'stock' => 200, 'status' => 'available', 'actions' => true],
    ['id' => 15, 'name' => 'Vestido Floral', 'category' => 'roupas', 'price' => 95.00, 'stock' => 50, 'status' => 'available', 'actions' => true],
    ['id' => 16, 'name' => 'Cadeira Ergonômica', 'category' => 'outros', 'price' => 600.00, 'stock' => 15, 'status' => 'available', 'actions' => true],
    ['id' => 17, 'name' => 'Impressora Multifuncional', 'category' => 'eletronicos', 'price' => 720.00, 'stock' => 0, 'status' => 'discontinued', 'actions' => true],
];
        return $this->render('templates/products', ['produtos' => $produtos]);
    }
    
}