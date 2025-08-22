<?php

namespace App\Controllers\Painel;

use Core\Controller\BaseController;
use Core\Http\Request;
use Core\Http\Response;

class LogsController extends BaseController
{
    public function index(): string
    {
        // Aqui você pode adicionar a lógica para buscar os logs do banco de dados ou de arquivos
        // Por enquanto, vamos retornar um array vazio ou com dados de exemplo
        $logs = []; // Substitua por sua lógica de busca de logs

        return $this->render('painel/logs', [
            'active_menu' => 'logs',
            'logs' => $logs
        ]);
    }
}
