
<?php

namespace App\Middleware;

use Core\Http\Request;
use Core\Http\Response;
use Core\Interface\MiddlewareInterface;
use Core\Auth\Auth;
use App\Models\EmpresaModel;
use App\Models\LojaModel;

class EmpresaMiddleware implements MiddlewareInterface
{
    private $empresaModel;
    private $lojaModel;

    public function __construct()
    {
        $this->empresaModel = new EmpresaModel();
        $this->lojaModel = new LojaModel();
    }

    public function handle(Request $request, \Closure $next): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return Response::redirectResponse(base_url('auth/login'));
        }

        // Verificar se é uma rota de loja específica
        $uri = $request->getUri();
        if (preg_match('/\/loja\/([^\/]+)/', $uri, $matches)) {
            $lojaSlug = $matches[1];
            
            // Verificar se a loja existe e está ativa
            $loja = $this->lojaModel->findBySlug($lojaSlug);
            if (!$loja || $loja['status'] !== 'ativo') {
                return Response::jsonResponse(['error' => 'Loja não encontrada ou inativa'], 404);
            }

            // Verificar se a empresa está ativa
            $empresa = $this->empresaModel->findById($loja['empresa_id']);
            if (!$empresa || $empresa['status'] !== 'ativo') {
                return Response::jsonResponse(['error' => 'Empresa não encontrada ou inativa'], 404);
            }

            // Adicionar dados da empresa e loja ao request
            $request->setAttributes([
                'empresa' => $empresa,
                'loja' => $loja
            ]);
        }

        // Verificar permissões do usuário para a empresa
        if (isset($empresa) && !$this->hasEmpresaPermission($user, $empresa['id'])) {
            return Response::jsonResponse(['error' => 'Sem permissão para acessar esta empresa'], 403);
        }

        return $next($request);
    }

    private function hasEmpresaPermission($user, $empresaId): bool
    {
        // Implementar lógica de permissão
        // Por exemplo: verificar se o usuário pertence à empresa
        return true; // Por enquanto permitir todos
    }
}
