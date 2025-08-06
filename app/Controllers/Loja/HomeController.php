<?php

namespace App\Controllers\Loja;

use App\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Produto;
use App\Models\Categoria;
use Core\Http\Request;
use Core\Http\Response;

class HomeController extends Controller
{
    public function index(Request $request): Response
    {
        $empresaId = $request->get('empresa_id') ?? 1; // Default ou detectar por subdomain
        
        $empresa = Empresa::find($empresaId);
        if (!$empresa || !$empresa->ativo) {
            return $this->view('loja.erro', ['mensagem' => 'Loja não encontrada']);
        }

        $produtos = Produto::where('empresa_id', $empresaId)
            ->where('ativo', true)
            ->where('estoque', '>', 0)
            ->orderBy('destaque', 'desc')
            ->orderBy('nome')
            ->limit(12)
            ->get();

        $categorias = Categoria::where('empresa_id', $empresaId)
            ->where('ativo', true)
            ->orderBy('ordem')
            ->orderBy('nome')
            ->get();

        return $this->view('loja.home', [
            'empresa' => $empresa,
            'produtos' => $produtos,
            'categorias' => $categorias
        ]);
    }

    public function produtos(Request $request): Response
    {
        $empresaId = $request->get('empresa_id') ?? 1;
        $categoriaId = $request->get('categoria_id');
        $busca = $request->get('busca');

        $query = Produto::where('empresa_id', $empresaId)
            ->where('ativo', true)
            ->where('estoque', '>', 0);

        if ($categoriaId) {
            $query->where('categoria_id', $categoriaId);
        }

        if ($busca) {
            $query->where(function($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                  ->orWhere('descricao', 'like', "%{$busca}%")
                  ->orWhere('codigo', 'like', "%{$busca}%");
            });
        }

        $produtos = $query->orderBy('nome')->paginate(20);

        $categorias = Categoria::where('empresa_id', $empresaId)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        return $this->view('loja.produtos', [
            'produtos' => $produtos,
            'categorias' => $categorias,
            'categoriaAtual' => $categoriaId,
            'busca' => $busca
        ]);
    }

    public function produto(Request $request, $id): Response
    {
        $empresaId = $request->get('empresa_id') ?? 1;
        
        $produto = Produto::where('empresa_id', $empresaId)
            ->where('ativo', true)
            ->find($id);

        if (!$produto) {
            return $this->redirect('/produtos');
        }

        $produtosRelacionados = Produto::where('empresa_id', $empresaId)
            ->where('ativo', true)
            ->where('categoria_id', $produto->categoria_id)
            ->where('id', '!=', $produto->id)
            ->limit(4)
            ->get();

        return $this->view('loja.produto', [
            'produto' => $produto,
            'produtosRelacionados' => $produtosRelacionados
        ]);
    }

    public function sobre(Request $request): Response
    {
        $empresaId = $request->get('empresa_id') ?? 1;
        $empresa = Empresa::find($empresaId);

        return $this->view('loja.sobre', ['empresa' => $empresa]);
    }

    public function contato(Request $request): Response
    {
        $empresaId = $request->get('empresa_id') ?? 1;
        $empresa = Empresa::find($empresaId);

        return $this->view('loja.contato', ['empresa' => $empresa]);
    }
} 