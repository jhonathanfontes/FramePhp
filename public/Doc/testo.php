Estrutura de Componentes Twig Reutilizáveis
 Estrutura de Pastas
 components/
 ├── form/
 │   ├── input.html.twig
 │   ├── textarea.html.twig
 │   ├── select.html.twig
 │   └── button.html.twig
 ├── ui/
 │   ├── card.html.twig
 │   ├── modal.html.twig
 │   └── alert.html.twig
 ├── navigation/
 │   ├── menu.html.twig
 │   ├── breadcrumb.html.twig
 │   └── pagination.html.twig
 └── layout/
 ├── header.html.twig
 ├── footer.html.twig
 └── sidebar.html.twig
 1. Componente Input (form/input.html.twig)
2. Componente Card (ui/card.html.twig)
 twig
 {# Componente Input Reutilizável #}
 {% set inputId = id|default('input_' ~ random()) %}
 {% set inputClass = 'form-control' ~ (class ? ' ' ~ class : '') %}
 {% set inputType = type|default('text') %}
 <div class="form-group{{ error ? ' has-error' : '' }}">
    {% if label %}
        <label for="{{ inputId }}" class="form-label">
            {{ label }}
            {% if required %}<span class="text-danger">*</span>{% endif %}
        </label>
    {% endif %}
    
    <input 
        type="{{ inputType }}"
        id="{{ inputId }}"
        name="{{ name }}"
        class="{{ inputClass }}"
        value="{{ value|default('') }}"
        placeholder="{{ placeholder|default('') }}"
        {% if required %}required{% endif %}
        {% if disabled %}disabled{% endif %}
        {% if readonly %}readonly{% endif %}
        {% for attr, val in attributes|default({}) %}
            {{ attr }}="{{ val }}"
        {% endfor %}
    >
    
    {% if help %}
        <small class="form-text text-muted">{{ help }}</small>
    {% endif %}
    
    {% if error %}
        <div class="invalid-feedback d-block">{{ error }}</div>
    {% endif %}
 </div>
twig
{# Componente Card Reutilizável #}
 {% set cardClass = 'card' ~ (class ? ' ' ~ class : '') %}
 <div class="{{ cardClass }}">
    {% if image %}
        <img src="{{ image.src }}" class="card-img-top" alt="{{ image.alt|default('') }}">
    {% endif %}
    
    {% if header or title %}
        <div class="card-header">
            {% if header %}
                {{ header|raw }}
            {% else %}
                <h5 class="card-title mb-0">{{ title }}</h5>
            {% endif %}
        </div>
    {% endif %}
    
    <div class="card-body">
        {% if title and not header %}
            <h5 class="card-title">{{ title }}</h5>
        {% endif %}
        
        {% if subtitle %}
            <h6 class="card-subtitle mb-2 text-muted">{{ subtitle }}</h6>
        {% endif %}
        
        {% if content %}
            <div class="card-text">{{ content|raw }}</div>
        {% endif %}
        
        {{ body|raw }}
        
        {% if actions %}
            <div class="card-actions mt-3">
                {% for action in actions %}
                    <a href="{{ action.url }}" class="btn {{ action.class|default('btn-primary'
                        {{ action.text }}
                    </a>
                {% endfor %}
            </div>
        {% endif %}
    </div>
    
    {% if footer %}
        <div class="card-footer">
{{ footer|raw }}
 </div>
 {% endif %}
 </div>
 3. Componente Modal (ui/modal.html.twig)
twig
 
 {# Componente Modal Reutilizável #}
 {% set modalId = id|default('modal_' ~ random()) %}
 {% set modalSize = size|default('') %}
 {% set sizeClass = modalSize ? ' modal-' ~ modalSize : '' %}
 <div class="modal fade" id="{{ modalId }}" tabindex="-1" aria-labelledby="{{ modalId }}Label" a
    <div class="modal-dialog{{ sizeClass }}{% if centered %} modal-dialog-centered{% endif %}{%
        <div class="modal-content">
            {% if title or header %}
                <div class="modal-header">
                    {% if header %}
                        {{ header|raw }}
                    {% else %}
                        <h5 class="modal-title" id="{{ modalId }}Label">{{ title }}</h5>
                    {% endif %}
                    
                    {% if dismissible|default(true) %}
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-la
                    {% endif %}
                </div>
            {% endif %}
            
            <div class="modal-body">
                {{ body|raw }}
            </div>
            
            {% if footer or actions %}
                <div class="modal-footer">
                    {% if footer %}
                        {{ footer|raw }}
                    {% else %}
                        {% for action in actions %}
                            <button type="{{ action.type|default('button') }}" 
                                    class="btn {{ action.class|default('btn-secondary') }}"
                                    {% if action.dismiss %}data-bs-dismiss="modal"{% endif %}
                                    {% if action.onclick %}onclick="{{ action.onclick }}"{% end
                                {{ action.text }}
                            </button>
                        {% endfor %}
                    {% endif %}
                </div>
            {% endif %}
        </div>
    </div>
 </div>
4. Componente Menu (navigation/menu.html.twig)
twig
{# Componente Menu Reutilizável #}
 {% set menuClass = 'navbar' ~ (class ? ' ' ~ class : ' navbar-expand-lg navbar-light bg-light')
 <nav class="{{ menuClass }}">
    <div class="container{% if fluid %}-fluid{% endif %}">
        {% if brand %}
            <a class="navbar-brand" href="{{ brand.url|default('#') }}">
                {% if brand.image %}
                    <img src="{{ brand.image }}" alt="{{ brand.text }}" height="30">
                {% else %}
                    {{ brand.text }}
                {% endif %}
            </a>
        {% endif %}
        
        {% if items %}
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-targ
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav{{ align == 'center' ? ' mx-auto' : (align == 'right' ? ' 
                    {% for item in items %}
                        <li class="nav-item{% if item.dropdown %} dropdown{% endif %}{% if item
                            {% if item.dropdown %}
                                <a class="nav-link dropdown-toggle" href="#" role="button" data
                                    {{ item.text }}
                                </a>
                                <ul class="dropdown-menu">
                                    {% for subItem in item.dropdown %}
                                        {% if subItem.divider %}
                                            <li><hr class="dropdown-divider"></li>
                                        {% else %}
                                            <li>
                                                <a class="dropdown-item{% if subItem.active %} 
                                                   href="{{ subItem.url }}">
                                                    {{ subItem.text }}
                                                </a>
                                            </li>
                                        {% endif %}
                                    {% endfor %}
                                </ul>
                            {% else %}
                                <a class="nav-link{% if item.active %} active{% endif %}" 
                                   href="{{ item.url }}">
                                    {{ item.text }}
Como Usar os Componentes
 1. Incluindo um Input:
 2. Incluindo um Card:
 3. Incluindo um Modal:
                                </a>
                            {% endif %}
                        </li>
                    {% endfor %}
                </ul>
            </div>
        {% endif %}
    </div>
 </nav>
 twig
 {% include 'components/form/input.html.twig' with {
    'name': 'email',
    'label': 'E-mail',
    'type': 'email',
    'placeholder': 'Digite seu e-mail',
    'required': true,
    'value': user.email|default(''),
    'help': 'Nunca compartilharemos seu e-mail'
 } %}
 twig
 {% include 'components/ui/card.html.twig' with {
    'title': 'Título do Card',
    'subtitle': 'Subtítulo opcional',
    'body': '<p>Conteúdo do card aqui...</p>',
    'actions': [
        {'text': 'Ver mais', 'url': '/detalhes', 'class': 'btn-primary'},
        {'text': 'Editar', 'url': '/editar', 'class': 'btn-outline-secondary'}
    ]
 } %}
4. Incluindo um Menu:
 Dicas Adicionais
 1. Variáveis Globais: Você pode definir variáveis globais no Twig para valores padrão
 2. Herança: Use {% extends %} para criar variações dos componentes
 3. Blocos: Utilize {% block %} dentro dos componentes para permitir personalização
 4. Macros: Para componentes mais simples, considere usar macros Twig
 5. Documentação: Mantenha comentários explicando os parâmetros de cada componente
 twig
 {% include 'components/ui/modal.html.twig' with {
    'id': 'confirmModal',
    'title': 'Confirmar Ação',
    'body': '<p>Tem certeza que deseja continuar?</p>',
    'actions': [
        {'text': 'Cancelar', 'class': 'btn-secondary', 'dismiss': true},
        {'text': 'Confirmar', 'class': 'btn-danger', 'onclick': 'confirmarAcao()'}
    ]
 } %}
 twig
 {% include 'components/navigation/menu.html.twig' with {
    'brand': {'text': 'Meu Site', 'url': '/'},
    'items': [
        {'text': 'Home', 'url': '/', 'active': true},
        {'text': 'Sobre', 'url': '/sobre'},
        {
            'text': 'Produtos',
            'dropdown': [
                {'text': 'Categoria 1', 'url': '/categoria-1'},
                {'text': 'Categoria 2', 'url': '/categoria-2'},
                {'divider': true},
                {'text': 'Todos', 'url': '/produtos'}
            ]
        },
        {'text': 'Contato', 'url': '/contato'}
    ]
 } %}