<?php

namespace App\Controllers\Painel;

use App\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Usuario;
use Core\Http\Request;
use Core\Http\Response;
use Core\Auth\Auth;

class EmpresasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:painel');
    }

    public function index(Request $request): Response
    {
        $busca = $request->get('busca');
        $status = $request->get('status');

        $query = Empresa::query();

        if ($busca) {
            $query->where(function($q) use ($busca) {
                $q->where('nome_fantasia', 'like', "%{$busca}%")
                  ->orWhere('razao_social', 'like', "%{$busca}%")
                  ->orWhere('cnpj', 'like', "%{$busca}%")
                  ->orWhere('email', 'like', "%{$busca}%");
            });
        }

        if ($status) {
            $query->where('ativo', $status === 'ativo');
        }

        $empresas = $query->orderBy('nome_fantasia')->paginate(20);

        return $this->view('painel.empresas.index', [
            'empresas' => $empresas,
            'busca' => $busca,
            'status' => $status
        ]);
    }

    public function create(Request $request): Response
    {
        if ($request->isPost()) {
            $dados = $request->all();
            
            $this->validate($dados, [
                'nome_fantasia' => 'required|max:100',
                'razao_social' => 'required|max:150',
                'cnpj' => 'required|unique:empresas,cnpj',
                'email' => 'required|email|unique:empresas,email',
                'telefone' => 'required',
                'endereco' => 'required',
                'cidade' => 'required',
                'estado' => 'required|size:2',
                'cep' => 'required'
            ]);

            $empresa = Empresa::create($dados);

            // Criar usuário admin padrão
            $usuario = new Usuario();
            $usuario->empresa_id = $empresa->id;
            $usuario->nome = 'Administrador';
            $usuario->email = $empresa->email;
            $usuario->setSenha('123456'); // Senha padrão
            $usuario->tipo = 'admin_empresa';
            $usuario->status = 'ativo';
            $usuario->save();

            return $this->redirect('/painel/empresas')
                ->with('success', 'Empresa criada com sucesso!');
        }

        return $this->view('painel.empresas.create');
    }

    public function edit(Request $request, $id): Response
    {
        $empresa = Empresa::find($id);
        
        if (!$empresa) {
            return $this->redirect('/painel/empresas')
                ->with('error', 'Empresa não encontrada');
        }

        if ($request->isPost()) {
            $dados = $request->all();
            
            $this->validate($dados, [
                'nome_fantasia' => 'required|max:100',
                'razao_social' => 'required|max:150',
                'cnpj' => "required|unique:empresas,cnpj,{$id}",
                'email' => "required|email|unique:empresas,email,{$id}",
                'telefone' => 'required',
                'endereco' => 'required',
                'cidade' => 'required',
                'estado' => 'required|size:2',
                'cep' => 'required'
            ]);

            $empresa->update($dados);

            return $this->redirect('/painel/empresas')
                ->with('success', 'Empresa atualizada com sucesso!');
        }

        return $this->view('painel.empresas.edit', ['empresa' => $empresa]);
    }

    public function show(Request $request, $id): Response
    {
        $empresa = Empresa::with(['usuarios', 'produtos', 'vendas'])->find($id);
        
        if (!$empresa) {
            return $this->redirect('/painel/empresas')
                ->with('error', 'Empresa não encontrada');
        }

        // Estatísticas da empresa
        $totalVendas = $empresa->vendas()->count();
        $totalProdutos = $empresa->produtos()->count();
        $totalUsuarios = $empresa->usuarios()->count();
        $receitaTotal = $empresa->vendas()->sum('total');

        return $this->view('painel.empresas.show', [
            'empresa' => $empresa,
            'totalVendas' => $totalVendas,
            'totalProdutos' => $totalProdutos,
            'totalUsuarios' => $totalUsuarios,
            'receitaTotal' => $receitaTotal
        ]);
    }

    public function toggleStatus(Request $request, $id): Response
    {
        $empresa = Empresa::find($id);
        
        if (!$empresa) {
            return $this->redirect('/painel/empresas')
                ->with('error', 'Empresa não encontrada');
        }

        $empresa->ativo = !$empresa->ativo;
        $empresa->save();

        $status = $empresa->ativo ? 'ativada' : 'desativada';
        
        return $this->redirect('/painel/empresas')
            ->with('success', "Empresa {$status} com sucesso!");
    }

    public function delete(Request $request, $id): Response
    {
        $empresa = Empresa::find($id);
        
        if (!$empresa) {
            return $this->redirect('/painel/empresas')
                ->with('error', 'Empresa não encontrada');
        }

        // Verificar se há vendas
        if ($empresa->vendas()->count() > 0) {
            return $this->redirect('/painel/empresas')
                ->with('error', 'Não é possível excluir uma empresa que possui vendas');
        }

        $empresa->delete();

        return $this->redirect('/painel/empresas')
            ->with('success', 'Empresa excluída com sucesso!');
    }
} 