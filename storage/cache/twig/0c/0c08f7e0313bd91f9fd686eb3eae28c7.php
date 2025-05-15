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

/* layouts/auth.twig */
class __TwigTemplate_563d3209059cee029b9260c08cb6c5e6 extends Template
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
            'content' => [$this, 'block_content'],
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
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["title"] ?? null), "html", null, true);
        yield " - ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(app_name(), "html", null, true);
        yield "</title>
    <link rel=\"stylesheet\" href=\"";
        // line 7
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(base_url("assets/css/auth.css"), "html", null, true);
        yield "\">
</head>
<body>
    <div class=\"main-container\">
        ";
        // line 11
        yield from $this->unwrap()->yieldBlock('content', $context, $blocks);
        // line 12
        yield "    </div>
    
    <footer class=\"auth-footer\">
        <p>&copy; ";
        // line 15
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate("now", "Y"), "html", null, true);
        yield " ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(app_name(), "html", null, true);
        yield " v";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(app_version(), "html", null, true);
        yield "</p>
    </footer>
</body>
</html>";
        yield from [];
    }

    // line 11
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "layouts/auth.twig";
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
        return array (  83 => 11,  70 => 15,  65 => 12,  63 => 11,  56 => 7,  50 => 6,  43 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html lang=\"pt-br\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>{{ title }} - {{ app_name() }}</title>
    <link rel=\"stylesheet\" href=\"{{ base_url('assets/css/auth.css') }}\">
</head>
<body>
    <div class=\"main-container\">
        {% block content %}{% endblock %}
    </div>
    
    <footer class=\"auth-footer\">
        <p>&copy; {{ \"now\"|date(\"Y\") }} {{ app_name() }} v{{ app_version() }}</p>
    </footer>
</body>
</html>", "layouts/auth.twig", "D:\\Xampp\\htdocs\\FramePhp\\app\\Views\\layouts\\auth.twig");
    }
}
