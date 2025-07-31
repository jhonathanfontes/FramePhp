Adding the renderPdfContent helper method to the BaseController.
```

```php
<?php

namespace Core\Controller;

use Core\View\TwigManager;
use Core\Http\Response;
use Core\Controller;

class BaseController extends Controller
{
        protected function isLogged(): bool
    {
        // Implemente sua lógica de verificação de login aqui
        // Ex: return isset($_SESSION['user_id']);
        return true; // Apenas para exemplo
    }

    protected function getServico(): array
    {
        // Implemente a lógica para obter dados de serviço (permissões, URLs, etc.)
        return [
            "URL" => "/admin/modulos", // URL base de exemplo
            "ALTERAR" => "1" // Permissão de alteração de exemplo
        ];
    }

    /**
     * Retorna um parâmetro GET ou POST, ou um valor padrão.
     * @param string $key Chave do parâmetro.
     * @param mixed $default Valor padrão se a chave não for encontrada.
     * @return mixed
     */
    protected function getParams(string $key, $default = null)
    {
        // Prioriza POST, depois GET
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        return $default;
    }
    protected function render(string $view, array $data = []): string
    {
        $twig = TwigManager::getInstance();
        return $twig->render($view, $data);
    }

    protected function redirect($url)
    {
        header("Location: $url");
        exit;
    }

    protected function renderPdfContent($template, $data = [])
    {
        $twig = TwigManager::getInstance();
        return $twig->render($template . '.twig', $data);
    }
}
```<?php

namespace Core\Controller;

use Core\View\TwigManager;
use Core\Http\Response;
use Core\Controller;

class BaseController extends Controller
{
        protected function isLogged(): bool
    {
        // Implemente sua lógica de verificação de login aqui
        // Ex: return isset($_SESSION['user_id']);
        return true; // Apenas para exemplo
    }

    protected function getServico(): array
    {
        // Implemente a lógica para obter dados de serviço (permissões, URLs, etc.)
        return [
            "URL" => "/admin/modulos", // URL base de exemplo
            "ALTERAR" => "1" // Permissão de alteração de exemplo
        ];
    }

    /**
     * Retorna um parâmetro GET ou POST, ou um valor padrão.
     * @param string $key Chave do parâmetro.
     * @param mixed $default Valor padrão se a chave não for encontrada.
     * @return mixed
     */
    protected function getParams(string $key, $default = null)
    {
        // Prioriza POST, depois GET
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        return $default;
    }
    protected function render(string $view, array $data = []): string
    {
        $twig = TwigManager::getInstance();
        return $twig->render($view, $data);
    }

    protected function redirect($url)
    {
        header("Location: $url");
        exit;
    }

    protected function renderPdfContent($template, $data = [])
    {
        $twig = TwigManager::getInstance();
        return $twig->render($template . '.twig', $data);
    }
}