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

/* errors/500.twig */
class __TwigTemplate_fe69ca55cfa9fafef66b16a4e16a6088 extends Template
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
            'error_icon' => [$this, 'block_error_icon'],
            'error_help' => [$this, 'block_error_help'],
        ];
    }

    protected function doGetParent(array $context): bool|string|Template|TemplateWrapper
    {
        // line 1
        return "errors/layouts/error.twig";
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 3
        $context["code"] = 500;
        // line 4
        $context["title"] = "Erro Interno do Servidor";
        // line 5
        $context["color"] = "danger";
        // line 1
        $this->parent = $this->load("errors/layouts/error.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 7
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_error_icon(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 8
        yield "    <i class=\"fas fa-exclamation-circle\"></i>
";
        yield from [];
    }

    // line 11
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_error_help(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 12
        yield "    <div class=\"error-help\">
        <h3><i class=\"fas fa-lightbulb\"></i> Possíveis soluções:</h3>
        <ul>
            <li><i class=\"fas fa-check-circle\"></i> Este é um problema no servidor e não com sua requisição</li>
            <li><i class=\"fas fa-check-circle\"></i> Os administradores foram notificados do problema</li>
            <li><i class=\"fas fa-check-circle\"></i> Tente novamente mais tarde ou entre em contato com o suporte</li>
        </ul>
    </div>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "errors/500.twig";
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
        return array (  79 => 12,  72 => 11,  66 => 8,  59 => 7,  54 => 1,  52 => 5,  50 => 4,  48 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'errors/layouts/error.twig' %}

{% set code = 500 %}
{% set title = 'Erro Interno do Servidor' %}
{% set color = 'danger' %}

{% block error_icon %}
    <i class=\"fas fa-exclamation-circle\"></i>
{% endblock %}

{% block error_help %}
    <div class=\"error-help\">
        <h3><i class=\"fas fa-lightbulb\"></i> Possíveis soluções:</h3>
        <ul>
            <li><i class=\"fas fa-check-circle\"></i> Este é um problema no servidor e não com sua requisição</li>
            <li><i class=\"fas fa-check-circle\"></i> Os administradores foram notificados do problema</li>
            <li><i class=\"fas fa-check-circle\"></i> Tente novamente mais tarde ou entre em contato com o suporte</li>
        </ul>
    </div>
{% endblock %}", "errors/500.twig", "D:\\Xampp\\htdocs\\FramePhp\\app\\Views\\errors\\500.twig");
    }
}
