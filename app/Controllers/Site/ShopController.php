
<?php

namespace App\Controllers\Site;

use Core\Controller;
use App\Models\CadProdutoModel;
use App\Models\CadCategoriaModel;

class ShopController extends Controller
{
    private $produtoModel;
    private $categoriaModel;

    public function __construct()
    {
        $this->produtoModel = new CadProdutoModel();
        $this->categoriaModel = new CadCategoriaModel();
    }

    /**
     * Página principal da loja
     */
    public function index()
    {
        $produtos = $this->produtoModel->findAllProdutos();
        $categorias = $this->categoriaModel->findAllCategorias();

        return $this->view('pages/shop/index', [
            'produtos' => $produtos,
            'categorias' => $categorias,
            'title' => 'Loja Online'
        ]);
    }

    /**
     * Página de produtos por categoria
     */
    public function categoria($id)
    {
        $categoria = $this->categoriaModel->findById($id);
        if (!$categoria) {
            return $this->redirect('/shop');
        }

        $produtos = $this->produtoModel->findByCategoria($id);

        return $this->view('pages/shop/categoria', [
            'produtos' => $produtos,
            'categoria' => $categoria,
            'title' => 'Categoria: ' . $categoria['cat_nome']
        ]);
    }

    /**
     * Página de detalhes do produto
     */
    public function produto($id)
    {
        $produto = $this->produtoModel->findById($id);
        if (!$produto) {
            return $this->redirect('/shop');
        }

        $produtosRelacionados = $this->produtoModel->findByCategoria($produto['categoria_id'], 4);

        return $this->view('pages/shop/produto', [
            'produto' => $produto,
            'produtos_relacionados' => $produtosRelacionados,
            'title' => $produto['pro_nome']
        ]);
    }

    /**
     * Buscar produtos
     */
    public function buscar()
    {
        $termo = $_GET['q'] ?? '';
        $produtos = [];

        if (!empty($termo)) {
            $produtos = $this->produtoModel->buscarProdutos($termo);
        }

        return $this->view('pages/shop/busca', [
            'produtos' => $produtos,
            'termo' => $termo,
            'title' => 'Busca: ' . $termo
        ]);
    }
}
