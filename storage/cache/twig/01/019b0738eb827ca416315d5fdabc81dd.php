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

/* errors/layouts/error.twig */
class __TwigTemplate_b552132355d46b16fb201fbe6ff6d6df extends Template
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
            'error_icon' => [$this, 'block_error_icon'],
            'error_help' => [$this, 'block_error_help'],
            'error_actions' => [$this, 'block_error_actions'],
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
    <title>";
        // line 6
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("title", $context)) ? (Twig\Extension\CoreExtension::default(($context["title"] ?? null), "Erro")) : ("Erro")), "html", null, true);
        yield " - ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(app_name(), "html", null, true);
        yield "</title>
    <!-- Font Awesome -->
    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css\">
    <!-- Remova a linha abaixo que referencia o arquivo CSS externo -->
    <!-- <link rel=\"stylesheet\" href=\"";
        // line 10
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(base_url("app/Views/errors/css/error.css"), "html", null, true);
        yield "\"> -->
    <style>
        :root {
            --primary-color: #4a6cf7;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --success-color: #28a745;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --gray-color: #6c757d;
            --color: var(--";
        // line 21
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("color", $context)) ? (Twig\Extension\CoreExtension::default(($context["color"] ?? null), "danger")) : ("danger")), "html", null, true);
        yield "-color);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .error-container {
            width: 100%;
            max-width: 800px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .error-header {
            background-color: var(--color);
            color: white;
            padding: 30px 20px;
            text-align: center;
            position: relative;
        }

        .error-icon {
            font-size: 64px;
            margin-bottom: 15px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .error-code {
            font-size: 72px;
            font-weight: bold;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .error-title {
            font-size: 24px;
            margin-bottom: 0;
        }

        .error-body {
            padding: 30px;
        }

        .error-message {
            font-size: 18px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 4px solid var(--color);
            display: flex;
            align-items: flex-start;
        }

        .error-message i {
            margin-right: 10px;
            font-size: 20px;
            color: var(--color);
            margin-top: 2px;
        }

        .error-help {
            margin-bottom: 30px;
        }

        .error-help h3 {
            margin-bottom: 15px;
            color: var(--dark-color);
            display: flex;
            align-items: center;
        }

        .error-help h3 i {
            margin-right: 10px;
            color: var(--color);
        }

        .error-help ul {
            list-style-type: none;
            padding-left: 20px;
        }

        .error-help ul li {
            margin-bottom: 10px;
            position: relative;
            padding-left: 25px;
        }

        .error-help ul li i {
            position: absolute;
            left: 0;
            top: 3px;
            color: var(--color);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            padding: 10px 20px;
            font-size: 16px;
            line-height: 1.5;
            border-radius: 4px;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
            cursor: pointer;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn-primary {
            color: #fff;
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #3a5bd9;
            border-color: #3a5bd9;
        }

        .error-actions {
            text-align: center;
            margin-bottom: 20px;
        }

        .error-footer {
            text-align: center;
            padding: 15px;
            background-color: #f8f9fa;
            color: var(--gray-color);
            font-size: 14px;
            border-top: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-footer i {
            margin-right: 8px;
        }

        /* Estilos para o modo de depuração */
        .debug-info {
            margin-top: 30px;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }

        .debug-info h3 {
            margin-bottom: 15px;
            color: var(--dark-color);
            display: flex;
            align-items: center;
        }

        .debug-info h3 i {
            margin-right: 10px;
            color: var(--info-color);
        }

        .debug-details {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 15px;
            font-family: monospace;
            white-space: pre-wrap;
            font-size: 14px;
            overflow-x: auto;
        }

        .debug-trace {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            font-family: monospace;
            white-space: pre-wrap;
            font-size: 14px;
            overflow-x: auto;
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class=\"error-container\">
        <div class=\"error-header\">
            <div class=\"error-icon\">
                ";
        // line 244
        yield from $this->unwrap()->yieldBlock('error_icon', $context, $blocks);
        // line 247
        yield "            </div>
            <div class=\"error-code\">";
        // line 248
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("code", $context)) ? (Twig\Extension\CoreExtension::default(($context["code"] ?? null), "500")) : ("500")), "html", null, true);
        yield "</div>
            <h1 class=\"error-title\">";
        // line 249
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("title", $context)) ? (Twig\Extension\CoreExtension::default(($context["title"] ?? null), "Erro Interno do Servidor")) : ("Erro Interno do Servidor")), "html", null, true);
        yield "</h1>
        </div>
        
        <div class=\"error-body\">
            <div class=\"error-message\">
                <i class=\"fas fa-info-circle\"></i>
                <div>";
        // line 255
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["error"] ?? null), "message", [], "any", true, true, false, 255)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["error"] ?? null), "message", [], "any", false, false, false, 255), "Ocorreu um erro inesperado.")) : ("Ocorreu um erro inesperado.")), "html", null, true);
        yield "</div>
            </div>
            
            ";
        // line 258
        yield from $this->unwrap()->yieldBlock('error_help', $context, $blocks);
        // line 270
        yield "            
            <div class=\"error-actions\">
                ";
        // line 272
        yield from $this->unwrap()->yieldBlock('error_actions', $context, $blocks);
        // line 277
        yield "            </div>
            
            ";
        // line 279
        if ((($tmp = $this->env->getFunction('app_debug')->getCallable()()) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 280
            yield "                ";
            yield from $this->load("errors/partials/debug.twig", 280)->unwrap()->yield($context);
            // line 281
            yield "            ";
        }
        // line 282
        yield "        </div>
        
        <div class=\"error-footer\">
            <i class=\"fas fa-code\"></i> ";
        // line 285
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(app_name(), "html", null, true);
        yield " v";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(app_version(), "html", null, true);
        yield "
        </div>
    </div>
</body>
</html>";
        yield from [];
    }

    // line 244
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_error_icon(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 245
        yield "                    <i class=\"fas fa-exclamation-circle\"></i>
                ";
        yield from [];
    }

    // line 258
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_error_help(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 259
        yield "                ";
        if (array_key_exists("help", $context)) {
            // line 260
            yield "                <div class=\"error-help\">
                    <h3><i class=\"fas fa-lightbulb\"></i> ";
            // line 261
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("help_title", $context)) ? (Twig\Extension\CoreExtension::default(($context["help_title"] ?? null), "Possíveis soluções:")) : ("Possíveis soluções:")), "html", null, true);
            yield "</h3>
                    <ul>
                        ";
            // line 263
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(($context["help"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                // line 264
                yield "                            <li><i class=\"fas fa-check-circle\"></i> ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["item"], "html", null, true);
                yield "</li>
                        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['item'], $context['_parent']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 266
            yield "                    </ul>
                </div>
                ";
        }
        // line 269
        yield "            ";
        yield from [];
    }

    // line 272
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_error_actions(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 273
        yield "                <a href=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(base_url(), "html", null, true);
        yield "\" class=\"btn btn-primary\">
                    <i class=\"fas fa-home\"></i> Voltar para a página inicial
                </a>
                ";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "errors/layouts/error.twig";
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
        return array (  423 => 273,  416 => 272,  411 => 269,  406 => 266,  397 => 264,  393 => 263,  388 => 261,  385 => 260,  382 => 259,  375 => 258,  369 => 245,  362 => 244,  350 => 285,  345 => 282,  342 => 281,  339 => 280,  337 => 279,  333 => 277,  331 => 272,  327 => 270,  325 => 258,  319 => 255,  310 => 249,  306 => 248,  303 => 247,  301 => 244,  75 => 21,  61 => 10,  52 => 6,  45 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html lang=\"pt-br\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>{{ title|default('Erro') }} - {{ app_name() }}</title>
    <!-- Font Awesome -->
    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css\">
    <!-- Remova a linha abaixo que referencia o arquivo CSS externo -->
    <!-- <link rel=\"stylesheet\" href=\"{{ base_url('app/Views/errors/css/error.css') }}\"> -->
    <style>
        :root {
            --primary-color: #4a6cf7;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --success-color: #28a745;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --gray-color: #6c757d;
            --color: var(--{{ color|default('danger') }}-color);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .error-container {
            width: 100%;
            max-width: 800px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .error-header {
            background-color: var(--color);
            color: white;
            padding: 30px 20px;
            text-align: center;
            position: relative;
        }

        .error-icon {
            font-size: 64px;
            margin-bottom: 15px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .error-code {
            font-size: 72px;
            font-weight: bold;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .error-title {
            font-size: 24px;
            margin-bottom: 0;
        }

        .error-body {
            padding: 30px;
        }

        .error-message {
            font-size: 18px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 4px solid var(--color);
            display: flex;
            align-items: flex-start;
        }

        .error-message i {
            margin-right: 10px;
            font-size: 20px;
            color: var(--color);
            margin-top: 2px;
        }

        .error-help {
            margin-bottom: 30px;
        }

        .error-help h3 {
            margin-bottom: 15px;
            color: var(--dark-color);
            display: flex;
            align-items: center;
        }

        .error-help h3 i {
            margin-right: 10px;
            color: var(--color);
        }

        .error-help ul {
            list-style-type: none;
            padding-left: 20px;
        }

        .error-help ul li {
            margin-bottom: 10px;
            position: relative;
            padding-left: 25px;
        }

        .error-help ul li i {
            position: absolute;
            left: 0;
            top: 3px;
            color: var(--color);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            padding: 10px 20px;
            font-size: 16px;
            line-height: 1.5;
            border-radius: 4px;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
            cursor: pointer;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn-primary {
            color: #fff;
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #3a5bd9;
            border-color: #3a5bd9;
        }

        .error-actions {
            text-align: center;
            margin-bottom: 20px;
        }

        .error-footer {
            text-align: center;
            padding: 15px;
            background-color: #f8f9fa;
            color: var(--gray-color);
            font-size: 14px;
            border-top: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-footer i {
            margin-right: 8px;
        }

        /* Estilos para o modo de depuração */
        .debug-info {
            margin-top: 30px;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }

        .debug-info h3 {
            margin-bottom: 15px;
            color: var(--dark-color);
            display: flex;
            align-items: center;
        }

        .debug-info h3 i {
            margin-right: 10px;
            color: var(--info-color);
        }

        .debug-details {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 15px;
            font-family: monospace;
            white-space: pre-wrap;
            font-size: 14px;
            overflow-x: auto;
        }

        .debug-trace {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            font-family: monospace;
            white-space: pre-wrap;
            font-size: 14px;
            overflow-x: auto;
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class=\"error-container\">
        <div class=\"error-header\">
            <div class=\"error-icon\">
                {% block error_icon %}
                    <i class=\"fas fa-exclamation-circle\"></i>
                {% endblock %}
            </div>
            <div class=\"error-code\">{{ code|default('500') }}</div>
            <h1 class=\"error-title\">{{ title|default('Erro Interno do Servidor') }}</h1>
        </div>
        
        <div class=\"error-body\">
            <div class=\"error-message\">
                <i class=\"fas fa-info-circle\"></i>
                <div>{{ error.message|default('Ocorreu um erro inesperado.') }}</div>
            </div>
            
            {% block error_help %}
                {% if help is defined %}
                <div class=\"error-help\">
                    <h3><i class=\"fas fa-lightbulb\"></i> {{ help_title|default('Possíveis soluções:') }}</h3>
                    <ul>
                        {% for item in help %}
                            <li><i class=\"fas fa-check-circle\"></i> {{ item }}</li>
                        {% endfor %}
                    </ul>
                </div>
                {% endif %}
            {% endblock %}
            
            <div class=\"error-actions\">
                {% block error_actions %}
                <a href=\"{{ base_url() }}\" class=\"btn btn-primary\">
                    <i class=\"fas fa-home\"></i> Voltar para a página inicial
                </a>
                {% endblock %}
            </div>
            
            {% if app_debug() %}
                {% include 'errors/partials/debug.twig' %}
            {% endif %}
        </div>
        
        <div class=\"error-footer\">
            <i class=\"fas fa-code\"></i> {{ app_name() }} v{{ app_version() }}
        </div>
    </div>
</body>
</html>", "errors/layouts/error.twig", "D:\\Xampp\\htdocs\\FramePhp\\app\\Views\\errors\\layouts\\error.twig");
    }
}
