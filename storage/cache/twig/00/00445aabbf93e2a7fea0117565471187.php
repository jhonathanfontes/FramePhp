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

/* auth/forgot-password.twig */
class __TwigTemplate_853cd6f3fd7584750289eecdecbab80d extends Template
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

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doGetParent(array $context): bool|string|Template|TemplateWrapper
    {
        // line 1
        return "layouts/auth.twig";
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $this->parent = $this->load("layouts/auth.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 4
        yield "<div class=\"auth-container\">
    <div class=\"auth-card\">
        <h1>Recuperar Senha</h1>
        
        ";
        // line 8
        if ((($tmp = ($context["error"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 9
            yield "            <div class=\"alert alert-danger\">
                ";
            // line 10
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["error"] ?? null), "html", null, true);
            yield "
            </div>
        ";
        }
        // line 13
        yield "        
        ";
        // line 14
        if ((($tmp = ($context["success"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 15
            yield "            <div class=\"alert alert-success\">
                ";
            // line 16
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["success"] ?? null), "html", null, true);
            yield "
            </div>
        ";
        }
        // line 19
        yield "        
        <p>Informe seu e-mail para receber instruções de recuperação de senha.</p>
        
        <form action=\"";
        // line 22
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(base_url("forgot-password"), "html", null, true);
        yield "\" method=\"POST\">
            <div class=\"form-group\">
                <label for=\"email\">E-mail</label>
                <input type=\"email\" name=\"email\" id=\"email\" required>
            </div>
            
            <button type=\"submit\" class=\"btn btn-primary btn-block\">Enviar</button>
        </form>
        
        <div class=\"auth-links\">
            <a href=\"";
        // line 32
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(base_url("login"), "html", null, true);
        yield "\">Voltar para o login</a>
        </div>
    </div>
</div>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "auth/forgot-password.twig";
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
        return array (  107 => 32,  94 => 22,  89 => 19,  83 => 16,  80 => 15,  78 => 14,  75 => 13,  69 => 10,  66 => 9,  64 => 8,  58 => 4,  51 => 3,  40 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends \"layouts/auth.twig\" %}

{% block content %}
<div class=\"auth-container\">
    <div class=\"auth-card\">
        <h1>Recuperar Senha</h1>
        
        {% if error %}
            <div class=\"alert alert-danger\">
                {{ error }}
            </div>
        {% endif %}
        
        {% if success %}
            <div class=\"alert alert-success\">
                {{ success }}
            </div>
        {% endif %}
        
        <p>Informe seu e-mail para receber instruções de recuperação de senha.</p>
        
        <form action=\"{{ base_url('forgot-password') }}\" method=\"POST\">
            <div class=\"form-group\">
                <label for=\"email\">E-mail</label>
                <input type=\"email\" name=\"email\" id=\"email\" required>
            </div>
            
            <button type=\"submit\" class=\"btn btn-primary btn-block\">Enviar</button>
        </form>
        
        <div class=\"auth-links\">
            <a href=\"{{ base_url('login') }}\">Voltar para o login</a>
        </div>
    </div>
</div>
{% endblock %}", "auth/forgot-password.twig", "D:\\Xampp\\htdocs\\FramePhp\\app\\Views\\auth\\forgot-password.twig");
    }
}
