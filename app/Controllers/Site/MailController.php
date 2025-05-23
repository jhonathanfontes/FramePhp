<?php

namespace App\Controllers\Site;

use App\Services\EmailService;
use Core\Controller;
use Core\View\TwigManager;
use Core\Http\Response;

class MailController extends Controller
{
    private $emailService;
    private $twig;

    public function __construct()
    {
        $this->twig = TwigManager::getInstance();
        $this->emailService = new EmailService($this->twig);
    }

    public function index()
    {
        return $this->view('site/mail/index');
    }

    public function send()
    {
        try {
            $template = $_POST['template'] ?? '';
            $email = $_POST['email'] ?? '';
            $name = $_POST['name'] ?? '';

            if (empty($template) || empty($email) || empty($name)) {
                return (new Response())->redirect(base_url('mail'))->with('error', 'Todos os campos são obrigatórios');
            }

            switch ($template) {
                case 'welcome':
                    $status = $this->emailService->sendWelcomeEmail($email, $name);
                    break;

                case 'reset-password':
                    $token = bin2hex(random_bytes(32));
                    $status = $this->emailService->sendPasswordResetEmail($email, $name, $token);
                    break;

                case 'generic':
                    $subject = $_POST['subject'] ?? '';
                    $message = $_POST['message'] ?? '';
                    
                    if (empty($subject) || empty($message)) {
                        return (new Response())->redirect('/mail')->with('error', 'Assunto e mensagem são obrigatórios para e-mails genéricos');
                    }

                    $status = $this->emailService->sendGenericEmail($email, $name, $subject, $message);
                    break;

                default:
                    return (new Response())->redirect('/mail')->with('error', 'Template inválido');
            }

            if ($status['success']) {
                return (new Response())->redirect(base_url('mail'))->with('success', $status['message']);
            } else {
                $errorMessage = $status['error'] 
                    ? $status['message'] . ': ' . $status['error']
                    : $status['message'];
                return (new Response())->redirect(base_url('mail'))->with('error', $errorMessage);
            }

        } catch (\Exception $e) {
            error_log("Erro detalhado: " . $e->getMessage());
            return (new Response())->redirect(base_url('mail'))->with('error', 'Erro ao enviar e-mail: ' . $e->getMessage());
        }
    }
}