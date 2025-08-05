<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Venda;
use App\Models\Produto;
use App\Models\Pessoa;
use App\Models\Empresa;
use Core\Http\Request;
use Core\Http\Response;
use Core\Auth\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request): Response
    {
        $usuario = Auth::user();
        $empresa = $usuario->empresa;

        // Estatísticas do dashboard
        $vendasHoje = Venda::where('empresa_id', $empresa->id)
            ->whereDate('data_venda', today())
            ->count();

        $vendasMes = Venda::where('empresa_id', $empresa->id)
            ->whereMonth('data_venda', now()->month)
            ->whereYear('data_venda', now()->year)
            ->count();

        $receitaMes = Venda::where('empresa_id', $empresa->id)
            ->whereMonth('data_venda', now()->month)
            ->whereYear('data_venda', now()->year)
            ->sum('total');

        $produtosBaixoEstoque = Produto::where('empresa_id', $empresa->id)
            ->where('estoque', '<=', 'estoque_minimo')
            ->where('ativo', true)
            ->count();

        $vendasRecentes = Venda::where('empresa_id', $empresa->id)
            ->with('pessoa')
            ->orderBy('data_venda', 'desc')
            ->limit(10)
            ->get();

        $produtosMaisVendidos = Produto::select('produtos.*')
            ->join('venda_itens', 'produtos.id', '=', 'venda_itens.produto_id')
            ->join('vendas', 'venda_itens.venda_id', '=', 'vendas.id')
            ->where('vendas.empresa_id', $empresa->id)
            ->whereMonth('vendas.data_venda', now()->month)
            ->whereYear('vendas.data_venda', now()->year)
            ->groupBy('produtos.id')
            ->orderByRaw('SUM(venda_itens.quantidade) DESC')
            ->limit(5)
            ->get();

        return $this->view('admin.dashboard', [
            'empresa' => $empresa,
            'vendasHoje' => $vendasHoje,
            'vendasMes' => $vendasMes,
            'receitaMes' => $receitaMes,
            'produtosBaixoEstoque' => $produtosBaixoEstoque,
            'vendasRecentes' => $vendasRecentes,
            'produtosMaisVendidos' => $produtosMaisVendidos
        ]);
    }

    public function relatorios(Request $request): Response
    {
        $usuario = Auth::user();
        $empresa = $usuario->empresa;

        $dataInicio = $request->get('data_inicio', now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', now()->endOfMonth()->format('Y-m-d'));

        $vendas = Venda::where('empresa_id', $empresa->id)
            ->whereBetween('data_venda', [$dataInicio, $dataFim])
            ->with('pessoa')
            ->orderBy('data_venda', 'desc')
            ->paginate(20);

        $totalVendas = Venda::where('empresa_id', $empresa->id)
            ->whereBetween('data_venda', [$dataInicio, $dataFim])
            ->sum('total');

        $totalItens = Venda::where('empresa_id', $empresa->id)
            ->whereBetween('data_venda', [$dataInicio, $dataFim])
            ->join('venda_itens', 'vendas.id', '=', 'venda_itens.venda_id')
            ->sum('venda_itens.quantidade');

        return $this->view('admin.relatorios', [
            'empresa' => $empresa,
            'vendas' => $vendas,
            'totalVendas' => $totalVendas,
            'totalItens' => $totalItens,
            'dataInicio' => $dataInicio,
            'dataFim' => $dataFim
        ]);
    }

    public function configuracoes(Request $request): Response
    {
        $usuario = Auth::user();
        $empresa = $usuario->empresa;

        if ($request->isPost()) {
            $dados = $request->all();
            
            // Validação
            $this->validate($dados, [
                'nome_fantasia' => 'required|max:100',
                'email' => 'required|email',
                'telefone' => 'required',
                'endereco' => 'required',
                'cidade' => 'required',
                'estado' => 'required|size:2',
                'cep' => 'required'
            ]);

            $empresa->update($dados);

            return $this->redirect('/admin/configuracoes')
                ->with('success', 'Configurações atualizadas com sucesso!');
        }

        return $this->view('admin.configuracoes', ['empresa' => $empresa]);
    }
} 