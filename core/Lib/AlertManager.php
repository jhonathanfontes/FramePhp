<?php

namespace Core\Lib;

/**
 * Sistema de Alertas Avançado para FramePhp
 * Baseado no AlertLib do SpeedPHP
 * 
 * DANGER - ERRO
 * WARNING - ATENÇÃO  
 * SUCCESS - EXECUTADO COM SUCESSO
 * INFO - INFORMAR
 */
class AlertManager
{
    protected const NOME_SESSAO = "ALERTMANAGER";
    protected const TYPES = ['danger', 'warning', 'success', 'info'];

    public function __construct()
    {
        $this->startSession();
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(self::NOME_SESSAO);
            session_start();
        }
    }

    private function endSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
    }

    private function setSessionAlert(string $alert, string $type): void
    {
        $this->startSession();
        $_SESSION['ALERTMANAGER_MSG'] = $alert;
        $_SESSION['ALERTMANAGER_TYPE'] = $type;
        $this->endSession();
    }

    private function clearSessionAlert(): void
    {
        $this->startSession();
        unset($_SESSION['ALERTMANAGER_MSG'], $_SESSION['ALERTMANAGER_TYPE']);
        $this->endSession();
    }

    /**
     * Exibe um alerta e redireciona
     */
    public function showAlert(string $type, string $message, string $redirect = null): void
    {
        if (!in_array($type, self::TYPES)) {
            $type = 'info';
        }

        $title = match ($type) {
            'danger' => 'Ops!',
            'warning' => 'Atenção!',
            'success' => 'Sucesso!',
            'info' => 'Informação!',
            default => 'Notificação'
        };

        $alert = "<script>window.onload = function () {iziToast.{$type}({title: '{$title}', message: '{$message}', position: 'bottomRight'});}</script>";

        $this->setSessionAlert($alert, $type);
        
        if ($redirect) {
            header("Location: $redirect");
            exit;
        }
    }

    /**
     * Alerta de erro
     */
    public function danger(string $message, string $redirect = null): void
    {
        $this->showAlert('danger', $message, $redirect);
    }

    /**
     * Alerta de atenção
     */
    public function warning(string $message, string $redirect = null): void
    {
        $this->showAlert('warning', $message, $redirect);
    }

    /**
     * Alerta de sucesso
     */
    public function success(string $message, string $redirect = null): void
    {
        $this->showAlert('success', $message, $redirect);
    }

    /**
     * Alerta informativo
     */
    public function info(string $message, string $redirect = null): void
    {
        $this->showAlert('info', $message, $redirect);
    }

    /**
     * Verifica se há alertas para exibir
     */
    public function checkAlert(): ?string
    {
        $this->startSession();
        $alert = $_SESSION['ALERTMANAGER_MSG'] ?? null;
        $this->endSession();
        $this->clearSessionAlert();
        return $alert;
    }

    /**
     * Exibe alerta inline sem redirecionamento
     */
    public function showInline(string $type, string $message): string
    {
        if (!in_array($type, self::TYPES)) {
            $type = 'info';
        }

        $title = match ($type) {
            'danger' => 'Ops!',
            'warning' => 'Atenção!',
            'success' => 'Sucesso!',
            'info' => 'Informação!',
            default => 'Notificação'
        };

        return "<script>iziToast.{$type}({title: '{$title}', message: '{$message}', position: 'bottomRight'});</script>";
    }

    /**
     * Exibe alerta toast simples
     */
    public function toast(string $type, string $message, string $position = 'bottomRight'): string
    {
        if (!in_array($type, self::TYPES)) {
            $type = 'info';
        }

        return "<script>iziToast.{$type}({message: '{$message}', position: '{$position}'});</script>";
    }
}
