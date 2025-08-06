<?php

namespace App\Controllers\Loja;

use App\Models\CadUsuarioModel;

class UsuarioController
{
    public function all()
    {
        $usuarios = new CadUsuarioModel();
       return $usuarios->all();
    }
    public function create()
    {
        // Implementação do método create
    }

    public function read($id)
    {
        // Implementação do método read
    }

    public function update($id, $data)
    {
        // Implementação do método user
    }

    public function data()
    {
        // Implementação do método data
    }

    public function adminStats()
    {
        // Implementação do método adminStats
    }
}