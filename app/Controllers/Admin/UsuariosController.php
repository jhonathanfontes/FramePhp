<?php

namespace App\Controllers\Admin;

use App\Lib\TableBuilder;
use App\Models\CadUsuarioModel;
use Core\Controller\BaseController;

class UsuariosController extends BaseController
{
     public function index()
    {
        $buscaParam = $_GET['b'] ?? null;

        $userModel = new CadUsuarioModel();
        $usuarios = $userModel->all();

        // Lógica para a tabela de produtos usando TableBuilder (HTML puro)
        $table = new TableBuilder();
        $table->setQtd(10); // 5 itens por página

        $table->init(
            data: $usuarios, // Dados completos dos produtos
            headers: array("ID", "NOME", "TIPO", "SITUAÇÃO", "Status", "Ações"), // Headers
            getParams: array("b" => $this->getParams("b")) // Parâmetros GET para busca
        );

        foreach ($usuarios as $key => $item) {
            if ($table->checkPagination($key)) { // Verifica se o item está na página atual

                $roleLabel = ''; // Lógica de formatação de status
                $roleClass = '';
                switch ($item->permissao_id) {
                    case 1:
                        $roleLabel = 'Administrador';
                        $roleClass = 'is-success';
                        break;
                    default:
                        $roleLabel = 'Usuario';
                        $roleClass = 'is-danger';
                        break;
                }
                $formattedRoles = "<span class='tag {$roleClass}'>{$roleLabel}</span>";

                $statusLabel = ''; // Lógica de formatação de status
                $statusClass = '';
                switch ($item->status) {
                    case 1:
                        $statusLabel = 'Ativo';
                        $statusClass = 'is-success';
                        break;
                    default:
                        $statusLabel = 'Inativo';
                        $statusClass = 'is-danger';
                        break;
                }
                $formattedStatus = "<span class='tag {$statusClass}'>{$statusLabel}</span>";

                $table->addCol($item->id_usuario) // Valor para o checkbox
                    ->addCol($item->use_nome)
                    ->addCol($item->use_email)
                    ->addCol($formattedRoles) // Status formatado
                    ->addCol($formattedStatus) // Status formatado
                    ->addCol("  <div class='buttons has-addons is-small'>
                        <a href='/admin/users/" . $item->id_usuario . "/edit' class='button is-info is-light'>
                            <span class='icon is-small'><i class='fas fa-edit'></i></span> <span>Editar</span>
                        </a> 
                        <form action='/admin/users/" . $item->id_usuario . "/delete' method='POST' onsubmit=\"return confirm('Tem certeza que deseja excluir este usuário?');\">
                            <input type='hidden' name='_method' value='DELETE'>
                            <button type='submit' class='button is-danger is-light' title='Excluir'>
                                <span class='icon is-small'><i class='fas fa-trash-alt'></i></span> <span>Excluir</span> 
                            </button>
                        </form>
                    </div>")
                    ->addRow('', 'addlinha(' . $item->id_usuario . ')'); // Adiciona a linha
            }
        }

        return $this->render('templates/users', [
            'usuariosTableHtml' => $table->render(),
            'busca_param' => $buscaParam
        ]);
    }
}