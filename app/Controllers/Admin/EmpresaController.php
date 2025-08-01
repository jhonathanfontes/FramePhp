<?php

namespace App\Controllers\Admin;

use Core\Controller\BaseController;
use App\Models\EmpresaModel;
use App\Models\LojaModel;

class EmpresaController extends BaseController
{
    private $empresaModel;
    private $lojaModel;

    public function __construct()
    {
        $this->empresaModel = new EmpresaModel();
        $this->lojaModel = new LojaModel();
    }

    public function index()
    {
        $empresas = $this->empresaModel->findAllEmpresas();
        
        return $this->render('pages/admin/empresas/index', [
            'empresas' => $empresas
        ]);
    }

    public function create()
    {
        return $this->render('pages/admin/empresas/create');
    }

    public function store()
    {
        $data = [
            'razao_social' => $_POST['razao_social'],
            'nome_fantasia' => $_POST['nome_fantasia'],
            'cnpj' => $_POST['cnpj'],
            'inscricao_estadual' => $_POST['inscricao_estadual'],
            'email' => $_POST['email'],
            'telefone' => $_POST['telefone'],
            'endereco' => $_POST['endereco'],
            'cidade' => $_POST['cidade'],
            'estado' => $_POST['estado'],
            'cep' => $_POST['cep'],
            'status' => 'ativo'
        ];

        $empresaId = $this->empresaModel->create($data);

        // Criar loja padrão
        $lojaData = [
            'empresa_id' => $empresaId,
            'nome_loja' => $data['nome_fantasia'],
            'slug' => $this->generateSlug($data['nome_fantasia']),
            'status' => 'ativo'
        ];

        $this->lojaModel->create($lojaData);

        header('Location: /admin/empresas');
        exit;
    }

    public function edit($id)
    {
        $empresa = $this->empresaModel->findById($id);
        if (!$empresa) {
            return $this->render('errors/404');
        }

        return $this->render('pages/admin/empresas/edit', [
            'empresa' => $empresa
        ]);
    }

    public function update($id)
    {
        $data = [
            'razao_social' => $_POST['razao_social'],
            'nome_fantasia' => $_POST['nome_fantasia'],
            'cnpj' => $_POST['cnpj'],
            'inscricao_estadual' => $_POST['inscricao_estadual'],
            'email' => $_POST['email'],
            'telefone' => $_POST['telefone'],
            'endereco' => $_POST['endereco'],
            'cidade' => $_POST['cidade'],
            'estado' => $_POST['estado'],
            'cep' => $_POST['cep']
        ];

        $this->empresaModel->update($id, $data);

        header('Location: /admin/empresas');
        exit;
    }

    private function generateSlug($text)
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }
}
