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

/* auth/register.twig */
class __TwigTemplate_56abd0dc7200b7f639fd445c0d145778 extends Template
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
        <h1>Cadastro</h1>
        
        <form action=\"";
        // line 8
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(base_url("register"), "html", null, true);
        yield "\" method=\"POST\">
            <div class=\"form-group\">
                <label for=\"name\">Nome</label>
                <input type=\"text\" name=\"name\" id=\"name\" value=\"";
        // line 11
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("name", $context)) ? (Twig\Extension\CoreExtension::default(($context["name"] ?? null), "")) : ("")), "html", null, true);
        yield "\" required>
                ";
        // line 12
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["errors"] ?? null), "name", [], "any", false, false, false, 12)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 13
            yield "                    <div class=\"invalid-feedback\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["errors"] ?? null), "name", [], "any", false, false, false, 13), "html", null, true);
            yield "</div>
                ";
        }
        // line 15
        yield "            </div>
            
            <div class=\"form-group\">
                <label for=\"email\">E-mail</label>
                <input type=\"email\" name=\"email\" id=\"email\" value=\"";
        // line 19
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("email", $context)) ? (Twig\Extension\CoreExtension::default(($context["email"] ?? null), "")) : ("")), "html", null, true);
        yield "\" required>
                ";
        // line 20
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["errors"] ?? null), "email", [], "any", false, false, false, 20)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 21
            yield "                    <div class=\"invalid-feedback\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["errors"] ?? null), "email", [], "any", false, false, false, 21), "html", null, true);
            yield "</div>
                ";
        }
        // line 23
        yield "            </div>
            
            <div class=\"form-group\">
                <label for=\"password\">Senha</label>
                <input type=\"password\" name=\"password\" id=\"password\" required>
                ";
        // line 28
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["errors"] ?? null), "password", [], "any", false, false, false, 28)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 29
            yield "                    <div class=\"invalid-feedback\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["errors"] ?? null), "password", [], "any", false, false, false, 29), "html", null, true);
            yield "</div>
                ";
        }
        // line 31
        yield "            </div>
            
            <div class=\"form-group\">
                <label for=\"password_confirmation\">Confirmar Senha</label>
                <input type=\"password\" name=\"password_confirmation\" id=\"password_confirmation\" required>
                ";
        // line 36
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["errors"] ?? null), "password_confirmation", [], "any", false, false, false, 36)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 37
            yield "                    <div class=\"invalid-feedback\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["errors"] ?? null), "password_confirmation", [], "any", false, false, false, 37), "html", null, true);
            yield "</div>
                ";
        }
        // line 39
        yield "            </div>
            
            <button type=\"submit\" class=\"btn btn-primary btn-block\">Cadastrar</button>
        </form>
        
        <div class=\"auth-links\">
            <a href=\"";
        // line 45
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(base_url("login"), "html", null, true);
        yield "\">Já tem uma conta? Faça login</a>
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
        return "auth/register.twig";
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
        return array (  138 => 45,  130 => 39,  124 => 37,  122 => 36,  115 => 31,  109 => 29,  107 => 28,  100 => 23,  94 => 21,  92 => 20,  88 => 19,  82 => 15,  76 => 13,  74 => 12,  70 => 11,  64 => 8,  58 => 4,  51 => 3,  40 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends \"layouts/auth.twig\" %}

{% block content %}
<div class=\"auth-container\">
    <div class=\"auth-card\">
        <h1>Cadastro</h1>
        
        <form action=\"{{ base_url('register') }}\" method=\"POST\">
            <div class=\"form-group\">
                <label for=\"name\">Nome</label>
                <input type=\"text\" name=\"name\" id=\"name\" value=\"{{ name|default('') }}\" required>
                {% if errors.name %}
                    <div class=\"invalid-feedback\">{{ errors.name }}</div>
                {% endif %}
            </div>
            
            <div class=\"form-group\">
                <label for=\"email\">E-mail</label>
                <input type=\"email\" name=\"email\" id=\"email\" value=\"{{ email|default('') }}\" required>
                {% if errors.email %}
                    <div class=\"invalid-feedback\">{{ errors.email }}</div>
                {% endif %}
            </div>
            
            <div class=\"form-group\">
                <label for=\"password\">Senha</label>
                <input type=\"password\" name=\"password\" id=\"password\" required>
                {% if errors.password %}
                    <div class=\"invalid-feedback\">{{ errors.password }}</div>
                {% endif %}
            </div>
            
            <div class=\"form-group\">
                <label for=\"password_confirmation\">Confirmar Senha</label>
                <input type=\"password\" name=\"password_confirmation\" id=\"password_confirmation\" required>
                {% if errors.password_confirmation %}
                    <div class=\"invalid-feedback\">{{ errors.password_confirmation }}</div>
                {% endif %}
            </div>
            
            <button type=\"submit\" class=\"btn btn-primary btn-block\">Cadastrar</button>
        </form>
        
        <div class=\"auth-links\">
            <a href=\"{{ base_url('login') }}\">Já tem uma conta? Faça login</a>
        </div>
    </div>
</div>
{% endblock %}", "auth/register.twig", "D:\\Xampp\\htdocs\\FramePhp\\app\\Views\\auth\\register.twig");
    }
}
