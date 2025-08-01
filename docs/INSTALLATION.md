
# Guia de Instalação - Sistema Multi-Empresas

## 📋 Pré-requisitos

### Software Necessário
- **PHP 8.1 ou superior**
- **Composer** (gerenciador de dependências PHP)
- **MySQL 5.7 ou superior** (ou MariaDB equivalente)
- **Servidor Web** (Apache/Nginx - opcional, pode usar servidor embutido do PHP)

### Verificação do Ambiente
```bash
# Verificar versão do PHP
php --version

# Verificar extensões necessárias
php -m | grep -E "(pdo|pdo_mysql|mbstring|openssl|json|curl)"

# Verificar Composer
composer --version
```

## 🛠️ Instalação Passo a Passo

### 1. Obter o Código
```bash
# Clone do repositório
git clone https://github.com/seu-usuario/sistema-multi-empresas.git
cd sistema-multi-empresas

# Ou download direto
wget https://github.com/seu-usuario/sistema-multi-empresas/archive/main.zip
unzip main.zip
cd sistema-multi-empresas-main
```

### 2. Instalar Dependências
```bash
# Instalar dependências do Composer
composer install

# Para produção (sem dependências de desenvolvimento)
composer install --no-dev --optimize-autoloader
```

### 3. Configuração do Ambiente

#### Arquivo .env
```bash
# Copiar arquivo de exemplo
cp .env.example .env

# Editar configurações
nano .env
```

#### Configurações Essenciais
```env
# Informações da Aplicação
APP_NAME="Sistema Multi-Empresas"
APP_DEBUG=true  # false em produção
APP_URL=http://localhost:5000

# Banco de Dados
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=sistema_multiempresas
DB_USERNAME=root
DB_PASSWORD=sua_senha

# Chaves de Segurança (gerar novas)
APP_KEY=base64:sua_chave_secreta_aqui
JWT_SECRET=sua_chave_jwt_muito_secreta
```

### 4. Configuração do Banco de Dados

#### Criar Banco de Dados
```sql
-- MySQL/MariaDB
CREATE DATABASE sistema_multiempresas 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Criar usuário (opcional)
CREATE USER 'sistema_user'@'localhost' IDENTIFIED BY 'senha_segura';
GRANT ALL PRIVILEGES ON sistema_multiempresas.* TO 'sistema_user'@'localhost';
FLUSH PRIVILEGES;
```

#### Executar Migrações
```bash
# Executar todas as migrações
php artisan migrate

# Verificar status das migrações
php artisan migrate:status

# Popular dados iniciais (opcional)
php artisan seed
```

### 5. Configurar Permissões

#### Permissões de Pastas
```bash
# Linux/Mac
chmod -R 755 storage/
chmod -R 755 public/
chmod -R 777 storage/cache/
chmod -R 777 storage/logs/

# Criar pastas se não existirem
mkdir -p storage/cache/twig
mkdir -p storage/logs
mkdir -p storage/uploads
```

### 6. Iniciar o Servidor

#### Servidor Embutido do PHP (Desenvolvimento)
```bash
# Iniciar na porta 5000
php -S 0.0.0.0:5000 -t public

# Ou usar o comando artisan
php artisan serve
```

#### Apache Virtual Host
```apache
<VirtualHost *:80>
    ServerName sistema.local
    DocumentRoot "/caminho/para/sistema-multi-empresas/public"
    
    <Directory "/caminho/para/sistema-multi-empresas/public">
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/sistema_error.log
    CustomLog ${APACHE_LOG_DIR}/sistema_access.log combined
</VirtualHost>
```

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name sistema.local;
    root /caminho/para/sistema-multi-empresas/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 🔧 Configurações Avançadas

### Email (SMTP)
```env
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu_email@gmail.com
MAIL_PASSWORD=sua_senha_app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@sistema.com
MAIL_FROM_NAME="Sistema Multi-Empresas"
```

### Cache
```bash
# Configurar cache Redis (opcional)
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Logs
```env
LOG_CHANNEL=daily
LOG_LEVEL=debug  # error em produção
LOG_MAX_FILES=30
```

## 🧪 Verificação da Instalação

### Testes Básicos
```bash
# Executar testes
vendor/bin/phpunit

# Verificar rotas
php artisan route:list

# Verificar conexão com banco
php artisan migrate:status
```

### URLs de Teste
- **Home**: http://localhost:5000/
- **Admin**: http://localhost:5000/admin
- **API**: http://localhost:5000/api/health
- **Debug**: http://localhost:5000/debug.php

## 🚨 Solução de Problemas

### Problemas Comuns

#### Erro: "Class not found"
```bash
# Recriar autoload
composer dump-autoload
```

#### Erro: "Permission denied"
```bash
# Corrigir permissões
sudo chown -R www-data:www-data storage/
sudo chmod -R 775 storage/
```

#### Erro: "Database connection failed"
```bash
# Verificar configurações do .env
# Testar conexão MySQL
mysql -h localhost -u root -p

# Verificar se extensão PDO MySQL está instalada
php -m | grep pdo_mysql
```

#### Erro: "Twig cache directory not writable"
```bash
# Criar e dar permissão para cache
mkdir -p storage/cache/twig
chmod 777 storage/cache/twig
```

### Logs de Debug
```bash
# Verificar logs de erro
tail -f storage/logs/error.log

# Log do PHP
tail -f /var/log/apache2/error.log  # Apache
tail -f /var/log/nginx/error.log    # Nginx
```

## 🔒 Configurações de Segurança

### Produção
```env
# .env para produção
APP_DEBUG=false
APP_ENV=production

# HTTPS obrigatório
FORCE_HTTPS=true

# Headers de segurança
SECURITY_HEADERS=true
```

### Firewall
```bash
# Abrir apenas portas necessárias
ufw allow 22    # SSH
ufw allow 80    # HTTP
ufw allow 443   # HTTPS
ufw enable
```

## 📊 Monitoramento

### Health Check
```bash
# Endpoint de saúde
curl http://localhost:5000/api/health

# Verificar status do banco
curl http://localhost:5000/api/health/database
```

### Métricas
```bash
# Uso de memória
ps aux | grep php

# Espaço em disco
df -h

# Logs de acesso
tail -f storage/logs/access.log
```

## 📞 Suporte

Se encontrar problemas durante a instalação:

1. **Verifique os logs** em `storage/logs/`
2. **Consulte a FAQ** em `docs/FAQ.md`
3. **Abra uma issue** no GitHub
4. **Entre em contato** via email: suporte@sistema.com

---

**Instalação concluída com sucesso! 🎉**
