
<?php

namespace App\Controllers\Site;

use Core\Controller;
use App\Models\CadProdutoModel;
use App\Models\CadCategoriaModel;
use App\Models\PedidoModel;
use Core\Session\Session;

class ClientController extends Controller
{
    private $produtoModel;
    private $categoriaModel;
    private $pedidoModel;

    public function __construct()
    {
        $this->produtoModel = new CadProdutoModel();
        $this->categoriaModel = new CadCategoriaModel();
        $this->pedidoModel = new PedidoModel();
    }

    /**
     * Página inicial da loja para clientes
     */
    public function index()
    {
        $produtosDestaque = $this->produtoModel->findEmDestaque(8);
        $produtosPromocao = $this->produtoModel->findEmPromocao(6);
        $categorias = $this->categoriaModel->findAllCategorias();

        return $this->view('pages/client/home/index', [
            'produtos_destaque' => $produtosDestaque,
            'produtos_promocao' => $produtosPromocao,
            'categorias' => $categorias,
            'title' => 'Bem-vindo à nossa loja'
        ]);
    }

    /**
     * Catálogo completo de produtos
     */
    public function catalogo()
    {
        $categoriaId = $_GET['categoria'] ?? null;
        $busca = $_GET['busca'] ?? '';
        $ordenacao = $_GET['ordenacao'] ?? 'nome';
        $pagina = (int)($_GET['pagina'] ?? 1);
        $porPagina = 12;

        if (!empty($busca)) {
            $produtos = $this->produtoModel->buscarProdutos($busca);
        } elseif ($categoriaId) {
            $produtos = $this->produtoModel->findByCategoria($categoriaId);
        } else {
            $produtos = $this->produtoModel->findAllProdutos();
        }

        // Ordenação
        $produtos = $this->ordenarProdutos($produtos, $ordenacao);

        // Paginação
        $totalProdutos = count($produtos);
        $produtos = array_slice($produtos, ($pagina - 1) * $porPagina, $porPagina);

        $categorias = $this->categoriaModel->findAllCategorias();

        return $this->view('pages/client/catalogo/index', [
            'produtos' => $produtos,
            'categorias' => $categorias,
            'categoria_atual' => $categoriaId,
            'busca_atual' => $busca,
            'ordenacao_atual' => $ordenacao,
            'pagina_atual' => $pagina,
            'total_produtos' => $totalProdutos,
            'total_paginas' => ceil($totalProdutos / $porPagina),
            'title' => 'Catálogo de Produtos'
        ]);
    }

    /**
     * Detalhes do produto
     */
    public function produto($id)
    {
        $produto = $this->produtoModel->findById($id);
        if (!$produto) {
            return $this->redirect('/client/catalogo');
        }

        $produtosRelacionados = $this->produtoModel->findByCategoria($produto['categoria_id'], 4);

        return $this->view('pages/client/produto/detalhes', [
            'produto' => $produto,
            'produtos_relacionados' => $produtosRelacionados,
            'title' => $produto['pro_nome']
        ]);
    }

    /**
     * Carrinho de compras
     */
    public function carrinho()
    {
        $carrinho = Session::get('carrinho', []);
        $itensCarrinho = [];
        $total = 0;

        foreach ($carrinho as $produtoId => $quantidade) {
            $produto = $this->produtoModel->findById($produtoId);
            if ($produto) {
                $subtotal = $produto['pro_preco'] * $quantidade;
                $itensCarrinho[] = [
                    'produto' => $produto,
                    'quantidade' => $quantidade,
                    'subtotal' => $subtotal
                ];
                $total += $subtotal;
            }
        }

        return $this->view('pages/client/carrinho/index', [
            'itens_carrinho' => $itensCarrinho,
            'total' => $total,
            'title' => 'Carrinho de Compras'
        ]);
    }

    /**
     * Adicionar produto ao carrinho
     */
    public function adicionarCarrinho()
    {
        $produtoId = $_POST['produto_id'] ?? null;
        $quantidade = (int)($_POST['quantidade'] ?? 1);

        if (!$produtoId || $quantidade <= 0) {
            return $this->json(['success' => false, 'message' => 'Dados inválidos']);
        }

        $produto = $this->produtoModel->findById($produtoId);
        if (!$produto) {
            return $this->json(['success' => false, 'message' => 'Produto não encontrado']);
        }

        $carrinho = Session::get('carrinho', []);
        
        if (isset($carrinho[$produtoId])) {
            $carrinho[$produtoId] += $quantidade;
        } else {
            $carrinho[$produtoId] = $quantidade;
        }

        Session::set('carrinho', $carrinho);

        return $this->json(['success' => true, 'message' => 'Produto adicionado ao carrinho']);
    }

    /**
     * Atualizar quantidade no carrinho
     */
    public function atualizarCarrinho()
    {
        $produtoId = $_POST['produto_id'] ?? null;
        $quantidade = (int)($_POST['quantidade'] ?? 0);

        if (!$produtoId) {
            return $this->json(['success' => false, 'message' => 'Produto não especificado']);
        }

        $carrinho = Session::get('carrinho', []);

        if ($quantidade <= 0) {
            unset($carrinho[$produtoId]);
        } else {
            $carrinho[$produtoId] = $quantidade;
        }

        Session::set('carrinho', $carrinho);

        return $this->json(['success' => true, 'message' => 'Carrinho atualizado']);
    }

    /**
     * Remover produto do carrinho
     */
    public function removerCarrinho($produtoId)
    {
        $carrinho = Session::get('carrinho', []);
        unset($carrinho[$produtoId]);
        Session::set('carrinho', $carrinho);

        return $this->redirect('/client/carrinho');
    }

    /**
     * Checkout
     */
    public function checkout()
    {
        $carrinho = Session::get('carrinho', []);
        if (empty($carrinho)) {
            return $this->redirect('/client/carrinho');
        }

        $itensCarrinho = [];
        $total = 0;

        foreach ($carrinho as $produtoId => $quantidade) {
            $produto = $this->produtoModel->findById($produtoId);
            if ($produto) {
                $subtotal = $produto['pro_preco'] * $quantidade;
                $itensCarrinho[] = [
                    'produto' => $produto,
                    'quantidade' => $quantidade,
                    'subtotal' => $subtotal
                ];
                $total += $subtotal;
            }
        }

        return $this->view('pages/client/checkout/index', [
            'itens_carrinho' => $itensCarrinho,
            'total' => $total,
            'title' => 'Finalizar Compra'
        ]);
    }

    /**
     * Processar checkout
     */
    public function processarCheckout()
    {
        // Implementar lógica de processamento do pedido
        $dados = $_POST;
        $carrinho = Session::get('carrinho', []);

        if (empty($carrinho)) {
            return $this->json(['success' => false, 'message' => 'Carrinho vazio']);
        }

        // Aqui você implementaria a lógica de criação do pedido
        // Por enquanto, vamos apenas limpar o carrinho
        Session::remove('carrinho');

        return $this->json(['success' => true, 'message' => 'Pedido realizado com sucesso!']);
    }

    /**
     * Minha conta
     */
    public function conta()
    {
        // Verificar se usuário está logado
        $userId = Session::get('user_id');
        if (!$userId) {
            return $this->redirect('/auth/login');
        }

        return $this->view('pages/client/conta/index', [
            'title' => 'Minha Conta'
        ]);
    }

    /**
     * Meus pedidos
     */
    public function pedidos()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return $this->redirect('/auth/login');
        }

        $pedidos = $this->pedidoModel->findByUserId($userId);

        return $this->view('pages/client/conta/pedidos', [
            'pedidos' => $pedidos,
            'title' => 'Meus Pedidos'
        ]);
    }

    /**
     * Detalhes do pedido
     */
    public function pedido($id)
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return $this->redirect('/auth/login');
        }

        $pedido = $this->pedidoModel->findById($id);
        if (!$pedido || $pedido['user_id'] != $userId) {
            return $this->redirect('/client/conta/pedidos');
        }

        return $this->view('pages/client/conta/pedido', [
            'pedido' => $pedido,
            'title' => 'Pedido #' . $pedido['id']
        ]);
    }

    private function ordenarProdutos($produtos, $ordenacao)
    {
        switch ($ordenacao) {
            case 'preco_asc':
                usort($produtos, fn($a, $b) => $a['pro_preco'] <=> $b['pro_preco']);
                break;
            case 'preco_desc':
                usort($produtos, fn($a, $b) => $b['pro_preco'] <=> $a['pro_preco']);
                break;
            case 'nome':
            default:
                usort($produtos, fn($a, $b) => strcasecmp($a['pro_nome'], $b['pro_nome']));
                break;
        }
        return $produtos;
    }
}
