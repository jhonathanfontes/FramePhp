<?php

namespace Tests\Unit\Mail;

use Core\Mail\Mail;
use PHPUnit\Framework\TestCase;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailTest extends TestCase
{
    private Mail $mail;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mail = new Mail();
    }

    /**
     * Testa a configuração inicial do e-mail
     */
    public function testConfiguracaoInicial()
    {
        $this->assertInstanceOf(Mail::class, $this->mail);
    }

    /**
     * Testa o envio de e-mail básico
     */
    public function testEnvioEmailBasico()
    {
        $resultado = $this->mail
            ->para('teste@exemplo.com', 'Usuário Teste')
            ->assunto('Teste de E-mail')
            ->conteudo('Conteúdo do teste de e-mail')
            ->enviar();

        $this->assertTrue($resultado);
    }

    /**
     * Testa o envio de e-mail com anexo
     */
    public function testEnvioEmailComAnexo()
    {
        $caminhoAnexo = __DIR__ . '/arquivo_teste.txt';
        // Criar arquivo temporário para teste
        file_put_contents($caminhoAnexo, 'Conteúdo do arquivo de teste');

        try {
            $resultado = $this->mail
                ->para('teste@exemplo.com')
                ->assunto('Teste com Anexo')
                ->conteudo('E-mail com arquivo anexo')
                ->anexo($caminhoAnexo)
                ->enviar();

            $this->assertTrue($resultado);
        } finally {
            // Limpar arquivo temporário
            if (file_exists($caminhoAnexo)) {
                unlink($caminhoAnexo);
            }
        }
    }

    /**
     * Testa o envio de e-mail com cópia
     */
    public function testEnvioEmailComCopia()
    {
        $resultado = $this->mail
            ->para('principal@exemplo.com')
            ->cc('copia@exemplo.com')
            ->assunto('Teste CC')
            ->conteudo('E-mail com cópia')
            ->enviar();

        $this->assertTrue($resultado);
    }

    /**
     * Testa o envio de e-mail com cópia oculta
     */
    public function testEnvioEmailComCopiaOculta()
    {
        $resultado = $this->mail
            ->para('principal@exemplo.com')
            ->cco('copiaoculta@exemplo.com')
            ->assunto('Teste CCO')
            ->conteudo('E-mail com cópia oculta')
            ->enviar();

        $this->assertTrue($resultado);
    }

    /**
     * Testa o envio de e-mail com HTML
     */
    public function testEnvioEmailComHTML()
    {
        $conteudoHTML = '<h1>Título</h1><p>Parágrafo de teste</p>';
        
        $resultado = $this->mail
            ->para('teste@exemplo.com')
            ->assunto('Teste HTML')
            ->conteudo($conteudoHTML, true)
            ->enviar();

        $this->assertTrue($resultado);
    }

    /**
     * Testa a limpeza dos destinatários
     */
    public function testLimpezaDestinatarios()
    {
        $this->mail
            ->para('teste1@exemplo.com')
            ->cc('teste2@exemplo.com')
            ->cco('teste3@exemplo.com');

        $this->mail->limpar();

        // Aqui precisaríamos verificar se os destinatários foram realmente limpos
        // Como os atributos são privados, podemos testar tentando enviar o e-mail
        // que deve falhar sem destinatários
        
        $this->expectException(Exception::class);
        $this->mail
            ->assunto('Teste')
            ->conteudo('Conteúdo')
            ->enviar();
    }

    /**
     * Testa erro com endereço de e-mail inválido
     */
    public function testErroEmailInvalido()
    {
        $this->expectException(Exception::class);
        
        $this->mail
            ->para('email_invalido')
            ->assunto('Teste')
            ->conteudo('Conteúdo')
            ->enviar();
    }
}