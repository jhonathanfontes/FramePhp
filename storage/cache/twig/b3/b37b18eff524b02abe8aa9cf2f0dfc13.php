<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* auth/login.twig */
class __TwigTemplate_0fb7378f7c30fab93c76abc10bf9be42 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield "<!DOCTYPE html>
<html lang=\"pt-br\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Login - FramePhp</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        
        .login-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
        }
        
        h1 {
            text-align: center;
            color: #4a6cf7;
            margin-bottom: 24px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        
        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .form-check input {
            width: auto;
            margin-right: 8px;
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #4a6cf7;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            text-align: center;
        }
        
        .btn:hover {
            background-color: #3a5bd9;
        }
        
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .auth-links {
            margin-top: 20px;
            text-align: center;
        }
        
        .auth-links a {
            color: #4a6cf7;
            text-decoration: none;
            display: block;
            margin-top: 8px;
        }
        
        .auth-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class=\"login-container\">
        <h1>Login</h1>
        
        ";
        // line 121
        if (array_key_exists("error", $context)) {
            // line 122
            yield "            <div class=\"alert alert-danger\">
                ";
            // line 123
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["error"] ?? null), "html", null, true);
            yield "
            </div>
        ";
        }
        // line 126
        yield "        
        ";
        // line 127
        if (array_key_exists("success", $context)) {
            // line 128
            yield "            <div class=\"alert alert-success\">
                ";
            // line 129
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["success"] ?? null), "html", null, true);
            yield "
            </div>
        ";
        }
        // line 132
        yield "        
        <form action=\"";
        // line 133
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(base_url("login"), "html", null, true);
        yield "\" method=\"POST\">
            <div class=\"form-group\">
                <label for=\"email\">E-mail</label>
                <input type=\"email\" name=\"email\" id=\"email\" value=\"";
        // line 136
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("email", $context)) ? (Twig\Extension\CoreExtension::default(($context["email"] ?? null), "")) : ("")), "html", null, true);
        yield "\" required>
            </div>
            
            <div class=\"form-group\">
                <label for=\"password\">Senha</label>
                <input type=\"password\" name=\"password\" id=\"password\" required>
            </div>
            
            <div class=\"form-check\">
                <input type=\"checkbox\" name=\"remember\" id=\"remember\">
                <label for=\"remember\">Lembrar-me</label>
            </div>
            
            <button type=\"submit\" class=\"btn\">Entrar</button>
        </form>
        
        <div class=\"auth-links\">
            <a href=\"";
        // line 153
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(base_url("forgot-password"), "html", null, true);
        yield "\">Esqueceu sua senha?</a>
            <a href=\"";
        // line 154
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(base_url("register"), "html", null, true);
        yield "\">Não tem uma conta? Cadastre-se</a>
        </div>
    </div>
</body>
</html>";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "auth/login.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  222 => 154,  218 => 153,  198 => 136,  192 => 133,  189 => 132,  183 => 129,  180 => 128,  178 => 127,  175 => 126,  169 => 123,  166 => 122,  164 => 121,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html lang=\"pt-br\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Login - FramePhp</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        
        .login-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
        }
        
        h1 {
            text-align: center;
            color: #4a6cf7;
            margin-bottom: 24px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        
        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .form-check input {
            width: auto;
            margin-right: 8px;
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #4a6cf7;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            text-align: center;
        }
        
        .btn:hover {
            background-color: #3a5bd9;
        }
        
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .auth-links {
            margin-top: 20px;
            text-align: center;
        }
        
        .auth-links a {
            color: #4a6cf7;
            text-decoration: none;
            display: block;
            margin-top: 8px;
        }
        
        .auth-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class=\"login-container\">
        <h1>Login</h1>
        
        {% if error is defined %}
            <div class=\"alert alert-danger\">
                {{ error }}
            </div>
        {% endif %}
        
        {% if success is defined %}
            <div class=\"alert alert-success\">
                {{ success }}
            </div>
        {% endif %}
        
        <form action=\"{{ base_url('login') }}\" method=\"POST\">
            <div class=\"form-group\">
                <label for=\"email\">E-mail</label>
                <input type=\"email\" name=\"email\" id=\"email\" value=\"{{ email|default('') }}\" required>
            </div>
            
            <div class=\"form-group\">
                <label for=\"password\">Senha</label>
                <input type=\"password\" name=\"password\" id=\"password\" required>
            </div>
            
            <div class=\"form-check\">
                <input type=\"checkbox\" name=\"remember\" id=\"remember\">
                <label for=\"remember\">Lembrar-me</label>
            </div>
            
            <button type=\"submit\" class=\"btn\">Entrar</button>
        </form>
        
        <div class=\"auth-links\">
            <a href=\"{{ base_url('forgot-password') }}\">Esqueceu sua senha?</a>
            <a href=\"{{ base_url('register') }}\">Não tem uma conta? Cadastre-se</a>
        </div>
    </div>
</body>
</html>", "auth/login.twig", "D:\\Xampp\\htdocs\\FramePhp\\app\\Views\\auth\\login.twig");
    }
}
