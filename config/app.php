<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configurações da Aplicação
    |--------------------------------------------------------------------------
    */
    'name' => env('APP_NAME', 'FramePhp'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => env('APP_TIMEZONE', 'America/Sao_Paulo'),
    'locale' => env('APP_LOCALE', 'pt_BR'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'key' => env('APP_KEY', 'base64:'.base64_encode(random_bytes(32))),
    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Configurações de Segurança
    |--------------------------------------------------------------------------
    */
    'security' => [
        'csrf_protection' => true,
        'session_secure' => env('SESSION_SECURE', false),
        'session_http_only' => true,
        'session_same_site' => 'Lax',
        'password_timeout' => 10800, // 3 horas
        'max_login_attempts' => 5,
        'lockout_time' => 900, // 15 minutos
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Cache
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'default' => env('CACHE_DRIVER', 'file'),
        'ttl' => env('CACHE_TTL', 3600),
        'prefix' => env('CACHE_PREFIX', 'framephp_'),
        'enabled' => env('CACHE_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Sessão
    |--------------------------------------------------------------------------
    */
    'session' => [
        'driver' => env('SESSION_DRIVER', 'file'),
        'lifetime' => env('SESSION_LIFETIME', 120),
        'expire_on_close' => false,
        'encrypt' => false,
        'files' => BASE_PATH . '/storage/sessions',
        'connection' => env('SESSION_CONNECTION'),
        'table' => 'sessions',
        'store' => env('SESSION_STORE'),
        'lottery' => [2, 100],
        'cookie' => env(
            'SESSION_COOKIE',
            'framephp_session'
        ),
        'path' => '/',
        'domain' => env('SESSION_DOMAIN'),
        'secure' => env('SESSION_SECURE_COOKIE'),
        'http_only' => true,
        'same_site' => 'lax',
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Banco de Dados
    |--------------------------------------------------------------------------
    */
    'database' => [
        'default' => env('DB_CONNECTION', 'mysql'),
        'connections' => [
            'mysql' => [
                'driver' => 'mysql',
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => env('DB_DATABASE', 'framephp'),
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD', ''),
                'unix_socket' => env('DB_SOCKET', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => true,
                'engine' => null,
                'options' => extension_loaded('pdo_mysql') ? array_filter([
                    PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                ]) : [],
            ],
            'pgsql' => [
                'driver' => 'pgsql',
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '5432'),
                'database' => env('DB_DATABASE', 'framephp'),
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD', ''),
                'charset' => 'utf8',
                'prefix' => '',
                'prefix_indexes' => true,
                'schema' => 'public',
                'sslmode' => 'prefer',
            ],
            'sqlite' => [
                'driver' => 'sqlite',
                'database' => BASE_PATH . '/database/database.sqlite',
                'prefix' => '',
                'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Email
    |--------------------------------------------------------------------------
    */
    'mail' => [
        'default' => env('MAIL_MAILER', 'smtp'),
        'mailers' => [
            'smtp' => [
                'transport' => 'smtp',
                'host' => env('MAIL_HOST', 'smtp.gmail.com'),
                'port' => env('MAIL_PORT', 587),
                'encryption' => env('MAIL_ENCRYPTION', 'tls'),
                'username' => env('MAIL_USERNAME'),
                'password' => env('MAIL_PASSWORD'),
                'timeout' => null,
                'local_domain' => env('MAIL_EHLO_DOMAIN'),
            ],
            'ses' => [
                'transport' => 'ses',
            ],
            'mailgun' => [
                'transport' => 'mailgun',
            ],
            'log' => [
                'transport' => 'log',
                'channel' => env('MAIL_LOG_CHANNEL'),
            ],
            'array' => [
                'transport' => 'array',
            ],
        ],
        'from' => [
            'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
            'name' => env('MAIL_FROM_NAME', 'FramePhp'),
        ],
        'markdown' => [
            'theme' => 'default',
            'paths' => [
                BASE_PATH . '/app/Views',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Log
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'default' => env('LOG_CHANNEL', 'stack'),
        'channels' => [
            'stack' => [
                'driver' => 'stack',
                'channels' => ['single'],
                'ignore_exceptions' => false,
            ],
            'single' => [
                'driver' => 'single',
                'path' => BASE_PATH . '/storage/logs/framephp.log',
                'level' => env('LOG_LEVEL', 'debug'),
            ],
            'daily' => [
                'driver' => 'daily',
                'path' => BASE_PATH . '/storage/logs/framephp.log',
                'level' => env('LOG_LEVEL', 'debug'),
                'days' => 14,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Upload
    |--------------------------------------------------------------------------
    */
    'upload' => [
        'max_size' => env('UPLOAD_MAX_SIZE', 10240), // 10MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'],
        'disk' => env('UPLOAD_DISK', 'local'),
        'path' => 'uploads',
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de API
    |--------------------------------------------------------------------------
    */
    'api' => [
        'rate_limit' => env('API_RATE_LIMIT', 60),
        'rate_limit_window' => env('API_RATE_LIMIT_WINDOW', 60),
        'throttle' => true,
        'version' => 'v1',
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de reCAPTCHA
    |--------------------------------------------------------------------------
    */
    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
        'enabled' => env('RECAPTCHA_ENABLED', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de JWT
    |--------------------------------------------------------------------------
    */
    'jwt' => [
        'secret' => env('JWT_SECRET'),
        'ttl' => env('JWT_TTL', 43200), // 12 horas
        'refresh_ttl' => env('JWT_REFRESH_TTL', 20160), // 14 dias
        'algo' => 'HS256',
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Cores (Tema)
    |--------------------------------------------------------------------------
    */
    'colors' => [
        'primary' => '#3B82F6',
        'secondary' => '#6B7280',
        'success' => '#10B981',
        'danger' => '#EF4444',
        'warning' => '#F59E0B',
        'info' => '#3B82F6',
        'light' => '#F9FAFB',
        'dark' => '#111827',
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Paginação
    |--------------------------------------------------------------------------
    */
    'pagination' => [
        'per_page' => env('PAGINATION_PER_PAGE', 15),
        'max_per_page' => env('PAGINATION_MAX_PER_PAGE', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Timezone
    |--------------------------------------------------------------------------
    */
    'timezones' => [
        'America/Sao_Paulo' => 'São Paulo (UTC-3)',
        'America/New_York' => 'New York (UTC-5)',
        'Europe/London' => 'London (UTC+0)',
        'Asia/Tokyo' => 'Tokyo (UTC+9)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Manutenção
    |--------------------------------------------------------------------------
    */
    'maintenance' => [
        'enabled' => env('MAINTENANCE_MODE', false),
        'secret' => env('MAINTENANCE_SECRET'),
        'allowed_ips' => explode(',', env('MAINTENANCE_ALLOWED_IPS', '127.0.0.1')),
    ],
];