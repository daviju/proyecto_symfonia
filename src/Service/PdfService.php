<?php
namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    public function generatePdf(string $html): string
    {
        // Configurar opciones de Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Retornar el contenido del PDF
        return $dompdf->output();
    }
}

/*
<div style="text-align: center;">
                <h1>Bienvenido ruinero</h1>
    
                <img src="%s" alt="Cani2" style="width: 300px; height: auto; margin-bottom: 20px;">
                
                <p>
                    Que pasa loco <br>
                    Man decio que ere el numero uno del barrio y quiero que vea er proyecto que tengo <br>
                    le pueto un surwofe arpine pa ke pete eso ai en er symfony ar masimo <br>
    
                    <br>
                    pero tengo un secretito, que me encanta la musica er bivardi <br>
                    .... solo ecusharlo .... <br>
    
                    yo lo flipo con lo repositorio, la entidade, lo metodo, la base de dato, lo adayo, la armonia, lo tempo <br>
    
                    <img src="%s" alt="Cani" style="width: 300px; height: auto; margin-bottom: 20px;">
    
                    Yo si ke malegro de aber conosio ar ar kabesa ese que iso symfony. <br>
    
                    lo repositorio y la base de dato automatica, flipante vamo, flipante <br>
    
                    Yo soy programado, pero programado barroco, eso de SpringBoot, Angular, nah <br>
    
                    eso son... papanata vamo eso no valen ni pa preguntarle ke miren a ve si lluebe
                </p>
                
                <br>
                
                <p>
                    Yo si ke malegro de aberte conosio Usuario.
                </p>
            </div>',
*/