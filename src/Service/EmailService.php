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
            $baseUrl = $request ? $request->getSchemeAndHttpHost() : 'http://default-url.com'; // Valor por defecto si no hay solicitud

            // Rutas de las imágenes a incluir
            $images = [
                'cani' => __DIR__ . '/../../public/images/cani.jpg',
                'cani2' => __DIR__ . '/../../public/images/cani2.jpg',
            ];

            // Convertir las imágenes a base64
            $imageSrc = [];
            foreach ($images as $name => $path) {
                if (file_exists($path)) {
                    $imageData = base64_encode(file_get_contents($path));
                    $imageSrc[$name] = 'data:image/jpeg;base64,' . $imageData;
                }
            }

            // Generar el contenido HTML para el PDF, incluyendo las imágenes base64
            $html = sprintf(
                '
            <div style="text-align: center;">
                <h1>Bienvenido ruinero</h1>

                <img src="%s" alt="Cani barroco" style="width: 300px; height: auto; margin-bottom: 20px;">
                
                <p>
                    Que pasa loco <br> <br> 
                    Man decio que ere el numero uno del barrio y quiero que vea er proyecto que tengo <br>
                    le pueto un surwofe arpine pa ke pete eso ai en er symfony ar masimo <br>

                    <br>
                    Pero tengo un secretito, que me encanta la cosa der symfony <br>
                    .... solo ecusharlo .... <br>

                    yo lo flipo con lo repositorio, la entidade, lo metodo, la base de dato, lo adayo, la armonia, lo tempo <br>
                </p>

                <img src="%s" alt="Imagen de bienvenida" style="width: 300px; height: auto; margin-bottom: 20px;">

                <p>
                    Yo si ke malegro de aber conosio ar ar kabesa ese que iso symfony, <br>

                    lo repositorio y la base de dato automatica, flipante vamo, flipante <br>

                    <br>
                    Yo soy programado, pero programado barroco, eso de SpringBoot, Angular, nah <br>

                    eso son... papanata vamo eso no valen ni pa preguntarle ke miren a ve si lluebe
                </p>
                
                <br>
                
                <h1>
                    Yo si ke malegro de aberte conosio Usuario.
                </h1>
            </div>',
                $imageSrc['cani'] ?? '', // Mostrar imagen 'cani'
                $imageSrc['cani2'] ?? ''  // Mostrar imagen 'cani2'
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
