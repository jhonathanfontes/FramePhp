
<?php

namespace App\Controllers\Site;

use Core\Controller;
use Core\Session\Session;
use App\Models\CadProdutoModel;

class CartController extends Controller
{
    private $produtoModel;

    public function __construct()
    {
        $this->produtoModel = new CadProdutoModel();
    }

    /**
     * Exibir carrinho
     */
    public function index()
    {
        $carrinho = Session::get('carrinho', []);
        $total = 0;
        $itens = [];

        foreach ($carrinho as $produtoId => $quantidade) {
            $produto = $this->produtoModel->findById($produtoId);
            if ($produto) {
                $subtotal = $produto['pro_preco'] * $quantidade;
                $total += $subtotal;
                
                $itens[] = [
                    'produto' => $produto,
                    'quantidade' => $quantidade,
                    'subtotal' => $subtotal
                ];
            }
        }

        return $this->view('pages/shop/carrinho', [
            'itens' => $itens,
            'total' => $total,
            'title' => 'Carrinho de Compras'
        ]);
    }

    /**
     * Adicionar produto ao carrinho
     */
    public function adicionar()
    {
        $produtoId = $_POST['produto_id'] ?? 0;
        $quantidade = (int)($_POST['quantidade'] ?? 1);

        if ($produtoId && $quantidade > 0) {
            $produto = $this->produtoModel->findById($produtoId);
            
            if ($produto) {
                $carrinho = Session::get('carrinho', []);
                
                if (isset($carrinho[$produtoId])) {
                    $carrinho[$produtoId] += $quantidade;
                } else {
                    $carrinho[$produtoId] = $quantidade;
                }
                
                Session::set('carrinho', $carrinho);
                
                return $this->json([
                    'success' => true,
                    'message' => 'Produto adicionado ao carrinho'
                ]);
            }
        }

        return $this->json([
            'success' => false,
            'message' => 'Erro ao adicionar produto'
        ], 400);
    }

    /**
     * Atualizar quantidade
     */
    public function atualizar()
    {
        $produtoId = $_POST['produto_id'] ?? 0;
        $quantidade = (int)($_POST['quantidade'] ?? 0);

        $carrinho = Session::get('carrinho', []);

        if ($quantidade > 0) {
            $carrinho[$produtoId] = $quantidade;
        } else {
            unset($carrinho[$produtoId]);
        }

        Session::set('carrinho', $carrinho);

        return $this->redirect('/carrinho');
    }

    /**
     * Remover produto do carrinho
     */
    public function remover($produtoId)
    {
        $carrinho = Session::get('carrinho', []);
        unset($carrinho[$produtoId]);
        Session::set('carrinho', $carrinho);

        return $this->redirect('/carrinho');
    }

    /**
     * Limpar carrinho
     */
    public function limpar()
    {
        Session::remove('carrinho');
        return $this->redirect('/carrinho');
    }
}
