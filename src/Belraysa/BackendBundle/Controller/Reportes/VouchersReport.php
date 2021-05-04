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

    -->
</style>
<page backtop="30mm" backbottom="14mm" backleft="10mm" backright="10mm">
    <page_header >
        <table class="page_header" >
            <col style="width: 20%">
            <col style="width: 45%">
            <col style="width: 25%">
            <tr>
                <td style="text-align: center">
                    <img src="<?php echo dirname(__FILE__) . '/belraysa.jpg' ?>" WIDTH=170 HEIGHT=55><br><br><span
                        style="font-weight: bold; font-size: 7pt;">RUC 1866733-1-716356 DV 40</span>
                </td>
                <td></td>
                <td style="text-align: right">
                    <p style="font-weight: bold; margin-top: 0px; font-size: 8pt; float: right;">Calle Eric del Valle,
                        Edificio Viky 2 e/ Via Argentina y Calle Alberto Navarro El Cangrejo, Bella Vista, Ciudad Panamá
                        Tel. 214 3509 / info@belraysatours.com</p>
                </td>
            </tr>
        </table>
    </page_header>
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

    <table style="padding-top: 65px; width: 100%; text-align: center">
        <tr>
            <td style="width: 100%; text-align: center">
                <p style="font-weight: bold;  font-size: 10pt;">REPORTE DE VOUCHERS <?php echo $fromString ?>
                    - <?php echo $toString ?></p>
            </td>
        </tr>
    </table>

    <table class="mytable mytable-body" style="padding-top: 30px">
        <col style="width: 5%">
        <col style="width: 30%">
        <col style="width: 10%">
        <col style="width: 25%">
        <col style="width: 10%">
        <col style="width: 10%">
        <col style="width: 10%">

        <tr>
            <td style=" border-top: 1px solid black;">
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-size: 10pt;"><b>No</b></td>
                    </tr>
                </table>
            </td>
            <td style=" border-top: 1px solid black;">
                <table class="mytable mytable-inside">
                    <tr>
                        <td style=" font-size: 10pt;"><b>No. Voucher</b></td>
                    </tr>
                </table>
            </td>
            <td style=" border-top: 1px solid black;">
                <table class="mytable mytable-inside">
                    <tr>
                        <td style=" font-size: 10pt;"><b>Fecha</b></td>
                    </tr>
                </table>
            </td>
            <td style=" border-top: 1px solid black;">
                <table class="mytable mytable-inside">
                    <tr>
                        <td style=" font-size: 10pt;"><b>Cliente</b></td>
                    </tr>
                </table>
            </td>
            <td style=" border-top: 1px solid black;">
                <table class="mytable mytable-inside">
                    <tr>
                        <td style=" font-size: 10pt;"><b>Cant. Pax</b></td>
                    </tr>
                </table>
            </td>
            <td style=" border-top: 1px solid black;">
                <table class="mytable mytable-inside">
                    <tr>
                        <td style=" font-size: 10pt;"><b>Entrada </b></td>
                    </tr>
                </table>
            </td>
            <td style=" border-top: 1px solid black;">
                <table class="mytable mytable-inside">
                    <tr>
                        <td style=" font-size: 10pt;"><b>Salida</b></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <?php for ($i = 1; $i <= sizeof($vouchersReport); $i++) {
        $createdAtString = date_format($vouchersReport[$i-1]->getCreationDate(), 'd/m/Y');
        $beginAtString = date_format($vouchersReport[$i-1]->getBeginDate(), 'd/m/Y');
        $finishAtString = date_format($vouchersReport[$i-1]->getFinishDate(), 'd/m/Y');
        echo ' <table class="mytable mytable-body">
        <col style="width: 5%">
        <col style="width: 30%">
        <col style="width: 10%">
        <col style="width: 25%">
        <col style="width: 10%">
        <col style="width: 10%">
        <col style="width: 10%">
          '; if($vouchersReport[$i-1]->isActivo()){ echo '<tr>';} else { echo '<tr style="  color:#a61717; text-decoration: line-through" {% endif %}>' ;} ; echo
            '<td >
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-size: 10pt;"><b>'.$i.'</b></td>
                    </tr>
                </table>
            </td>
            <td >
                <table class="mytable mytable-inside">
                    <tr>
                        <td style=" font-size: 10pt;">'.$vouchersReport[$i-1]->getSisgerCode().'</td>
                    </tr>
                </table>
            </td>
            <td >
                <table class="mytable mytable-inside">
                    <tr>
                        <td style=" font-size: 10pt;">'.$createdAtString.'</td>
                    </tr>
                </table>
            </td>
            <td >
                <table class="mytable mytable-inside">
                    <tr>
                        <td style=" font-size: 10pt;">'.smart_wordwrap($vouchersReport[$i-1]->getClient()->getFirstName().' '.$vouchersReport[$i-1]->getClient()->getLastName(), $width = 28, $break = "<br>").'</td>
                    </tr>
                </table>
            </td>
            <td >
                <table class="mytable mytable-inside">
                    <tr>
                        <td style=" font-size: 10pt;">'.$vouchersReport[$i-1]->getPax().'</td>
                    </tr>
                </table>
            </td>
            <td >
                <table class="mytable mytable-inside">
                    <tr>
                        <td style=" font-size: 10pt;">'.$beginAtString.'</td>
                    </tr>
                </table>
            </td>
            <td >
                <table class="mytable mytable-inside">
                    <tr>
                        <td style=" font-size: 10pt;">'.$finishAtString.'</td>
                    </tr>
                </table>
            </td>
        </tr>
        </table>
';

    }
    ?>
    <table class="mytable mytable-footer">
        <col style="width: 70%">
        <col style="width: 10%">
        <col style="width: 20%">

        <tr>
            <td style=" border-top: 1px solid black;">
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="font-size: 10pt;"><b>Total</b></td>
                    </tr>
                </table>
            </td>
            <td style=" border-top: 1px solid black;">
                <table class="mytable mytable-inside">
                    <tr>
                        <td style=" font-size: 10pt;"><b><?php echo $totalPax[0][1] ?> </b></td>
                    </tr>
                </table>
            </td>
            <td style="background-color: #000000">
            </td>
        </tr>
    </table>

</page>


