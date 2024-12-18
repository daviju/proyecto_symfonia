<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendRegistrationEmail(string $to, string $username): void
    {
        $email = (new Email())
            ->from('no-reply@tests.com')
            ->to($to)
            ->subject('Bienvenido a la plataforma')
            ->text('Hola ' . $username . ', gracias por registrarte en nuestra plataforma.')
            ->html('<p>Hola <strong>' . $username . '</strong>, gracias por registrarte en nuestra plataforma.</p>');

        $this->mailer->send($email);
    }
}