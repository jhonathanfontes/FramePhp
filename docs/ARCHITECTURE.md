
# Arquitetura do Sistema - Multi-Empresas

## 📋 Visão Geral

O Sistema Multi-Empresas foi desenvolvido seguindo os princípios de arquitetura limpa, padrões de design modernos e boas práticas de desenvolvimento PHP.

## 🏗️ Arquitetura Geral

```
┌─────────────────────────────────────────────────────────────┐
│                    PRESENTATION LAYER                       │
├─────────────────────────────────────────────────────────────┤
│  Controllers  │  Views (Twig)  │  Routes  │  Middleware     │
├─────────────────────────────────────────────────────────────┤
│                     BUSINESS LAYER                          │
├─────────────────────────────────────────────────────────────┤
│   Services    │  Validation   │   Auth    │   Policies      │
├─────────────────────────────────────────────────────────────┤
│                      DATA LAYER                             │
├─────────────────────────────────────────────────────────────┤
│    Models     │   Database    │  Cache    │   External APIs │
└─────────────────────────────────────────────────────────────┘
```

## 🎯 Padrões Arquiteturais

### 1. Model-View-Controller (MVC)
- **Models**: Representam dados e lógica de negócio
- **Views**: Templates Twig para apresentação
- **Controllers**: Coordenam interações entre Model e View

### 2. Repository Pattern
```php
interface ProdutoRepositoryInterface
{
    public function find(int $id): ?Produto;
    public function findByEmpresa(int $empresaId): array;
    public function save(Produto $produto): bool;
}
```

### 3. Service Layer
```php
class ProdutoService
{
    public function __construct(
        private ProdutoRepository $repository,
        private ValidatorService $validator
    ) {}
    
    public function criarProduto(array $dados): Produto
    {
        $this->validator->validate($dados, $this->regras());
        return $this->repository->save(new Produto($dados));
    }
}
```

### 4. Middleware Pipeline
```php
$request → [Auth] → [CSRF] → [RateLimit] → Controller → Response
```

## 🔧 Componentes do Core

### 1. Router System
```php
// Definição de rotas
Router::group(['prefix' => 'api', 'middleware' => ['auth']], function() {
    Router::get('/produtos', [ProdutoController::class, 'index']);
    Router::post('/produtos', [ProdutoController::class, 'store']);
});
```

### 2. Dependency Injection Container
```php
class Container
{
    public function bind(string $abstract, Closure $concrete): void
    public function singleton(string $abstract, Closure $concrete): void
    public function resolve(string $abstract)
}
```

### 3. Database Abstraction
```php
// Query Builder
$produtos = DB::table('produtos')
    ->where('empresa_id', $empresaId)
    ->where('ativo', true)
    ->orderBy('nome')
    ->get();

// ORM Style
$produto = Produto::where('id', $id)
    ->with(['categoria', 'empresa'])
    ->first();
```

## 🔒 Segurança

### 1. Autenticação JWT
```php
class JWTMiddleware
{
    public function handle(Request $request): bool
    {
        $token = $request->bearerToken();
        
        if (!JWT::validate($token)) {
            throw new UnauthorizedException();
        }
        
        $user = JWT::decode($token);
        $request->setUser($user);
        
        return true;
    }
}
```

### 2. Autorização baseada em Políticas
```php
class ProdutoPolicy
{
    public function view(User $user, Produto $produto): bool
    {
        return $user->empresa_id === $produto->empresa_id;
    }
    
    public function update(User $user, Produto $produto): bool
    {
        return $user->hasPermission('produtos.editar') 
            && $this->view($user, $produto);
    }
}
```

### 3. Validação e Sanitização
```php
class Validator
{
    public function validate(array $data, array $rules): array
    {
        foreach ($rules as $field => $rule) {
            $this->validateField($data[$field] ?? null, $rule);
        }
        
        return $this->sanitize($data);
    }
}
```

## 🎨 Template Engine (Twig)

### 1. Estrutura de Templates
```
app/Views/
├── layouts/
│   ├── app.html.twig          # Layout principal
│   ├── admin.html.twig        # Layout administrativo
│   └── reports.html.twig      # Layout para relatórios
├── pages/
│   ├── admin/                 # Páginas administrativas
│   ├── shop/                  # Páginas da loja
│   └── auth/                  # Páginas de autenticação
└── components/                # Componentes reutilizáveis
```

### 2. Extensões Customizadas
```php
class UrlExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('route', [$this, 'route']),
            new TwigFunction('asset', [$this, 'asset']),
        ];
    }
    
    public function route(string $name, array $params = []): string
    {
        return Router::url($name, $params);
    }
}
```

## 💾 Camada de Dados

### 1. Conexão com Banco
```php
class DatabaseConnection
{
    private static ?PDO $connection = null;
    
    public static function getInstance(): PDO
    {
        if (self::$connection === null) {
            $dsn = "mysql:host={$host};dbname={$database};charset=utf8mb4";
            self::$connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
        
        return self::$connection;
    }
}
```

### 2. Migrações
```php
class CreateProdutosTable extends Migration
{
    public function up(): void
    {
        $this->schema->create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao');
            $table->decimal('preco', 10, 2);
            $table->foreignId('empresa_id')->constrained();
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        $this->schema->dropIfExists('produtos');
    }
}
```

### 3. Models com Relacionamentos
```php
class Produto extends Model
{
    protected $fillable = ['nome', 'descricao', 'preco', 'empresa_id'];
    
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
    
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }
    
    public function pedidoItens(): HasMany
    {
        return $this->hasMany(PedidoItem::class);
    }
}
```

## 🔄 Fluxo de Requisição

### 1. Ciclo de Vida da Requisição
```
HTTP Request
    ↓
Router (route matching)
    ↓
Middleware Pipeline
    ↓
Controller Method
    ↓
Service Layer
    ↓
Repository/Model
    ↓
Database
    ↓
Response Formation
    ↓
View Rendering (Twig)
    ↓
HTTP Response
```

### 2. Tratamento de Erros
```php
class ErrorHandler
{
    public function handle(Throwable $exception): Response
    {
        $this->log($exception);
        
        if ($exception instanceof ValidationException) {
            return $this->validationError($exception);
        }
        
        if ($exception instanceof AuthenticationException) {
            return $this->authenticationError($exception);
        }
        
        return $this->serverError($exception);
    }
}
```

## 📊 Cache Strategy

### 1. Níveis de Cache
```php
// Application Cache
Cache::remember('produtos_empresa_' . $empresaId, 3600, function() {
    return Produto::where('empresa_id', $empresaId)->get();
});

// View Cache
$twig = new Environment($loader, [
    'cache' => 'storage/cache/twig',
    'auto_reload' => $debug
]);

// Database Query Cache
$produtos = DB::cache(3600)->table('produtos')->get();
```

### 2. Cache Invalidation
```php
class ProdutoService
{
    public function update(int $id, array $data): Produto
    {
        $produto = $this->repository->update($id, $data);
        
        // Invalidar caches relacionados
        Cache::forget('produtos_empresa_' . $produto->empresa_id);
        Cache::forget('produto_' . $id);
        
        return $produto;
    }
}
```

## 🌐 API Architecture

### 1. RESTful Design
```
GET    /api/produtos           # Listar
POST   /api/produtos           # Criar
GET    /api/produtos/{id}      # Obter
PUT    /api/produtos/{id}      # Atualizar
DELETE /api/produtos/{id}      # Deletar
```

### 2. Resource Transformers
```php
class ProdutoTransformer
{
    public function transform(Produto $produto): array
    {
        return [
            'id' => $produto->id,
            'nome' => $produto->nome,
            'preco' => $produto->preco,
            'empresa' => [
                'id' => $produto->empresa->id,
                'nome' => $produto->empresa->nome_fantasia,
            ],
        ];
    }
}
```

### 3. Rate Limiting
```php
class RateLimitMiddleware
{
    public function handle(Request $request): bool
    {
        $key = $this->resolveRequestSignature($request);
        $attempts = Cache::get($key, 0);
        
        if ($attempts >= $this->maxAttempts) {
            throw new TooManyRequestsException();
        }
        
        Cache::put($key, $attempts + 1, $this->decayMinutes * 60);
        
        return true;
    }
}
```

## 📱 Multi-Tenant Architecture

### 1. Tenant Resolution
```php
class EmpresaMiddleware
{
    public function handle(Request $request): bool
    {
        $empresaId = $this->resolveEmpresa($request);
        
        if (!$empresaId) {
            throw new TenantNotResolvedException();
        }
        
        app()->instance('current_empresa', $empresaId);
        
        return true;
    }
}
```

### 2. Scoped Queries
```php
trait EmpresaScope
{
    protected static function booted(): void
    {
        static::addGlobalScope('empresa', function (Builder $builder) {
            if ($empresaId = app('current_empresa')) {
                $builder->where('empresa_id', $empresaId);
            }
        });
    }
}
```

## 🧪 Testing Architecture

### 1. Test Structure
```
tests/
├── Unit/                      # Testes unitários
│   ├── Services/
│   ├── Models/
│   └── Validators/
├── Integration/               # Testes de integração
│   ├── Api/
│   └── Database/
└── Feature/                   # Testes de funcionalidade
    ├── Auth/
    └── Produtos/
```

### 2. Test Factories
```php
class ProdutoFactory
{
    public static function make(array $attributes = []): Produto
    {
        return new Produto(array_merge([
            'nome' => 'Produto Teste',
            'preco' => 99.90,
            'empresa_id' => 1,
        ], $attributes));
    }
}
```

## 📈 Performance Considerations

### 1. Database Optimization
- Índices estratégicos
- Query optimization
- Connection pooling
- Read replicas para relatórios

### 2. Application Optimization
- Lazy loading de relacionamentos
- Eager loading quando necessário
- Cache de queries frequentes
- Compressão de assets

### 3. Monitoring
```php
class PerformanceMiddleware
{
    public function handle(Request $request): bool
    {
        $start = microtime(true);
        
        // Process request...
        
        $duration = microtime(true) - $start;
        
        Log::info('Request processed', [
            'url' => $request->url(),
            'method' => $request->method(),
            'duration' => $duration,
            'memory' => memory_get_peak_usage(true),
        ]);
        
        return true;
    }
}
```

## 🔧 Configuration Management

### 1. Environment Configuration
```php
class Config
{
    private static array $config = [];
    
    public static function get(string $key, $default = null)
    {
        return data_get(self::$config, $key, $default);
    }
    
    public static function set(string $key, $value): void
    {
        data_set(self::$config, $key, $value);
    }
}
```

### 2. Feature Flags
```php
class FeatureFlag
{
    public static function isEnabled(string $feature): bool
    {
        return Config::get("features.{$feature}", false);
    }
}

// Uso
if (FeatureFlag::isEnabled('new_dashboard')) {
    return $this->newDashboard();
}
```

---

Esta arquitetura garante escalabilidade, manutenibilidade e performance para o sistema multi-empresas.
