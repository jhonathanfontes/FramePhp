<?php

namespace Core\Http;

class Request
{
    private array $get;
    private array $post;
    private array $server;
    private array $files;
    private array $cookies;
    private array $headers;
    private ?string $content;
    private array $attributes = [];

    public function __construct()
    {
        $this->get = $_GET ?? [];
        $this->post = $_POST ?? [];
        $this->server = $_SERVER ?? [];
        $this->files = $_FILES ?? [];
        $this->cookies = $_COOKIE ?? [];
        $this->headers = $this->getHeaders();
        $this->content = file_get_contents('php://input');
    }

    public function getMethod(): string
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    public function getUri(): string
    {
        return $this->server['REQUEST_URI'] ?? '/';
    }

    public function getPath(): string
    {
        $uri = $this->getUri();
        $position = strpos($uri, '?');
        
        if ($position === false) {
            return $uri;
        }
        
        return substr($uri, 0, $position);
    }

    public function getQuery(): array
    {
        return $this->get;
    }

    public function getQueryParam(string $key, $default = null)
    {
        return $this->get[$key] ?? $default;
    }

    public function getPost(): array
    {
        return $this->post;
    }

    public function input(string $key, $default = null)
    {
        return $this->post[$key] ?? $this->get[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->get, $this->post);
    }

    public function getFile(string $key)
    {
        return $this->files[$key] ?? null;
    }

    public function getCookie(string $key, $default = null)
    {
        return $this->cookies[$key] ?? $default;
    }

    public function getHeader(string $key, $default = null)
    {
        $key = strtolower($key);
        return $this->headers[$key] ?? $default;
    }

    public function getHeaders(): array
    {
        if (!empty($this->headers)) {
            return $this->headers;
        }

        $headers = [];
        foreach ($this->server as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $name = str_replace('_', '-', strtolower(substr($key, 5)));
                $headers[$name] = $value;
            }
        }

        return $headers;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getJson(): array
    {
        $content = $this->getContent();
        if (empty($content)) {
            return [];
        }

        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    public function isAjax(): bool
    {
        return $this->getHeader('X-Requested-With') === 'XMLHttpRequest';
    }

    public function isJson(): bool
    {
        return strpos($this->getHeader('Content-Type', ''), 'application/json') !== false;
    }

    public function getReferer(): ?string
    {
        return $this->server['HTTP_REFERER'] ?? null;
    }

    public function getIp(): string
    {
        return $this->server['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    public function getUserAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }

    public function setAttribute(string $key, $value): self
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    public function getAttribute(string $key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }

    public function hasAttribute(string $key): bool
    {
        return isset($this->attributes[$key]);
    }
}