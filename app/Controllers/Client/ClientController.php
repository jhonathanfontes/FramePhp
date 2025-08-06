<?php

namespace App\Controllers\Client;

use Core\Controller\BaseController;

class ClientController extends BaseController
{
    public function dashboard()
    {
        // Implementação do método dashboard
    }

    public function orders()
    {
        // Implementação do método orders
    }

    public function profile()
    {
        // Implementação do método profile
    }

    
    /**
     * Página Sobre
     */
    public function sobre()
    {
        echo $this->render('pages/client/sobre/index', [
            'title' => 'Sobre Nós'
        ]);
    }

    /**
     * Página Contato
     */
    public function contato()
    {
        echo $this->view('pages/client/contato/index', [
            'title' => 'Contato'
        ]);
    }
}