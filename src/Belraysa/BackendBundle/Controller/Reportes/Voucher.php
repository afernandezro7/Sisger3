<?php

function smart_wordwrap($string, $width, $break = "<br>") {
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


$refVoucher = $voucher->getSisgerCode();
$refExpediente = $voucher->getReply()->getId();

if ($voucher->getRefProviders() == "") {
    $refProviders = '-';
} else {
    $refProviders = $voucher->getRefProviders();
}

$provider = $voucher->getProvider();

$now = $voucher->getCreationDate();
$now = date_format($now, 'd/m/Y');
$day = substr($now, 0, 2);
$month = substr($now, 3, 2);
$year = substr($now, 6, 4);

$client = $voucher->getClient()->getFirstName() . ' ' . $voucher->getClient()->getLastName();

$pax = $voucher->getPax();

$dateIn = date_format($voucher->getBeginDate(), 'd-m-Y');
$dateOut = date_format($voucher->getFinishDate(), 'd-m-Y');

$operations = $voucher->getServices();
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
        background-color: white;
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

    ol, ul {
        margin-top: 0;
        margin-bottom: 10px;
        margin-left:-20px;
    }


    -->
</style>
<page backtop="14mm" backbottom="14mm" backleft="10mm" backright="10mm" pagegroup="new">
    <page_header>
        <table class="page_header">
            <col style="width: 20%">
            <col style="width: 45%">
            <col style="width: 25%">
            <tr>
                <td style="text-align: center">
                    <img src="<?php if ($workspace->getLogo() != null) {
                        echo $this->container->getParameter('belraysa.route.logos') . $workspace->getLogo();
                    } else {
                        echo $this->container->getParameter('belraysa.route.logos') . 'belraysa.png';
                    }
                    ?>" WIDTH=170 HEIGHT=55><br><br><span
                        style="font-weight: bold; font-size: 7pt;">RUC 1866733-1-716356 DV 40</span>
                </td>
                <td></td>
                <td style="text-align: right">
                    <p style="font-weight: bold; margin-top: 0px; font-size: 8pt; float: right;">Calle Eric del Valle,
                        Edificio Viky 2 e/ Via Argentina y Calle Alberto Navarro El Cangrejo, Bella Vista, Ciudad Panam??
                        Tel. 214 3509 / info@belraysatours.com</p>
                </td>
            </tr>
        </table>
    </page_header>
    <page_footer>
        <table class="page_footer">
            <tr>
                <td style="width: 100%; text-align: center">
                    <p style="font-weight: bold;  font-size: 8pt; ">ESTE DOCUMENTO NO ES V??LIDO SIN FIRMA Y SELLOS
                        AUTORIZADOS</p>
                </td>
            </tr>
        </table>
    </page_footer>

    <table style="padding-top: 100px; width: 100%; text-align: center">
        <tr>
            <td style="width: 100%; text-align: center">
                <p style="font-weight: bold;  font-size: 10pt;">VOUCHER DE SERVICIOS</p>
            </td>
        </tr>
    </table>

    <table class="mytable mytable-body" style="padding-top: 30px;">
        <col style="width: 40%">
        <col style="width: 60%">
        <tr>
            <td style=" border-top: 1px solid black;">
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-size: 10pt;"><b>VOUCHER:</b> <?php echo $refVoucher ?></td>
                    </tr>
                </table>
            </td>
            <td style=" border-top: 1px solid black;">
                <table class="mytable mytable-inside">
                    <tr>
                        <td style=" font-size: 10pt;"><b>REF.
                                PROVEEDORES: </b><?php echo smart_wordwrap($refProviders, $width = 20, $break = "<br>") ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table class="mytable mytable-body">
        <col style="width: 65%">
        <col style="width: 12%">
        <col style="width: 12%">
        <col style="width: 11%">
        <tr>
            <td>
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-size: 10pt;"><b>PROVEEDOR:</b> <?php echo $provider ?></td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="mytable mytable-inside">
                    <tr>
                        <td style=" font-size: 10pt;"><b>DIA</b></td>
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
                        <td style="font-size: 10pt;"><b>A??O:</b></td>
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
                        <td style="font-size: 10pt;"><b>CLIENTE:</b> <?php echo $client ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table class="mytable mytable-body">
        <col style="width: 30%">
        <col style="width: 35%">
        <col style="width: 35%">
        <tr>
            <td>
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-size: 10pt;"><b>CANT. PERSONAS:</b> <?php echo $pax ?></td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-size: 10pt;"><b>ENTRADA:</b> <?php echo $dateIn ?></td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-size: 10pt;"><b>SALIDA:</b> <?php echo $dateOut ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table class="mytable mytable-footer">
        <col style="width: 100%">
        <col style="width: 100%">
        <tr>
            <td>
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-size: 10pt;"><b>SERVICIOS:</b>
                            <br>
                            <?php echo smart_wordwrap($operations, $width = 95, $break = "<br>") ?></td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>
    <table class="mytable mytable-inside" style="padding-top:120px">
        <col style="width: 70%">
        <col style="width: 30%">
       
       
       <?php if($voucher->isFirmado()){ ?>
       <tr>
            <td></td>
            <td style="font-weight: bold; font-size: 10pt;"></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-weight: bold; font-size: 10pt;border-bottom: 1px solid">
                <img src="<?php echo $firma ?>">
               
            </td>
        </tr>
        <?php }else{ ?>
        <tr>
            <td></td>
            <td style="font-weight: bold; font-size: 10pt;border-bottom: 1px solid"></td>
        </tr>
         <?php } ?>
        <tr>
            <td></td>
            <td style=" font-weight: bold; font-size: 10pt; text-align: center;">
                FIRMA Y SELLO
            </td>
        </tr>
    </table>


</page>


