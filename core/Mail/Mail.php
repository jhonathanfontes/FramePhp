<?php

namespace Core\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mail
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configurarMailer();
    }

    private function configurarMailer(): void
    {
        try {
            // Configurações do servidor
            $this->mailer->isSMTP();
            $this->mailer->Host = env('MAIL_HOST');
            $this->mailer->SMTPAuth = env('MAIL_USERNAME') && env('MAIL_PASSWORD'); // Só habilita se tiver credenciais
            if ($this->mailer->SMTPAuth) {
                $this->mailer->Username = env('MAIL_USERNAME');
                $this->mailer->Password = env('MAIL_PASSWORD');
            }
            $this->mailer->SMTPSecure = env('MAIL_ENCRYPTION');
            $this->mailer->Port = env('MAIL_PORT');
            $this->mailer->CharSet = 'UTF-8';

            // Remetente padrão
            $this->mailer->setFrom(
                env('MAIL_FROM_ADDRESS'),
                env('MAIL_FROM_NAME')
            );
        } catch (Exception $e) {
            throw new Exception("Erro ao configurar o mailer: {$e->getMessage()}");
        }
    }

    public function para(string $endereco, string $nome = ''): self
    {
        $this->mailer->addAddress($endereco, $nome);
        return $this;
    }

    public function assunto(string $assunto): self
    {
        $this->mailer->Subject = $assunto;
        return $this;
    }

    public function conteudo(string $conteudo, bool $isHtml = true): self
    {
        $this->mailer->isHTML($isHtml);
        $this->mailer->Body = $conteudo;
        
        if ($isHtml) {
            $this->mailer->AltBody = strip_tags($conteudo);
        }
        
        return $this;
    }

    public function anexo(string $caminho, string $nome = ''): self
    {
        try {
            $this->mailer->addAttachment($caminho, $nome);
        } catch (Exception $e) {
            throw new Exception("Erro ao adicionar anexo: {$e->getMessage()}");
        }
        return $this;
    }

    public function cc(string $endereco, string $nome = ''): self
    {
        $this->mailer->addCC($endereco, $nome);
        return $this;
    }

    public function cco(string $endereco, string $nome = ''): self
    {
        $this->mailer->addBCC($endereco, $nome);
        return $this;
    }

    public function enviar(): bool
    {
        try {
            return $this->mailer->send();
        } catch (Exception $e) {
            throw new Exception("Erro ao enviar e-mail: {$e->getMessage()}");
        }
    }

    public function limpar(): void
    {
        $this->mailer->clearAddresses();
        $this->mailer->clearAttachments();
        $this->mailer->clearCCs();
        $this->mailer->clearBCCs();
    }
}