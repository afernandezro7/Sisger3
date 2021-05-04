<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Belraysa\BackendBundle\Controller\FPDF;

use Belraysa\BackendBundle\Controller\FPDF\FPDF;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Description of PDFController
 *
 * @author cjfigueiras
 */
class PDFController extends FPDF
{

    function __construct($orientation = 'L', $unit = 'mm', $size = 'A4')
    {
        // Llama al constructor de la clase padre
        parent:: __construct($orientation, $unit, $size);
    }

    // Voucher
    function Voucher($voucher, $image)
    {
        // Colores, ancho de línea y fuente en negrita
        $this->SetXY(10, 10);
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(255, 255, 255); //Fondo blanco de celda
        $this->SetTextColor(0, 0, 0); //Letra color negro
        // Cabecera


        $this->Image($image, 20, 15, 50);
        $this->SetXY(35, 27);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(33, 15, utf8_decode('RUC 1866733-1-716356 DV 40'), 0, 0, 'R');

        $this->RoundedRect(10, 10, 230, 100, 10, 'D', '1234');

        $this->SetXY(100, 15);

        //   $this->Cell(50, 20, utf8_decode($address), 1, 0, 'C');
        // Primero me guardo las coordenadas donde comienza la celda multilinea.
        $y1 = $this->GetY();
        $x1 = $this->GetX();
        $this->SetFont('Arial', 'B', 10);
        // Pinto la celda multilinea.
        $this->MultiCell(80, 5, utf8_decode("Calle Eric del Valle, Edificio Viky 2 e/ Via Argentina y Calle Alberto Navarro El Cangrejo, Bella Vista, Ciudad Panama"), 0, 'R', false);
        // Obtengo la posición vertical donde termina la celda y así saco el tamaño de alto de celda para aplicarlo al resto de las celdas de la misma fila.
        $y2 = $this->GetY();

        // Por último, me sitúo en las coordenadas correspondientes a la misma fila y siguiente columna.
        $this->SetXY($x1, $y2);
        // Pinto la celda multilinea.
        $this->MultiCell(80, 5, utf8_decode('Tel. 214 3509 / info@belraysatours.com'), 0, 'R', false);
        $this->SetXY(10, $y2 + 5);
        $this->MultiCell(80, 3, "", 0, 'R', false);
        $y2 = $this->GetY();
        $this->SetXY($x1 + 83, $y1 - 5);
        $this->MultiCell(80, 30, utf8_decode('   REF.'), 'L', 'L', false);
        $this->SetXY(10, $y2);
        $this->Cell(173, 10, utf8_decode('  PROVEEDOR:          ' . $voucher->getProvider()), 'RTB', 0, 'L');
        $this->SetXY(183, $y2);
        $this->SetFont('Arial', 'B', 8);
        $now = $voucher->getCreationDate();
        $now = date_format($now, 'd/m/Y');
        $day = substr($now, 0, 2);
        $month = substr($now, 3, 2);
        $year = substr($now, 6, 4);

        $this->Cell(19, 6.8, utf8_decode(' DIA: ' . $day), 'LT', 0, 'R');
        $this->Cell(19, 6.8, utf8_decode(' MES: ' . $month), 'RTL', 0, 'R');
        $this->Cell(19, 6.8, utf8_decode(' AÑO: ' . $year), 'RT', 0, 'R');
        $this->SetXY(183, $y2 + 7);
        $this->Cell(19, 3, utf8_decode(''), 'LB', 0, 'R');
        $this->Cell(19, 3, utf8_decode(''), 'RBL', 0, 'R');
        $this->Cell(19, 3, utf8_decode(''), 'RB', 0, 'R');
        $this->SetXY(10, $y2 + 10);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(230, 10, utf8_decode('  CLIENTE:      ' . $voucher->getClient()->getFirstName() . ' ' . $voucher->getClient()->getLastName()), 'B', 0, 'L');
        $this->SetXY(10, $y2 + 20);
        $this->Cell(76, 10, utf8_decode('  CANT. PERSONAS:     ' . $voucher->getPax()), 'B', 0, 'L');
        $inDate = $voucher->getBeginDate();
        $dateIn = date_format($inDate, 'd-m-Y');
        $this->Cell(76, 10, utf8_decode('  ENTRADA:      ') . $dateIn, 'BLR', 0, 'L');
        $outDate = $voucher->getFinishDate();
        $dateOut = date_format($outDate, 'd-m-Y');
        $this->Cell(78, 10, utf8_decode('  SALIDA:      ') . $dateOut, 'B', 0, 'L');
        $this->SetXY(10, $y2 + 30);
        $services = $voucher->getServices();
        $stringSevices = "";
        foreach ($services as $service) {
            $stringSevices = $stringSevices . $service->getName() . '      ';
        }
        $this->Cell(78, 10, utf8_decode('  SERVICIOS:      ' . $stringSevices), 0, 0, 'L');
        $this->SetXY(50, 102);
        $this->SetFont('Arial', 'BI', 6);

        $this->Cell(78, 10, utf8_decode('ESTE DOCUMENTO NO ES VÁLIDO SIN FIRMA Y CUÑOS AUTORIZADOS'), 0, 0, 'L');

        $this->SetFont('Arial', 'B', 10);
        $this->SetXY(162, $y2 + 62);
        $this->Cell(78, 10, utf8_decode('FIRMA Y CUÑO    '), 'T', 0, 'R');
    }

    // Receipe
    function Receipe($receipe, $image)
    {
        // Colores, ancho de línea y fuente en negrita
        $this->SetXY(10, 10);
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(255, 255, 255); //Fondo blanco de celda
        $this->SetTextColor(0, 0, 0); //Letra color negro
        // Cabecera


        $this->Image($image, 15, 15, 40);


        $this->RoundedRect(10, 10, 125, 97, 10, 'D', '1234');

        $this->SetXY(60, 15);

        //   $this->Cell(50, 20, utf8_decode($address), 1, 0, 'C');
        // Primero me guardo las coordenadas donde comienza la celda multilinea.
        $y1 = $this->GetY();
        $x1 = $this->GetX();
        $this->SetFont('Arial', 'B', 9);
        // Pinto la celda multilinea.
        $this->MultiCell(70, 4, utf8_decode("Calle Eric del Valle, Edificio Viky 2 e/ Via Argentina y Calle Alberto Navarro El Cangrejo, Bella Vista, Ciudad Panama"), 0, 'R', false);
        // Obtengo la posición vertical donde termina la celda y así saco el tamaño de alto de celda para aplicarlo al resto de las celdas de la misma fila.
        $y2 = $this->GetY();

        // Por último, me sitúo en las coordenadas correspondientes a la misma fila y siguiente columna.
        $this->SetXY($x1, $y2);
        // Pinto la celda multilinea.
        $this->MultiCell(70, 5, utf8_decode('Tel. 214 3509 / info@belraysatours.com'), 0, 'R', false);
        $this->SetXY(10, $y2 + 5);
        $this->MultiCell(80, 3, "", 0, 'R', false);
        $y2 = $this->GetY();

        $this->SetXY(35, $y1 + 10);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(21, 10, utf8_decode(' RUC 1866733-1-716356 DV 40'), 0, 0, 'R');

        $this->SetXY(10, $y2);
        $now = $receipe->getCreationDate();
        $now = date_format($now, 'd/m/Y');
        $day = substr($now, 0, 2);
        $month = substr($now, 3, 2);
        $year = substr($now, 6, 4);
        $this->Cell(28, 5, utf8_decode(' DIA: ' . $day), 'T', 0, 'L');
        $this->Cell(28, 5, utf8_decode(' MES: ' . $month), 'LT', 0, 'L');
        $this->Cell(28, 5, utf8_decode(' AÑO: ' . $year), 'LT', 0, 'L');
        $this->Cell(41, 5, utf8_decode(' IMPORTE'), 'LT', 0, 'L');

        $this->SetXY(10, $y2 + 5.2);
        $this->Cell(28, 5, utf8_decode(''), 'B', 0, 'R');
        $this->Cell(28, 5, utf8_decode(''), 'LB', 0, 'R');
        $this->Cell(28, 5, utf8_decode(''), 'LB', 0, 'R');
        $this->Cell(41, 5, utf8_decode(''), 'LB', 0, 'R');
        $this->SetXY(10, $y2 + 10);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(125, 10, utf8_decode('  RECIBÍ DE      ' . $receipe->getRecibide()), 'B', 0, 'L');
        $this->SetXY(10, $y2 + 20);
        $this->Cell(125, 10, utf8_decode('  LA SUMA DE      ' . '' . $receipe->getSuma()), 'B', 0, 'L');
        $this->SetXY(10, $y2 + 30);
        if (sizeof($receipe->getConcepto()) > 45) {

        }
        $this->Cell(125, 10, utf8_decode('  EN CONCEPTO DE      ' . $receipe->getConcepto()), 'B', 0, 'L');
        $this->SetXY(10, $y2 + 40);
        $this->Cell(125, 10, utf8_decode('  '), 'B', 0, 'L');
        $this->SetXY(10, $y2 + 50);
        $this->Cell(84, 10, utf8_decode('  '), 'BR', 0, 'L');

        $this->SetXY(10, $y2 + 60);
        $this->Cell(84, 10, utf8_decode(utf8_decode('     No. 0549')), 0, 0, 'L');
        $this->SetX(65);
        $this->Cell(84, 10, utf8_decode(utf8_decode(' RECIBIDO POR')), 0, 0, 'L');
    }

    function VoucherAndReceipe($address, $logo, $contact, $isBelraysa, $quantity, $reservations)
    {
        for ($i = 0; $i < $quantity; $i++) {
            $this->Voucher($address, $logo, $contact, $isBelraysa);
            if ($i + 1 != $quantity) {
                $this->AddPage();
            }
        }
        $this->AddPage();
        $this->Receipe($address, $logo, $contact, $isBelraysa);
    }

    function RoundedRect($x, $y, $w, $h, $r, $style = '', $angle = '1234')
    {
        $k = $this->k;
        $hp = $this->h;
        if ($style == 'F')
            $op = 'f';
        elseif ($style == 'FD' or $style == 'DF')
            $op = 'B';
        else
            $op = 'S';
        $MyArc = 4 / 3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2f %.2f m', ($x + $r) * $k, ($hp - $y) * $k));

        $xc = $x + $w - $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2f %.2f l', $xc * $k, ($hp - $y) * $k));
        if (strpos($angle, '2') === false)
            $this->_out(sprintf('%.2f %.2f l', ($x + $w) * $k, ($hp - $y) * $k));
        else
            $this->_Arc($xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc);

        $xc = $x + $w - $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2f %.2f l', ($x + $w) * $k, ($hp - $yc) * $k));
        if (strpos($angle, '3') === false)
            $this->_out(sprintf('%.2f %.2f l', ($x + $w) * $k, ($hp - ($y + $h)) * $k));
        else
            $this->_Arc($xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r);

        $xc = $x + $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2f %.2f l', $xc * $k, ($hp - ($y + $h)) * $k));
        if (strpos($angle, '4') === false)
            $this->_out(sprintf('%.2f %.2f l', ($x) * $k, ($hp - ($y + $h)) * $k));
        else
            $this->_Arc($xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc);

        $xc = $x + $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2f %.2f l', ($x) * $k, ($hp - $yc) * $k));
        if (strpos($angle, '1') === false) {
            $this->_out(sprintf('%.2f %.2f l', ($x) * $k, ($hp - $y) * $k));
            $this->_out(sprintf('%.2f %.2f l', ($x + $r) * $k, ($hp - $y) * $k));
        } else
            $this->_Arc($xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', $x1 * $this->k, ($h - $y1) * $this->k, $x2 * $this->k, ($h - $y2) * $this->k, $x3 * $this->k, ($h - $y3) * $this->k));
    }

    static public function ExportVoucherAsPDFAction($voucher, $image)
    {
        //Creacion del objeto de la clase heredada

        $pdf = new PDFController();       //  instancio la clase

        $pdf->AliasNbPages();       //  metodo que obtiene los numeros de las paginas
        $pdf->AddPage();            //  adiciona una nueva pagina

        /*
          I: envía el fichero al navegador de forma que se usa la extensión (plug in) si está disponible. El nombre dado en nombre se usa si el usuario escoge la opción "Guardar como..." en el enlace que genera el PDF.
          D: envía el fichero al navegador y fuerza la descarga del fichero con el nombre especificado por nombre.
          F: guarda el fichero en un fichero local de nombre nombre.
          S: devuelve el documento como una cadena. nombre se ignora.
         */


        $pdf->Voucher($voucher, $image);
        $pdf->Close();
        return $pdf->Output("voucher.pdf", "D");


    }

    static public function ExportReceipeAsPDFAction($receipe, $image)
    {
        //Creacion del objeto de la clase heredada

        $pdf = new PDFController();       //  instancio la clase

        $pdf->AliasNbPages();       //  metodo que obtiene los numeros de las paginas
        $pdf->AddPage();            //  adiciona una nueva pagina

        /*
          I: envía el fichero al navegador de forma que se usa la extensión (plug in) si está disponible. El nombre dado en nombre se usa si el usuario escoge la opción "Guardar como..." en el enlace que genera el PDF.
          D: envía el fichero al navegador y fuerza la descarga del fichero con el nombre especificado por nombre.
          F: guarda el fichero en un fichero local de nombre nombre.
          S: devuelve el documento como una cadena. nombre se ignora.
         */


        $pdf->Receipe($receipe, $image);
        $pdf->Close();
        return $pdf->Output("receipe.pdf", "D");


    }
}

?>
