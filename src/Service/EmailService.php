<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Psr\Log\LoggerInterface;
use App\Service\PdfService;
use Symfony\Component\HttpFoundation\RequestStack;

class EmailService
{
    private $mailer;
    private $logger;
    private $pdfService;
    private $requestStack;

    public function __construct(MailerInterface $mailer, LoggerInterface $logger, PdfService $pdfService, RequestStack $requestStack)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->pdfService = $pdfService;
        $this->requestStack = $requestStack;
    }

    public function sendRegistrationEmail(string $to): bool
    {
        try {
            // Obtener la URL base del servidor
            $request = $this->requestStack->getCurrentRequest();
            $baseUrl = $request->getSchemeAndHttpHost(); // Ejemplo: http://127.0.0.1:8000

            // Leer el archivo de imagen y convertirlo a base64
            $imagePath = __DIR__ . '/../../public/images/barsinso.jpg'; // Ruta desde public/
            $imageData = base64_encode(file_get_contents($imagePath));
            $imageSrc = 'data:image/jpeg;base64,' . $imageData;

            // Generar el contenido HTML para el PDF, incluyendo la imagen base64
            $html = sprintf(
                '
            <div style="text-align: center;">
                <h1>¡Buenos dias mi loco!</h1>
                <p>Gracias por registrarte.</p>
                <img src="%s" alt="Imagen de bienvenida" style="width: 300px; height: auto; margin-bottom: 20px;">
                <br>
                <p>
                    Con un porrito en la mano yo me lo lío <br>
                    Con esa raya de coca que me he metido <br>
                    Me he dado más de mil tirones y no me han cogido <br>
                    Y el cero noventa y uno pa ti pa mi es pan comido <br>
                    No tengo miedo los voy a matar <br>
                    A esos mamones de la policía <br>
                    Que desde el día que me cogieron <br>
                    A mi me llevaron pa comisaría <br>
                    Y yo por ser menor de edad <br>
                    A mí me dieron la libertad <br>
                    Vivo en un barrio de vacilillas <br>
                    Se dan la fuga y a toda pastilla <br>
                    Vivo en un barrio de vacilones <br>
                    De mi cuadrilla son todos ladrones <br>
                    Lerelerele lerelelei <br>
                    Lerelerele lerelela <br>
                    Lerelerele lerelelei <br>
                    Lerelerele lerelela <br>
                    Lerelerele lerelelei <br>
                    Y el cero noventa y uno pa ti pa mi es pan comido <br>
                    Lerelerele lerelela <br>
                    No tengo miedo los voy a matar <br>
                    A esos mamones de la policía <br>
                    Que desde el día que me cogieron <br>
                    A mí me llevaron pa comisaría <br>
                    Y yo por ser menor de edad <br>
                    A mí me dieron la libertad <br>
                    A mí me dieron la libertad <br>
                    Lerelerele lerelelei <br>
                    Lerelerele lerelela <br>
                    A mi me dieron la libertad <br>
                    A mí me dieron la libertad <br>
                </p>
            </div>',
                $imageSrc
            );

            // Generar el PDF utilizando PdfService
            $pdfContent = $this->pdfService->generatePdf($html);

            // Crear el correo
            $email = (new Email())
                ->from('no-reply@gmail.com')
                ->to($to)
                ->subject('Bienvenido a la plataforma')
                ->text('Gracias por registrarte en nuestra plataforma.')
                ->html('<p>Gracias por registrarte en nuestra plataforma. Consulta el archivo adjunto para más información.</p>')
                ->attach($pdfContent, 'registro.pdf', 'application/pdf'); // Adjuntar el PDF

            // Enviar el correo
            $this->mailer->send($email);
            $this->logger->info('Email de registro enviado exitosamente', ['to' => $to]);

            return true;
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Error al enviar email de registro', [
                'error' => $e->getMessage(),
                'to' => $to
            ]);
            return false;
        }
    }
}
