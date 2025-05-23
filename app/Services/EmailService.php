<?php

namespace App\Services;

use Core\Mail\Mail;
use Core\View\TwigManager;
use App\Models\EmailLog;

class EmailService
{
    private $twig;
    private $mail;
    private $status = [
        'success' => false,
        'message' => '',
        'error' => null
    ];

    public function __construct(TwigManager $twig)
    {
        $this->twig = $twig->getTwig();
        $this->mail = new Mail();
    }

    private function setStatus($success, $message, $error = null)
    {
        $this->status = [
            'success' => $success,
            'message' => $message,
            'error' => $error
        ];
        return $this->status;
    }

    private function logEmail($template, $email, $name, $subject, $content, $success, $errorMessage = null)
    {
        return EmailLog::logEmail([
            'template' => $template,
            'recipient_email' => $email,
            'recipient_name' => $name,
            'subject' => $subject,
            'content' => $content,
            'status' => $success ? 'success' : 'error',
            'error_message' => $errorMessage
        ]);
    }

    public function sendWelcomeEmail($to, $name)
    {
        try {
            $template = 'emails/welcome.twig';
            $data = ['name' => $name];

            $subject = $this->twig->render($template, $data, 'subject');
            $body = $this->twig->render($template, $data, 'body');

            $resultado = $this->mail
                ->para($to)
                ->assunto($subject)
                ->conteudo($body, true)
                ->enviar();

            $this->logEmail('welcome', $to, $name, $subject, $body, $resultado);

            if ($resultado) {
                return $this->setStatus(true, 'E-mail de boas-vindas enviado com sucesso');
            }

            return $this->setStatus(false, 'Falha ao enviar e-mail de boas-vindas');
        } catch (\Exception $e) {
            $this->logEmail('welcome', $to, $name, $subject ?? '', $body ?? '', false, $e->getMessage());
            return $this->setStatus(false, 'Erro ao enviar e-mail de boas-vindas', $e->getMessage());
        }
    }

    public function sendGenericEmail($to, $name, $subject, $message)
    {
        try {
            $template = 'emails/generic.twig';
            $data = [
                'name' => $name,
                'subject' => $subject,
                'message' => $message
            ];

            $body = $this->twig->render($template, $data);

            $resultado = $this->mail
                ->para($to)
                ->assunto($subject)
                ->conteudo($body, true)
                ->enviar();

            if ($resultado) {
                return $this->setStatus(true, 'E-mail enviado com sucesso');
            }

            return $this->setStatus(false, 'Falha ao enviar e-mail');
        } catch (\Exception $e) {
            return $this->setStatus(false, 'Erro ao enviar e-mail', $e->getMessage());
        }
    }

    public function sendPasswordResetEmail($email, $name, $token)
    {
        try {
            $template = 'emails/reset-password.twig';
            $data = [
                'name' => $name,
                'token' => $token,
                'app_name' => function() { return env('APP_NAME', 'FramePhp'); },
                'base_url' => function($path = '') { return rtrim(env('APP_URL', 'http://localhost'), '/') . '/' . ltrim($path, '/'); }
            ];

            $subject = 'Recuperação de Senha';
            $body = $this->twig->render($template, $data);

            $resultado = $this->mail
                ->para($email)
                ->assunto($subject)
                ->conteudo($body, true)
                ->enviar();

            $this->logEmail('reset-password', $email, $name, $subject, $body, $resultado);

            if ($resultado) {
                return $this->setStatus(true, 'E-mail de recuperação de senha enviado com sucesso');
            }

            return $this->setStatus(false, 'Falha ao enviar e-mail de recuperação de senha');
        } catch (\Exception $e) {
            $this->logEmail('reset-password', $email, $name, $subject ?? '', $body ?? '', false, $e->getMessage());
            return $this->setStatus(false, 'Erro ao enviar e-mail de recuperação de senha', $e->getMessage());
        }
    }
}