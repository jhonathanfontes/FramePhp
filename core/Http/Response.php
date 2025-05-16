<?php

namespace Core\Http;

class Response
{
    private string $content;
    private int $statusCode;
    private array $headers;

    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = array_merge([
            'Content-Type' => 'text/html; charset=UTF-8'
        ], $headers);
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function removeHeader(string $name): self
    {
        unset($this->headers[$name]);
        return $this;
    }

    /**
     * Redireciona para uma URL específica
     *
     * @param string $url
     * @param int $statusCode
     * @return Response
     */
    public function redirect(string $url, int $statusCode = 302): Response
    {
        header('Location: ' . $url, true, $statusCode);
        // Retorna $this para permitir encadeamento de métodos e garantir
        // que o tipo de retorno seja Response
        return $this;
    }

    public function json(array $data, int $statusCode = 200): self
    {
        $this->setHeader('Content-Type', 'application/json');
        $this->setContent(json_encode($data));
        $this->setStatusCode($statusCode);
        return $this;
    }

    public function send(): void
    {
        // Enviar código de status
        http_response_code($this->statusCode);

        // Enviar headers
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        // Enviar conteúdo
        echo $this->content;
        exit;
    }

    public static function html(string $content, int $statusCode = 200): self
    {
        return new self($content, $statusCode, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public static function jsonResponse(array $data, int $statusCode = 200): self
    {
        $response = new self(json_encode($data), $statusCode);
        $response->setHeader('Content-Type', 'application/json');
        return $response;
    }

    public static function download(string $filePath, string $fileName = null): self
    {
        if (!file_exists($filePath)) {
            return new self('Arquivo não encontrado', 404);
        }

        $fileName = $fileName ?? basename($filePath);
        $mimeType = mime_content_type($filePath) ?? 'application/octet-stream';
        $fileSize = filesize($filePath);

        $response = new self(file_get_contents($filePath));
        $response->setHeader('Content-Type', $mimeType);
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $response->setHeader('Content-Length', (string) $fileSize);
        
        return $response;
    }

    public static function redirectResponse(string $url, int $statusCode = 302): self
    {
        return (new self('', $statusCode))->setHeader('Location', $url);
    }
}