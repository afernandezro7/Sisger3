<?php

$refRecibo = $recibo->getSisgerCode();
$refExpediente = $recibo->getRefExpediente();

$importe = $recibo->getImporte();

$tipo = $recibo->getTipo();

$now = $recibo->getFecha();
$now = date_format($now, 'd/m/Y');
$day = substr($now, 0, 2);
$month = substr($now, 3, 2);
$year = substr($now, 6, 4);
if ($tipo == 'Ingreso') {
    $recibiDe = $recibo->getRecibiDe();
} else {
    $recibiDe = $recibo->getPagueA();
}
$suma = $recibo->getSuma();

if ($tipo == 'Ingreso') {
    $servicios = $recibo->getServicios();
} elseif ($tipo == 'Gasto') {
    $servicios = $recibo->getConceptosGasto();
} elseif ($tipo == 'Costo') {
    $servicios = $recibo->getConceptosCosto();
} else {
    $servicios = $recibo->getInventarios();
}

$concepto = $recibo->getDetalles();

$method = $recibo->getMetodoPago();

if ($tipo == 'Ingreso') {
    $saldoAnterior = number_format($recibo->getSaldoAnterior(), 2);
    $abono = number_format($recibo->getAbono(), 2);
    $saldoPendiente = number_format($recibo->getSaldoPendiente(), 2);
}


function smart_wordwrap($string, $width, $break = "<br>")
{
// split on problem words over the line length
    $pattern = sprintf('/([^ ]{%d,})/', $width);
    $output = '';
    $words = preg_split($pattern, $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

    foreach ($words as $word) {
        // normal behaviour, rebuild the string
        if (false !== strpos($word, ' ')) {
            $output .= $word;
        } else {
            // work out how many characters would be on the current line
            $wrapped = explode($break, wordwrap($output, $width, $break));
            $count = $width - (strlen(end($wrapped)) % $width);

            // fill the current line and add a break
            $output .= substr($word, 0, $count) . $break;

            // wrap any remaining characters from the problem word
            $output .= wordwrap(substr($word, $count), $width, $break, true);
        }
    }

    // wrap the final output
    return wordwrap($output, $width, $break);
}

function array_chunk_fixed($input, $num, $preserve_keys = FALSE)
{
    $count = count($input);
    if ($count)
        $input = array_chunk($input, ceil($count / $num), $preserve_keys);
    $input = array_pad($input, $num, array());
    return $input;
}

?>

<style type="text/css">
    <!--
    table.page_header {
        width: 100%;
        padding-top: 20px;
        padding-left: 30px;
        padding-right: 20px
    }

    table.page_footer {
        width: 100%;
        border: none;
        padding: 5mm;
        padding-bottom 50px;

    }

    .mytable {
        border-collapse: collapse;
        width: 100%;
        background-color: transparent;
        padding: 5px;
    }

    .mytable-head {
        border: 0px solid black;
        border-bottom: 1px solid black;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .mytable-head td {
        border: 1px solid black;
    }

    .mytable-body {
        border: 1px solid black;
        border-top: 0;
        margin-top: 0;
        padding-top: 0;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .mytable-body td {
        border: 1px solid black;
        border-top: 0;
    }

    .mytable-footer {
        border: 1px solid black;
        border-top: 0;
        margin-top: 0;
        padding-top: 0;
    }

    .mytable-footer td {
        border: 1px solid black;
        border-top: 0;
    }

    .mytable-inside td {
        border: 0px solid black;
        border-top: 0;
    }

    table.morpion {
        border: 0;
    }

    table.morpion td.j1 {
        font-size: 8pt;
        font-weight: bold;
        border: 1px solid black;
        border-top: 1px solid black;
        padding: 1px;
        text-align: center;
        width: 10px;
    }

    -->
</style>
<page backtop="14mm" backbottom="14mm" backleft="10mm" backright="10mm" pagegroup="new"
      backimg="<?php if ($recibo->isActivo() == false) {
          echo dirname(__FILE__) . '/watermark_cancelado1.png';
      }
      else if ($recibo->getTipo() == 'Ingreso' && $recibo->getCuentaXCobrar() == 1){
          echo dirname(__FILE__) . '/watermark_CxC.png';
      }
      ?>" backimgx="center" backimgy="top">

    <page_footer>
        <table class="page_footer">
            <tr>
                <td style="width: 100%; text-align: center">
                    <p style="font-weight: bold;  font-size: 8pt; ">ESTE DOCUMENTO NO ES VÁLIDO SIN FIRMA Y SELLOS
                        AUTORIZADOS</p>
                </td>
            </tr>
        </table>
    </page_footer>

    <table style="padding-top: 100px; width: 100%; text-align: center">
        <tr>
            <td style="width: 100%; text-align: center">
                <p style="font-weight: bold;  font-size: 10pt;">RECIBO DE <?php if ($tipo == 'Ingreso') {
                        echo 'INGRESO';
                    } elseif ($tipo == 'Gasto') {
                        echo 'GASTO';
                    } elseif ($tipo == 'Costo recurrente') {
                        echo 'COSTO RECURRENTE';
                    } else {
                        echo 'COSTO';
                    } ?></p>
            </td>
        </tr>
    </table>

    <table class="mytable mytable-body" style="padding-top: 30px;">
        <col style="width: 32%">
        <col style="width: 32%">
        <col style="width: 36%">
        <tr>
            <td style=" border-top: 1px solid black;">
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-size: 10pt;"><b>RECIBO: </b><?php echo $refRecibo ?></td>
                    </tr>
                </table>
            </td>
            <td style=" border-top: 1px solid black;">
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-size: 10pt;"><b>USUARIO:</b> <?php echo $recibo->getUsuario()->getLetra() ?>
                        </td>
                    </tr>
                </table>
            </td>
            <td style=" border-top: 1px solid black;">
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-size: 10pt;"><b>REF:</b> <?php echo $refExpediente ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table class="mytable mytable-body">
        <col style="width: 64%">
        <col style="width: 12%">
        <col style="width: 12%">
        <col style="width: 12%">
        <tr>
            <td>
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-size: 10pt;"><b>IMPORTE:</b> <?php echo $importe ?> USD</td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-size: 10pt;"><b>DIA</b></td>
                    </tr>
                    <tr>
                        <td style="font-size: 9pt; text-align: center;"><?php echo $day ?>
                        </td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-size: 10pt;"><b>MES:</b></td>
                    </tr>
                    <tr>
                        <td style="font-size: 9pt; text-align: center;"><?php echo $month ?></td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-size: 10pt;"><b>AÑO:</b></td>
                    </tr>
                    <tr>
                        <td style="font-size: 9pt; text-align: center;"><?php echo $year ?></td>
                    </tr>
                </table>
            </td>

        </tr>
    </table>
    <table class="mytable mytable-body">
        <col style="width: 100%">
        <tr>
            <td>
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-size: 10pt;"><b><?php if ($tipo == 'Ingreso') {
                                    echo 'RECIBÍ DE:';
                                } else {
                                    echo 'PAGUÉ A:';
                                } ?> </b> <?php echo $recibiDe ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table class="mytable mytable-body">
        <col style="width: 100%">
        <tr>
            <td>
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-size: 10pt;"><b>LA SUMA DE:</b> <?php echo $suma ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table class="mytable mytable-body">
        <col style="width: 100%">
        <tr>
            <td>
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-weight: bold; font-size: 10pt; text-align: ">EN CONCEPTO DE: &nbsp; &nbsp;
                            &nbsp;
                            &nbsp;
                        </td>

                    </tr>
                    <tr>
                        <td style=" font-size: 9pt;">
                            <table class="morpion" cellspacing="5px">

                                <?php

                                for ($i = 0; $i < sizeof($servicios); $i++) {
                                    if($i % 3 == 0)
                                        echo '<tr>';
                                    $servicio = $servicios[$i];
                                    if ($tipo == 'Ingreso') {
                                        $nombreServicio = $servicio->getName();
                                    } else {
                                        $nombreServicio = $servicio->getNombre();
                                    }
                                    echo '<td class="j1">X</td><td style=" font-size: 9pt; padding-right: 20px;">' . $nombreServicio . '</td>';
                                    if($i % 3 == 2 or $i == (sizeof($servicios) - 1))
                                        echo '</tr>';
                                }


//                                if (sizeof($servicios) == 1) {
//                                    if ($tipo == 'Ingreso') {
//                                        $nombreServicio = $servicios[0]->getName();
//                                    } else {
//                                        $nombreServicio = $servicios[0]->getNombre();
//                                    }
//                                    echo '<tr><td class="j1">X</td><td style=" font-size: 9pt; padding-right: 20px;">' . $nombreServicio . '</td></tr>';
//                                } else {
//                                    for ($i = 0; $i < sizeof($servicios); $i++) {
//                                        $servicio = $servicios[$i];
//                                        if ($tipo == 'Ingreso') {
//                                            $nombreServicio = $servicio->getName();
//                                        } else {
//                                            $nombreServicio = $servicio->getNombre();
//                                        }
//                                        if ($i == 0 or ($i % 4 == 0)) {
//                                            echo '<tr><td class="j1">X</td><td style=" font-size: 9pt; padding-right: 20px;">' . $nombreServicio . '</td>';
//                                        } elseif ($i == 3 or $i == (sizeof($servicios) - 1)) {
//                                            echo '<td class="j1">X</td><td style=" font-size: 9pt; padding-right: 20px;">' . $nombreServicio . '</td></tr>';
//                                        } else {
//                                            echo '<td class="j1">X</td><td style=" font-size: 9pt; padding-right: 20px;">' . $nombreServicio . '</td>';
//                                        }
//                                    }
//                                }
                                ?>

                            </table>
                        </td>
                    </tr>
                </table>
                <table class="mytable mytable-inside">
                    <tr>
                        <td>
                            <?php echo smart_wordwrap($concepto, $width = 80, $break = "<br>") ?>
                        </td>

                    </tr>

                </table>
            </td>
        </tr>
    </table>

    <table
        <?php if ($tipo == 'Ingreso') {
            echo 'class="mytable mytable-body"';
        } else {
            echo 'class="mytable mytable-footer"';
        } ?>>
        <col style="width: 100%">
        <tr>
            <td>
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-weight: bold; font-size: 10pt; text-align: ">FORMA DE PAGO: &nbsp; &nbsp; &nbsp;
                            &nbsp;
                        </td>

                    </tr>
                    <tr>
                        <td style=" font-size: 9pt;">
                            <table class="morpion" cellspacing="5px">

                                <tr>
                                    <?php
                                    foreach ($paymentMethods as $paymentMethod) {

                                        if ($method) {
                                            if ($method->getId() == $paymentMethod->getId()) {
                                                echo '<td class="j1">X</td><td style=" font-size: 9pt; padding-right: 20px;">' . $paymentMethod->getName() . '</td>';

                                            } else {
                                                echo ' <td class="j1"></td><td style=" font-size: 9pt; padding-right: 20px;">' . $paymentMethod->getName() . '</td>';
                                            }


                                        } else {
                                            echo ' <td class="j1"></td><td style=" font-size: 9pt; padding-right: 20px;">' . $paymentMethod->getName() . '</td>';
                                        }


                                    }


                                    ?>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <?php if ($tipo == 'Ingreso') {
        echo '
    <table class="mytable mytable-body">
        <col style="width: 30%">
        <col style="width: 35%">
        <col style="width: 35%">
        <tr>
            <td>
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-weight: bold; font-size: 10pt; text-align: center">SALDO ANTERIOR:</td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-weight: bold; font-size: 10pt;">ABONO:</td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-weight: bold; font-size: 10pt;">SALDO PENDIENTE:</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table class="mytable mytable-footer">
        <col style="width: 30%">
        <col style="width: 35%">
        <col style="width: 35%">
        <tr>
            <td>
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-size: 10pt; text-align: center"> &nbsp;' .
            $saldoAnterior . ' USD
                        </td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-size: 10pt;"> &nbsp;
                           ' . $abono .
            ' USD
                        </td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-size: 10pt;">' .
            $saldoPendiente .
            ' USD
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>';
    } ?>
    <table class="mytable mytable-inside" style="padding-top:120px">
        <col style="width: 70%">
        <col style="width: 30%">


        <?php if ($recibo->isFirmado()) { ?>

            <tr>
                <td></td>
                <td style="font-weight: bold; font-size: 10pt;"><img src="<?php echo $sello ?>"></td>
            </tr>
            <tr>
                <td></td>
                <td style="font-weight: bold; font-size: 10pt;border-bottom: 1px solid">

                    <img height="42px" src="<?php echo $firma ?>">

                </td>
            </tr>
        <?php } else { ?>
            <tr>
                <td></td>
                <td style="font-weight: bold; font-size: 10pt;border-bottom: 1px solid"></td>
            </tr>
        <?php } ?>
        <tr>
            <td></td>
            <td style=" font-weight: bold; font-size: 10pt; text-align: center;"> FIRMA Y SELLO</td>
        </tr>
    </table>
    <page_header>
        <table class="page_header" style="">
            <col style="width: 20%">
            <col style="width: 45%">
            <col style="width: 25%">
            <tr>
                <td style="text-align: center">
                    <img src="<?php echo dirname(__FILE__) . '/belraysa.jpg' ?>" WIDTH=170 HEIGHT=55><br><br><span
                            style="font-weight: bold; font-size: 7pt;"> RUC 1866733 - 1 - 716356 DV 40 </span>
                </td>
                <td></td>
                <td style="text-align: right">
                    <p style="font-weight: bold; margin-top: 0px; font-size: 8pt; float: right;"> Calle Eric del Valle,
                        Edificio Viky 2 e / Via Argentina y Calle Alberto Navarro El Cangrejo, Bella Vista, Ciudad
                        Panamá
                        Tel . 214 3509 / info@belraysatours . com </p>
                </td>
            </tr>
        </table>
    </page_header>
</page>


