<?php

namespace App\Controllers\Painel;

use Core\Controller\BaseController;
use App\Lib\TableBuilder;

class UsuariosController extends BaseController
{
    public function index()
    {
        // Gerando 30 usuários de exemplo, usando empresas como modelo
        $usuarios = [];
        for ($i = 1; $i <= 30; $i++) {
            $tipo = ($i % 3 == 0) ? 'admin' : (($i % 3 == 1) ? 'gerente' : 'operador');
            $usuarios[] = (object) [
                'id' => $i,
                'nome' => "Usuário {$i}",
                'email' => "usuario{$i}@empresa{$i}.com",
                'telefone' => sprintf('(1%u) 9%05u-%04u', rand(1, 9), rand(10000, 99999), rand(1000, 9999)),
                'tipo' => $tipo,
                'status' => ($i % 2 == 0) ? 'Ativo' : 'Inativo',
                'empresa' => (object) [
                    'id' => $i,
                    'nome_fantasia' => "Empresa {$i}",
                    'cnpj' => sprintf('%02d.%03d.%03d/0001-%02d', rand(10, 99), rand(100, 999), rand(100, 999), rand(10, 99))
                ],
                'data_cadastro' => date('Y-m-d', strtotime("-{$i} days"))
            ];
        }

        // TableBuilder para usuários
        $table = new TableBuilder();
        $table->setQtd(5);
        $table->init(
            data: $usuarios,
            headers: ["ID", "Nome", "E-mail", "Telefone", "Tipo", "Status", "Empresa Principal", "Cadastro", "Ações"],
            getParams: []
        );

        foreach ($usuarios as $key => $usuario) {
            if ($table->checkPagination($key)) {
                $table->addCol($usuario->id)
                    ->addCol("<strong>{$usuario->nome}</strong>")
                    ->addCol($usuario->email)
                    ->addCol($usuario->telefone)
                    ->addCol(ucfirst($usuario->tipo))
                    ->addCol("<span class='badge " . ($usuario->status == 'Ativo' ? 'badge-success' : 'badge-danger') . "'>" . $usuario->status . "</span>")
                    ->addCol("
                        <span style='font-weight:500;'>{$usuario->empresa->nome_fantasia}</span><br>
                        <small style='color:#888;'>{$usuario->empresa->cnpj}</small>
                    ")
                    ->addCol(date('d/m/Y', strtotime($usuario->data_cadastro)))
                    ->addCol("
                        <a href='/painel/usuario/{$usuario->id}' title='Visualizar' style='margin-right:10px; color:#007bff; font-size:1.25em; vertical-align:middle;'>
                            <i class='fas fa-eye'></i>
                        </a>
                        <button type='button' title='Excluir' onclick=\"if(confirm('Tem certeza que deseja excluir este usuário?')){this.closest('form').submit();}\" style='background:none; border:none; color:#e74c3c; font-size:1.25em; vertical-align:middle; cursor:pointer;'>
                            <i class='fas fa-trash'></i>
                        </button>
                        <form action='/painel/usuario/{$usuario->id}/delete' method='POST' style='display:none;'></form>
                    ")
                    ->addRow('', 'addlinha(' . $usuario->id . ')');
            }
        }

        return $this->render('painel/usuarios', [
            'usuariosTableHtml' => $table->render()
        ]);
    }

    public function create()
    {
        // Gerando empresas de exemplo para seleção de permissões
        $empresas = [];
        for ($i = 1; $i <= 10; $i++) {
            $empresas[] = (object) [
                'id' => $i,
                'nome_fantasia' => "Empresa {$i}",
                'cnpj_raiz' => sprintf('%02d%03d%03d%04d', rand(10, 99), rand(100, 999), rand(100, 999), rand(1000, 9999))
            ];
        }
        // Renderiza a view de criação de usuário
        return $this->render('painel/cadastrar_usuario', [
            'title' => 'Criar Novo Usuário',
            'action' => '/painel/usuarios/store',
            'empresas' => $empresas
        ]);
    }

    public function gerenciar($id)
    {
        // Simula busca do usuário pelo ID
        $usuario = (object) [
            'id' => $id,
            'nome' => "Usuário {$id}",
            'email' => "usuario{$id}@empresa{$id}.com",
            'telefone' => sprintf('(1%u) 9%05u-%04u', rand(1, 9), rand(10000, 99999), rand(1000, 9999)),
            'tipo' => ($id % 3 == 0) ? 'admin' : (($id % 3 == 1) ? 'gerente' : 'operador'),
            // Empresas vinculadas e níveis de acesso (exemplo)
            'empresas_ids' => [1, 3, 5], // IDs das empresas que ele tem acesso
            'empresas_niveis' => [
                1 => 'administrar',
                3 => 'editar',
                5 => 'visualizar'
            ]
        ];

        // Simula lista de empresas para seleção
        $empresas = [];
        for ($i = 1; $i <= 10; $i++) {
            $empresas[] = (object) [
                'id' => $i,
                'nome_fantasia' => "Empresa {$i}",
                'cnpj_raiz' => sprintf('%02d%03d%03d%04d', rand(10, 99), rand(100, 999), rand(100, 999), rand(1000, 9999))
            ];
        }

        return $this->render('painel/gerenciar_usuario', [
            'usuario' => $usuario,
            'empresas' => $empresas
        ]);
    }
}