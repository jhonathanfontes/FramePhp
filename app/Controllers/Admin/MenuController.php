<?php

namespace App\Controllers\Admin;

use Core\Controller\BaseController;
use Core\Database\Database;

class MenuController extends BaseController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function index()
    {
        // Buscar todos os menus com seus submenus
        $menus = $this->db->query("
            SELECT m.*, 
                   GROUP_CONCAT(s.id, ':', s.nome, ':', s.rota, ':', s.ordem ORDER BY s.ordem) as submenus
            FROM menus m
            LEFT JOIN submenus s ON m.id = s.menu_id
            GROUP BY m.id
            ORDER BY m.ordem
        ")->fetchAll();

        // Formatar os submenus
        foreach ($menus as &$menu) {
            $menu['submenus'] = $menu['submenus'] ? array_map(function($submenu) {
                list($id, $nome, $rota, $ordem) = explode(':', $submenu);
                return [
                    'id' => $id,
                    'nome' => $nome,
                    'rota' => $rota,
                    'ordem' => $ordem
                ];
            }, explode(',', $menu['submenus'])) : [];
        }

        return $this->render('admin/menus/index', [
            'menus' => $menus
        ]);
    }

    public function create()
    {
        return $this->render('admin/menus/create');
    }

    public function store()
    {
        $nome = $_POST['nome'] ?? '';
        $icone = $_POST['icone'] ?? '';
        $ordem = (int)($_POST['ordem'] ?? 0);

        if (empty($nome)) {
            return $this->json(['error' => 'Nome é obrigatório'], 400);
        }

        $this->db->query(
            "INSERT INTO menus (nome, icone, ordem) VALUES (?, ?, ?)",
            [$nome, $icone, $ordem]
        );

        return $this->redirect('/admin/menus');
    }

    public function edit($id)
    {
        $menu = $this->db->query(
            "SELECT * FROM menus WHERE id = ?",
            [$id]
        )->fetch();

        if (!$menu) {
            return $this->redirect('/admin/menus');
        }

        return $this->render('admin/menus/edit', [
            'menu' => $menu
        ]);
    }

    public function update($id)
    {
        $nome = $_POST['nome'] ?? '';
        $icone = $_POST['icone'] ?? '';
        $ordem = (int)($_POST['ordem'] ?? 0);

        if (empty($nome)) {
            return $this->json(['error' => 'Nome é obrigatório'], 400);
        }

        $this->db->query(
            "UPDATE menus SET nome = ?, icone = ?, ordem = ? WHERE id = ?",
            [$nome, $icone, $ordem, $id]
        );

        return $this->redirect('/admin/menus');
    }

    public function destroy($id)
    {
        // Primeiro remove os submenus
        $this->db->query("DELETE FROM submenus WHERE menu_id = ?", [$id]);
        
        // Depois remove o menu
        $this->db->query("DELETE FROM menus WHERE id = ?", [$id]);

        return $this->redirect('/admin/menus');
    }

    // Métodos para Submenus
    public function createSubmenu($menuId)
    {
        $menu = $this->db->query(
            "SELECT * FROM menus WHERE id = ?",
            [$menuId]
        )->fetch();

        if (!$menu) {
            return $this->redirect('/admin/menus');
        }

        return $this->render('admin/menus/create_submenu', [
            'menu' => $menu
        ]);
    }

    public function storeSubmenu($menuId)
    {
        $nome = $_POST['nome'] ?? '';
        $rota = $_POST['rota'] ?? '';
        $ordem = (int)($_POST['ordem'] ?? 0);

        if (empty($nome) || empty($rota)) {
            return $this->json(['error' => 'Nome e rota são obrigatórios'], 400);
        }

        $this->db->query(
            "INSERT INTO submenus (menu_id, nome, rota, ordem) VALUES (?, ?, ?, ?)",
            [$menuId, $nome, $rota, $ordem]
        );

        return $this->redirect('/admin/menus');
    }

    public function editSubmenu($id)
    {
        $submenu = $this->db->query(
            "SELECT s.*, m.nome as menu_nome 
             FROM submenus s 
             JOIN menus m ON s.menu_id = m.id 
             WHERE s.id = ?",
            [$id]
        )->fetch();

        if (!$submenu) {
            return $this->redirect('/admin/menus');
        }

        return $this->render('admin/menus/edit_submenu', [
            'submenu' => $submenu
        ]);
    }

    public function updateSubmenu($id)
    {
        $nome = $_POST['nome'] ?? '';
        $rota = $_POST['rota'] ?? '';
        $ordem = (int)($_POST['ordem'] ?? 0);

        if (empty($nome) || empty($rota)) {
            return $this->json(['error' => 'Nome e rota são obrigatórios'], 400);
        }

        $this->db->query(
            "UPDATE submenus SET nome = ?, rota = ?, ordem = ? WHERE id = ?",
            [$nome, $rota, $ordem, $id]
        );

        return $this->redirect('/admin/menus');
    }

    public function destroySubmenu($id)
    {
        $this->db->query("DELETE FROM submenus WHERE id = ?", [$id]);
        return $this->redirect('/admin/menus');
    }
} 