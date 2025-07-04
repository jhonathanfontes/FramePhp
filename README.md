# FramePhp 

Framework PHP simples, inspirado em boas práticas modernas.  
Inclui suporte a Twig, arquitetura modular, testes automatizados e internacionalização.

## Características

- Arquitetura MVC
- Sistema de roteamento simples e poderoso
- ORM integrado para manipulação de banco de dados
- Sistema de templates com Twig
- Middleware para controle de acesso
- Sistema de autenticação integrado
- Validação de formulários
- Sistema de cache
- Tratamento de erros e exceções
- Suporte a sessões e cookies
- Suporte a migrações de banco de dados

## Requisitos

- PHP 8.0 ou superior
- Composer
- MySQL 5.7 ou superior (ou outro banco de dados compatível)

## Instalação

```bash
composer install
```

## Primeiros Passos

1. Clone este repositório.
2. Copie `.env.example` para `.env` e ajuste as configurações.
3. Execute o servidor embutido:
   ```bash
   php -S localhost:8080 -t public
   ```

## Testes

```bash
vendor/bin/phpunit
```

## Estrutura de Pastas

- `app/` - Código principal da aplicação
- `public/` - Ponto de entrada (index.php)
- `tests/` - Testes automatizados
- `config/` - Configurações
- `resources/lang/` - Arquivos de tradução

## Contribuição

1. Crie um fork
2. Crie um branch: `git checkout -b minha-melhoria`
3. Faça commit das suas mudanças: `git commit -am 'feat: Minha melhoria'`
4. Faça push para o branch: `git push origin minha-melhoria`
5. Abra um Pull Request

---