<?php

namespace App\Controllers\Painel;

use Core\Controller\BaseController;
use App\Lib\TableBuilder;

class EmpresasController extends BaseController
{
    public function index()
    {
        // Gerando 30 empresas de exemplo
        $empresasData = [];
        $empresasAtivas = 0;
        $totalVendas = 0;
        $receitaTotal = 0.0;

        for ($i = 1; $i <= 30; $i++) {
            $ativo = ($i % 3 !== 0); // 2/3 ativas, 1/3 inativas
            $vendas = rand(0, 200);
            $receita = $vendas * rand(50, 300);

            $empresasData[] = (object) [
                'id' => $i,
                'nome_fantasia' => "Empresa {$i}",
                'razao_social' => "Razão Social {$i} Ltda",
                'cnpj' => sprintf('%02d.%03d.%03d/0001-%02d', rand(10, 99), rand(100, 999), rand(100, 999), rand(10, 99)),
                'email' => "contato{$i}@empresa{$i}.com",
                'telefone' => sprintf('(1%u) 9%05u-%04u', rand(1, 9), rand(10000, 99999), rand(1000, 9999)),
                'total_vendas' => $vendas,
                'receita_total' => $receita,
                'ativo' => $ativo,
                'data_cadastro' => date('Y-m-d', strtotime("-{$i} days"))
            ];

            if ($ativo)
                $empresasAtivas++;
            $totalVendas += $vendas;
            $receitaTotal += $receita;
        }

        // TableBuilder para empresas
        $table = new TableBuilder();
        $table->setQtd(10)
            ->init(
                data: $empresasData,
                headers: array('Codigo',"Empresa", "CNPJ", "Contatos", "Vendas", "Receita", "Status", "Cadastro", "Ações"),
                getParams: array("b" => $this->getParams("b"))
            );

        foreach ($empresasData as $key => $empresa) {
            if ($table->checkPagination($key)) {
                $statusLabel = $empresa->ativo ? 'Ativa' : 'Inativa';
                $statusClass = $empresa->ativo ? 'is-success' : 'is-danger';
                $formattedStatus = "<span class='tag {$statusClass}'>{$statusLabel}</span>";

                $table->addCol($empresa->id)
                    ->addCol("
                        <div style='line-height:1.3'>
                            <span style='font-weight:600; color:#222;'>{$empresa->nome_fantasia}</span><br>
                            <span style='font-size:0.95em; color:#888;'>{$empresa->razao_social}</span>
                        </div>
                    ")
                    ->addCol("<span style='font-family:monospace; color:#555;'>{$empresa->cnpj}</span>")
                    ->addCol("
                        <div style='line-height:1.3'>
                            <span style='color:#007bff; font-weight:500;'>{$empresa->email}</span><br>
                            <span style='color:#888; font-size:0.95em;'><i class='fas fa-phone-alt'></i> {$empresa->telefone}</span>
                        </div>
                    ")
                    ->addCol("<span style='color:#00b894; font-weight:500;'>{$empresa->total_vendas}</span>")
                    ->addCol("<span style='color:#f39c12; font-weight:500;'>R$ " . number_format($empresa->receita_total, 2, ',', '.') . "</span>")
                    ->addCol($formattedStatus)
                    ->addCol("<span style='color:#888; font-size:0.95em;'>" . date('d/m/Y', strtotime($empresa->data_cadastro)) . "</span>")
                    ->addCol("
                        <a href='/painel/empresas/{$empresa->id}/edit' title='Editar' style='margin-right:10px; color:#007bff; font-size:1.25em; vertical-align:middle;'>
                            <i class='fas fa-pen'></i>
                        </a>
                        <button type='button' title='Excluir' onclick=\"if(confirm('Tem certeza que deseja excluir esta empresa?')){this.closest('form').submit();}\" style='background:none; border:none; color:#e74c3c; font-size:1.25em; vertical-align:middle; cursor:pointer;'>
                            <i class='fas fa-trash'></i>
                        </button>
                        <form action='/painel/empresas/{$empresa->id}/delete' method='POST' style='display:none;'></form>
                    ")
                    ->addRow('', 'addlinha(' . $empresa->id . ')');
            }
        }

        return $this->render('painel/empresas', [
            'empresasTableHtml' => $table->render(),
            'empresasAtivas' => $empresasAtivas,
            'totalVendas' => $totalVendas,
            'receitaTotal' => $receitaTotal
        ]);
    }
}