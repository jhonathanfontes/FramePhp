<?php

namespace App\Controllers;

use Core\Controller\BaseController;
use Core\Cache\CacheManager;

/**
 * Controller de Exemplo Demonstrando as Novas Funcionalidades
 * 
 * Este controller mostra como usar todas as funcionalidades implementadas
 * baseadas no SpeedPHP
 */
class ExampleController extends BaseController
{
    private $cache;

    public function __construct()
    {
        parent::__construct();
        $this->cache = CacheManager::getInstance();
        
        // Define permissões de exemplo
        $this->permissionManager->setPermissions([
            'LER' => 1,
            'SALVAR' => 1,
            'ALTERAR' => 1,
            'EXCLUIR' => 0
        ]);
        
        $this->permissionManager->setUserRoles(['user', 'editor']);
    }

    /**
     * Página inicial com cache
     */
    public function index()
    {
        // Verifica permissão de leitura
        if (!$this->requirePermission('LER')) {
            return;
        }

        // Usa cache para dados frequentemente acessados
        $dados = $this->cache->remember('exemplo_dados', function() {
            return [
                'titulo' => 'Página de Exemplo',
                'descricao' => 'Demonstração das novas funcionalidades',
                'data' => date('Y-m-d H:i:s'),
                'usuarios' => [
                    ['id' => 1, 'nome' => 'João Silva'],
                    ['id' => 2, 'nome' => 'Maria Santos'],
                    ['id' => 3, 'nome' => 'Pedro Costa']
                ]
            ];
        }, 1800); // Cache por 30 minutos

        return $this->render('exemplo/index', $dados);
    }

    /**
     * Formulário de criação
     */
    public function criar()
    {
        // Verifica permissão de salvamento
        if (!$this->requirePermission('SALVAR')) {
            return;
        }

        // Gera token CSRF para o formulário
        $csrfToken = $this->generateCsrfToken();

        return $this->render('exemplo/criar', [
            'csrf_token' => $csrfToken,
            'titulo' => 'Criar Novo Registro'
        ]);
    }

    /**
     * Salvar dados via POST
     */
    public function salvar()
    {
        // Verifica permissão de salvamento
        if (!$this->requirePermission('SALVAR')) {
            return;
        }

        // Valida método HTTP
        if (!$this->requirePost()) {
            return;
        }

        // Valida token CSRF
        $csrfToken = $this->postParams('csrf_token');
        if (!$this->validateCsrfToken($csrfToken)) {
            return;
        }

        // Obtém dados do formulário
        $nome = $this->postParams('nome');
        $email = $this->postParams('email');

        // Validação básica
        if (empty($nome) || empty($email)) {
            $this->redirectError('/exemplo/criar', 'Nome e email são obrigatórios!');
            return;
        }

        try {
            // Simula salvamento no banco
            $id = $this->salvarNoBanco($nome, $email);
            
            // Limpa cache relacionado
            $this->cache->delete('exemplo_dados');
            
            // Redireciona com mensagem de sucesso
            $this->redirectSuccess('/exemplo', 'Registro criado com sucesso!');
            
        } catch (\Exception $e) {
            $this->redirectError('/exemplo/criar', 'Erro ao salvar: ' . $e->getMessage());
        }
    }

    /**
     * Editar registro
     */
    public function editar($id = null)
    {
        // Verifica permissão de alteração
        if (!$this->requirePermission('ALTERAR')) {
            return;
        }

        if (!$id) {
            $this->redirectError('/exemplo', 'ID do registro é obrigatório!');
            return;
        }

        // Busca dados do cache ou banco
        $registro = $this->cache->remember("exemplo_registro_{$id}", function() use ($id) {
            return $this->buscarRegistro($id);
        }, 3600);

        if (!$registro) {
            $this->redirectError('/exemplo', 'Registro não encontrado!');
            return;
        }

        $csrfToken = $this->generateCsrfToken();

        return $this->render('exemplo/editar', [
            'registro' => $registro,
            'csrf_token' => $csrfToken,
            'titulo' => 'Editar Registro'
        ]);
    }

    /**
     * Atualizar registro
     */
    public function atualizar($id = null)
    {
        // Verifica permissão de alteração
        if (!$this->requirePermission('ALTERAR')) {
            return;
        }

        // Valida método HTTP
        if (!$this->requirePost()) {
            return;
        }

        if (!$id) {
            $this->redirectError('/exemplo', 'ID do registro é obrigatório!');
            return;
        }

        // Valida token CSRF
        $csrfToken = $this->postParams('csrf_token');
        if (!$this->validateCsrfToken($csrfToken)) {
            return;
        }

        $nome = $this->postParams('nome');
        $email = $this->postParams('email');

        try {
            // Simula atualização
            $this->atualizarNoBanco($id, $nome, $email);
            
            // Limpa caches relacionados
            $this->cache->deleteMultiple([
                'exemplo_dados',
                "exemplo_registro_{$id}"
            ]);
            
            $this->redirectSuccess('/exemplo', 'Registro atualizado com sucesso!');
            
        } catch (\Exception $e) {
            $this->redirectError("/exemplo/editar/{$id}", 'Erro ao atualizar: ' . $e->getMessage());
        }
    }

    /**
     * Excluir registro (desabilitado por permissão)
     */
    public function excluir($id = null)
    {
        // Esta ação está desabilitada por permissão
        if (!$this->requirePermission('EXCLUIR')) {
            return;
        }

        // Código de exclusão aqui...
    }

    /**
     * API para listar dados
     */
    public function apiListar()
    {
        // Valida se é requisição GET
        if (!$this->requireGet(true)) {
            return;
        }

        // Verifica permissão
        if (!$this->requirePermission('LER')) {
            $this->jsonError('Acesso negado', [], 403);
            return;
        }

        try {
            $dados = $this->cache->remember('exemplo_api_dados', function() {
                return [
                    'usuarios' => [
                        ['id' => 1, 'nome' => 'João Silva', 'email' => 'joao@email.com'],
                        ['id' => 2, 'nome' => 'Maria Santos', 'email' => 'maria@email.com'],
                        ['id' => 3, 'nome' => 'Pedro Costa', 'email' => 'pedro@email.com']
                    ],
                    'total' => 3,
                    'timestamp' => date('Y-m-d H:i:s')
                ];
            }, 900); // Cache por 15 minutos

            $this->jsonSuccess('Dados recuperados com sucesso', $dados);
            
        } catch (\Exception $e) {
            $this->jsonError('Erro ao recuperar dados: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * API para criar registro
     */
    public function apiCriar()
    {
        // Valida se é requisição POST
        if (!$this->requirePost(true)) {
            return;
        }

        // Verifica permissão
        if (!$this->requirePermission('SALVAR')) {
            $this->jsonError('Acesso negado', [], 403);
            return;
        }

        // Obtém dados JSON
        $data = $this->jsonParams();
        
        if (empty($data['nome']) || empty($data['email'])) {
            $this->jsonError('Nome e email são obrigatórios', [], 400);
            return;
        }

        try {
            $id = $this->salvarNoBanco($data['nome'], $data['email']);
            
            // Limpa cache
            $this->cache->delete('exemplo_api_dados');
            
            $this->jsonSuccess('Registro criado com sucesso', ['id' => $id], 201);
            
        } catch (\Exception $e) {
            $this->jsonError('Erro ao criar registro: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Página de estatísticas do cache
     */
    public function cacheStats()
    {
        // Apenas administradores podem ver estatísticas
        if (!$this->requireRole('admin')) {
            return;
        }

        $stats = $this->cache->getStats();
        
        return $this->render('exemplo/cache_stats', [
            'stats' => $stats,
            'titulo' => 'Estatísticas do Cache'
        ]);
    }

    /**
     * Limpar cache
     */
    public function limparCache()
    {
        // Apenas administradores podem limpar cache
        if (!$this->requireRole('admin')) {
            return;
        }

        try {
            $this->cache->clear();
            $this->redirectSuccess('/exemplo/cache-stats', 'Cache limpo com sucesso!');
        } catch (\Exception $e) {
            $this->redirectError('/exemplo/cache-stats', 'Erro ao limpar cache: ' . $e->getMessage());
        }
    }

    /**
     * Métodos auxiliares simulados
     */
    private function salvarNoBanco($nome, $email)
    {
        // Simula salvamento no banco
        return rand(1000, 9999);
    }

    private function buscarRegistro($id)
    {
        // Simula busca no banco
        return [
            'id' => $id,
            'nome' => 'Usuário ' . $id,
            'email' => 'usuario' . $id . '@email.com',
            'criado_em' => date('Y-m-d H:i:s')
        ];
    }

    private function atualizarNoBanco($id, $nome, $email)
    {
        // Simula atualização no banco
        return true;
    }
}
