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

/* errors/debug.twig */
class __TwigTemplate_070a044337744fec019e75060b223a5e extends Template
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
    <title>Erro ";
        // line 6
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["statusCode"] ?? null), "html", null, true);
        yield " - ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(app_name(), "html", null, true);
        yield "</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .error-container {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .error-title {
            color: #721c24;
            margin-top: 0;
        }
        .error-details {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            overflow-x: auto;
        }
        .error-trace {
            font-family: monospace;
            white-space: pre-wrap;
            font-size: 14px;
        }
        .error-line {
            background-color: #ffecb3;
        }
    </style>
</head>
<body>
    <div class=\"error-container\">
        <h1 class=\"error-title\">Erro ";
        // line 46
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["statusCode"] ?? null), "html", null, true);
        yield "</h1>
        <p><strong>";
        // line 47
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["error"] ?? null), "type", [], "any", false, false, false, 47), "html", null, true);
        yield ":</strong> ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["error"] ?? null), "message", [], "any", false, false, false, 47), "html", null, true);
        yield "</p>
    </div>
    
    <div class=\"error-details\">
        <h2>Detalhes do Erro</h2>
        <p><strong>Arquivo:</strong> ";
        // line 52
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["error"] ?? null), "file", [], "any", false, false, false, 52), "html", null, true);
        yield "</p>
        <p><strong>Linha:</strong> ";
        // line 53
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["error"] ?? null), "line", [], "any", false, false, false, 53), "html", null, true);
        yield "</p>
        
        ";
        // line 55
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["error"] ?? null), "trace", [], "any", true, true, false, 55)) {
            // line 56
            yield "            <h3>Stack Trace</h3>
            <div class=\"error-trace\">";
            // line 57
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["error"] ?? null), "trace", [], "any", false, false, false, 57), "html", null, true);
            yield "</div>
        ";
        }
        // line 59
        yield "    </div>
    
    <footer>
        <p>";
        // line 62
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(app_name(), "html", null, true);
        yield " v";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(app_version(), "html", null, true);
        yield " - Modo de Depuração</p>
    </footer>
</body>
</html>";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "errors/debug.twig";
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
        return array (  132 => 62,  127 => 59,  122 => 57,  119 => 56,  117 => 55,  112 => 53,  108 => 52,  98 => 47,  94 => 46,  49 => 6,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html lang=\"pt-br\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Erro {{ statusCode }} - {{ app_name() }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .error-container {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .error-title {
            color: #721c24;
            margin-top: 0;
        }
        .error-details {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            overflow-x: auto;
        }
        .error-trace {
            font-family: monospace;
            white-space: pre-wrap;
            font-size: 14px;
        }
        .error-line {
            background-color: #ffecb3;
        }
    </style>
</head>
<body>
    <div class=\"error-container\">
        <h1 class=\"error-title\">Erro {{ statusCode }}</h1>
        <p><strong>{{ error.type }}:</strong> {{ error.message }}</p>
    </div>
    
    <div class=\"error-details\">
        <h2>Detalhes do Erro</h2>
        <p><strong>Arquivo:</strong> {{ error.file }}</p>
        <p><strong>Linha:</strong> {{ error.line }}</p>
        
        {% if error.trace is defined %}
            <h3>Stack Trace</h3>
            <div class=\"error-trace\">{{ error.trace }}</div>
        {% endif %}
    </div>
    
    <footer>
        <p>{{ app_name() }} v{{ app_version() }} - Modo de Depuração</p>
    </footer>
</body>
</html>", "errors/debug.twig", "D:\\Xampp\\htdocs\\FramePhp\\app\\Views\\errors\\debug.twig");
    }
}
