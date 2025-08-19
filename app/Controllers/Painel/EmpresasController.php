<?php

namespace App\Controllers\Painel;

use Core\Controller\BaseController;
use App\Models\EmpresaModel;
use Core\Http\Request;

class EmpresasController extends BaseController
{
    private $empresaModel;

    public function __construct()
    {
        $this->empresaModel = new EmpresaModel();
    }

    public function index()
    {
        $empresas = $this->empresaModel->orderBy('created_at', 'DESC')->get();
        
        return $this->render('painel/empresas/empresas.html.twig', [
            'active_menu' => 'empresas',
            'empresas' => $empresas
        ]);
    }

    public function create()
    {
        return $this->render('painel/empresas/form.html.twig', [
            'active_menu' => 'empresas',
            'action' => 'criar'
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        
        // Validação
        if (empty($data['nome']) || empty($data['cnpj'])) {
            return $this->json([
                'error' => 'Nome e CNPJ são obrigatórios'
            ], 422);
        }

        // Sanitização
        $data['cnpj'] = preg_replace('/[^0-9]/', '', $data['cnpj']);
        
        try {
            $this->empresaModel->create($data);
            return $this->json(['message' => 'Empresa criada com sucesso']);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Erro ao criar empresa'], 500);
        }
    }

    public function edit($id)
    {
        $empresa = $this->empresaModel->find($id);
        
        if (!$empresa) {
            return $this->json(['error' => 'Empresa não encontrada'], 404);
        }

        return $this->render('painel/empresas/form.html.twig', [
            'active_menu' => 'empresas',
            'empresa' => $empresa,
            'action' => 'editar'
        ]);
    }

    public function update(Request $request, $id)
    {
        $empresa = $this->empresaModel->find($id);
        
        if (!$empresa) {
            return $this->json(['error' => 'Empresa não encontrada'], 404);
        }

        $data = $request->all();
        
        // Validação
        if (empty($data['nome']) || empty($data['cnpj'])) {
            return $this->json([
                'error' => 'Nome e CNPJ são obrigatórios'
            ], 422);
        }

        // Sanitização
        $data['cnpj'] = preg_replace('/[^0-9]/', '', $data['cnpj']);
        
        try {
            $this->empresaModel->update($id, $data);
            return $this->json(['message' => 'Empresa atualizada com sucesso']);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Erro ao atualizar empresa'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->empresaModel->delete($id);
            return $this->json(['message' => 'Empresa excluída com sucesso']);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Erro ao excluir empresa'], 500);
        }
    }
}