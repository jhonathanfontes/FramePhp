<?php
// Lib/Mailer.php

namespace App\Lib;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Twig\Environment; // Para usar o Twig para renderizar templates de e-mail

class Mailer
{
    private $phpMailer;
    private $twig;
    private $fromAddress;
    private $fromName;

    public function __construct(Environment $twig, string $fromAddress, string $fromName = '')
    {
        $this->twig = $twig;
        $this->fromAddress = $fromAddress;
        $this->fromName = $fromName;

        $this->phpMailer = new PHPMailer(true); // O true habilita exceptions
        $this->configureSmtp();
    }

    private function configureSmtp(): void
    {
        // Carrega configurações de ambiente (simuladas, em um app real usaria um .env parser)
        $mailHost = getenv('MAIL_HOST') ?: 'smtp.gmail.com';
        $mailPort = getenv('MAIL_PORT') ?: 587;
        $mailUsername = getenv('MAIL_USERNAME') ?: 'seu_email@gmail.com'; // Use seu email Gmail real
        $mailPassword = getenv('MAIL_PASSWORD') ?: 'SUA_SENHA_DE_APLICATIVO_DO_GMAIL'; // Use sua senha de aplicativo Gmail real
        $mailEncryption = getenv('MAIL_ENCRYPTION') ?: 'tls';

        try {
            // Server settings
            $this->phpMailer->isSMTP();                                            // Send using SMTP
            $this->phpMailer->Host       = $mailHost;                              // Set the SMTP server to send through
            $this->phpMailer->SMTPAuth   = true;                                   // Enable SMTP authentication
            $this->phpMailer->Username   = $mailUsername;                          // SMTP username
            $this->phpMailer->Password   = $mailPassword;                          // SMTP password
            $this->phpMailer->SMTPSecure = $mailEncryption === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS; // Enable implicit TLS encryption
            $this->phpMailer->Port       = $mailPort;                              // TCP port to connect to

            // Set charset
            $this->phpMailer->CharSet = PHPMailer::CHARSET_UTF8;
            $this->phpMailer->setLanguage('pt_br'); // Set language to Portuguese (Brazil)

            // Debugging (only for development)
            // $this->phpMailer->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
            // $this->phpMailer->isHTML(true);
            // $this->phpMailer->setFrom($this->fromAddress, $this->fromName);

        } catch (Exception $e) {
            error_log("PHPMailer configuration error: {$e->getMessage()}");
            throw new Exception("Erro ao configurar o servidor de e-mail.");
        }
    }

    public function send(string $toEmail, string $toName, string $subject, string $templateName, array $templateData = []): bool
    {
        try {
            // Recipients
            $this->phpMailer->clearAllRecipients(); // Clear previous recipients
            $this->phpMailer->setFrom($this->fromAddress, $this->fromName);
            $this->phpMailer->addAddress($toEmail, $toName); // Add a recipient

            // Content
            $this->phpMailer->isHTML(true);                                  // Set email format to HTML
            $this->phpMailer->Subject = $subject;
            $this->phpMailer->Body    = $this->renderEmailTemplate($templateName, $templateData);
            $this->phpMailer->AltBody = strip_tags($this->phpMailer->Body); // Plain text for non-HTML mail clients

            $this->phpMailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$this->phpMailer->ErrorInfo}");
            return false;
        }
    }

    private function renderEmailTemplate(string $templateName, array $data): string
    {
        try {
            return $this->twig->render("emails/{$templateName}.html.twig", $data);
        } catch (Exception $e) {
            error_log("Error rendering email template '{$templateName}': {$e->getMessage()}");
            throw new Exception("Erro ao renderizar o template de e-mail.");
        }
    }
}