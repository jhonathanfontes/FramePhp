<?php

namespace App\Controllers;

class UserController
{
    public function show($id)
    {
        // O parâmetro $id será automaticamente injetado
        // com o valor capturado da URL
        return "Showing user with ID: {$id}";
    }

    public function index()
    {
        return "Listing all users";
    }
}