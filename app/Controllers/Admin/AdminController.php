<?php

namespace App\Controllers\Admin;

use App\Lib\TableBuilder;
use App\Models\CadUsuarioModel;
use Core\Controller\BaseController;

class AdminController extends BaseController
{
    public function dashboard()
    {
        return $this->render('templates/dashboard');
    }

  
    public function settings()
    {
        return $this->render('templates/settings');
    }

    public function reports()
    {
        return $this->render('templates/reports');
    }

    public function orders()
    {
        return $this->render('templates/orders');
    }

    public function products()
    {
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

        $buscaParam = $_GET['b'] ?? null;
        $produtosFiltrados = $produtos; // Começa com todos os produtos

        if (!empty($buscaParam)) {
            // Converte o termo de busca para minúsculas
            $searchTerm = strtolower($buscaParam);

            // Filtra os produtos
            $produtosFiltrados = array_filter($produtos, function ($produto) use ($searchTerm) {
                // Converte todos os valores da linha para string e minúsculas para buscar
                foreach ($produto as $key => $value) {
                    // Podemos buscar por 'name' e 'category' principalmente
                    if (in_array($key, ['name', 'category', 'description'])) { // Adicione outras chaves relevantes para busca
                        if (str_contains(strtolower((string) $value), $searchTerm)) {
                            return true; // Encontrou o termo de busca nesta linha
                        }
                    }
                }
                return false; // Não encontrou o termo nesta linha
            });
            // Reindexar o array após o filtro, para evitar problemas com índices no loop
            $produtos = array_values($produtosFiltrados);
        }

        // Lógica para a tabela de produtos usando TableBuilder (HTML puro)
        $table = new TableBuilder();
        $table->setQtd(10); // 5 itens por página
        $table->setHasCheckbox(true); // Ativa a coluna de checkbox
        $table->setHasImage(true); // Ativa a renderização de imagem para a última coluna (ou específica)
        $table->init(
            data: $produtos, // Dados completos dos produtos
            headers: array("Nome", "Categoria", "Preço", "Estoque", "Status", "Ações"), // Headers
            getParams: array("b" => $this->getParams("b")) // Parâmetros GET para busca
        );

        foreach ($produtos as $key => $item) {
            if ($table->checkPagination($key)) { // Verifica se o item está na página atual
                $statusLabel = ''; // Lógica de formatação de status
                $statusClass = '';
                switch ($item['status']) {
                    case 'available':
                        $statusLabel = 'Disponível';
                        $statusClass = 'is-success';
                        break;
                    case 'out_of_stock':
                        $statusLabel = 'Esgotado';
                        $statusClass = 'is-danger';
                        break;
                    case 'discontinued':
                        $statusLabel = 'Descontinuado';
                        $statusClass = 'is-light';
                        break;
                    default:
                        $statusLabel = $item['status'];
                        $statusClass = 'is-light';
                        break;
                }
                $formattedStatus = "<span class='tag {$statusClass}'>{$statusLabel}</span>";

                $table->addCol($item['id']) // Valor para o checkbox
                    ->addCol($item['name'])
                    ->addCol($item['category'])
                    ->addCol("R$ " . number_format($item['price'], 2, ',', '.')) // Preço formatado
                    ->addCol($item['stock'])
                    ->addCol($formattedStatus) // Status formatado
                    ->addCol("<a href=products/'" . $item['id'] . "/edit' class='button is-small is-info is-light'><span class='fas fa-edit'></span> Editar</a>") // Botão de exemplo
                    ->addRow('', 'addlinha(' . $item['id'] . ')'); // Adiciona a linha
            }
        }
        return $this->render('templates/products', [
            'productTableHtml' => $table->render(),
            'busca_param' => $buscaParam
        ]);
    }

    public function profile()
    {
        return $this->render('templates/profile');
    }

    public function teste()
    {
        return $this->render('loja/checkout');
    }
}