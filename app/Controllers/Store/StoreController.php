<?php

namespace App\Controllers\Store;

use Core\Controller\BaseController;
use App\Models\LojaModel;
use App\Models\CadProdutoModel;
use App\Models\CadCategoriaModel;

class StoreController extends BaseController
{
    private $lojaModel;
    private $produtoModel;
    private $categoriaModel;

    public function __construct()
    {
        $this->lojaModel = new LojaModel();
        $this->produtoModel = new CadProdutoModel();
        $this->categoriaModel = new CadCategoriaModel();
    }

    public function index()
    {
        // Detectar loja pelo domínio ou slug
        $domain = $_SERVER['HTTP_HOST'];
        $loja = $this->lojaModel->findByDominio($domain);

        if (!$loja) {
            return $this->render('errors/404');
        }

        // Buscar produtos em destaque
        $produtos = $this->produtoModel->findAllProducts();
        $categorias = $this->categoriaModel->findAllCategorias();

        return $this->render('store/home/index', [
            'loja' => $loja,
            'produtos' => $produtos,
            'categorias' => $categorias
        ]);
    }

    public function categoria($slug = null)
    {
        $domain = $_SERVER['HTTP_HOST'];
        $loja = $this->lojaModel->findByDominio($domain);

        if (!$loja) {
            return $this->render('errors/404');
        }

        // Buscar categoria
        $categoria = $this->categoriaModel->findByDescricao($slug);
        if (!$categoria) {
            return $this->render('errors/404');
        }

        // Buscar produtos da categoria
        $produtos = $this->produtoModel->findAllProducts();

        return $this->render('store/products/category', [
            'loja' => $loja,
            'categoria' => $categoria,
            'produtos' => $produtos
        ]);
    }

    public function produto($id)
    {
        $domain = $_SERVER['HTTP_HOST'];
        $loja = $this->lojaModel->findByDominio($domain);

        if (!$loja) {
            return $this->render('errors/404');
        }

        $produto = $this->produtoModel->findById($id);
        if (!$produto) {
            return $this->render('errors/404');
        }

        return $this->render('store/products/show', [
            'loja' => $loja,
            'produto' => $produto
        ]);
    }

    public function carrinho()
    {
        $domain = $_SERVER['HTTP_HOST'];
        $loja = $this->lojaModel->findByDominio($domain);

        if (!$loja) {
            return $this->render('errors/404');
        }

        return $this->render('store/cart/index', [
            'loja' => $loja
        ]);
    }

    public function checkout()
    {
        $domain = $_SERVER['HTTP_HOST'];
        $loja = $this->lojaModel->findByDominio($domain);

        if (!$loja) {
            return $this->render('errors/404');
        }

        return $this->render('store/checkout/index', [
            'loja' => $loja
        ]);
    }
}
