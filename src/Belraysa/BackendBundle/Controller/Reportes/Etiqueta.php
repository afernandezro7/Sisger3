<style type="text/css">

    .mytable {
        border-collapse: collapse;
        width: 100%;
        background-color: transparent;
        padding: 5px;
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

    .mytable-footer td {
        border: 1px solid black;
        border-top: 0;
    }

    .mytable-inside td {
        border: 0px solid black;
        border-top: 0;
    }

</style>
<page backtop="14mm" backbottom="14mm" backleft="10mm" backright="10mm" pagegroup="new">

    <table class="mytable mytable-body" style="padding-top: 2px;">
        <col style="width: 80%">
        <col style="width: 20%">
        <tr>
            <td style=" border-top: 1px solid black;">
                <table class="mytable mytable-inside">
                    <tr>
                        <td style="text-align: center">
                            <img src="<?php echo dirname(__FILE__) . '/belraysa.jpg' ?>" WIDTH=190 HEIGHT=55>
                        </td>
                    </tr>
                </table>
            </td>
            <td style=" border-top: 1px solid black; text-align: center">
                <label style="font-size: 8pt; font-style: italic">BULTOS</label>
                <br>
                <label style="font-size: 18pt"> <b><?php echo $indice . '/' . $cantidad ?></b></label>
            </td>
        </tr>
    </table>
    <table class="mytable mytable-body">
        <col style="width: 20%">
        <col style="width: 80%">
        <tr>
            <td style="padding-top:3px;padding-bottom:3px;font-size: 5pt; border-right: none">
                Powered by:
            </td>
            <td style="padding-top:3px;padding-bottom:3px;font-size: 7pt; font-style: italic; border-left: none; text-align: center">
                BELRAYSA TOURS & TRAVEL GROUP SA
            </td>
        </tr>
        <tr>
            <td style="padding-top:3px;padding-bottom:3px;text-align: center">
                <label style="font-size: 8pt"><b><?php echo $concepto ?></b></label>
            </td>
            <td style="padding-top:3px;padding-bottom:3px;font-size: 6pt; text-align: center">
                AGENCIA ADUANAL Y TRANSITARIA PALCO
            </td>
        </tr>
    </table>
    <table class="mytable mytable-body">
        <col style="width: 100%">
        <tr>
            <td style="font-size: 8pt; border-bottom: none; text-align: center">
                <b>RECOGIDA EN ALMACEN</b>
            </td>
        </tr>
    </table>
    <table class="mytable mytable-body">
        <col style="width: 20%">
        <col style="width: 20%">
        <col style="width: 60%">
        <tr>
            <td style="padding-top:7px; font-size: 7pt; border-bottom: none; border-top: none; border-right: none; text-align: center">
                <b>PESO:</b>
            </td>
            <td style="padding-top:7px;font-size: 7pt; border-bottom: none; border-top: none; border-right: none; text-align: center">
                <?php echo $peso ?> Kgs
            </td>
            <td style="padding-top:7px;font-size: 7pt; border-bottom: none; border-top: none;  text-align: center">
                ENVIO: PALCO MARITIMO
            </td>
        </tr>
        <tr>
            <td style="padding-top:7px; padding-bottom:5px; font-size: 7pt; border-bottom: none; border-top: none; border-right: none; text-align: center">
                <b>VOL:</b>
            </td>
            <td style="padding-top:7px;padding-bottom:5px;font-size: 7pt; border-bottom: none; border-top: none; border-right: none; text-align: center">
                <?php echo $volumen ?> M3
            </td>
            <td style="padding-top:7px;padding-bottom:5px;font-size: 7pt; border-bottom: none; border-top: none;  text-align: center">
                DESTINO: C. HABANA
            </td>
        </tr>
    </table>
    <table class="mytable mytable-body">
        <col style="width: 20%">
        <col style="width: 80%">
        <tr>
            <td style="text-align: center; border-top: 1px solid black; padding: 15px">
                <label style="font-size: 7pt"><b>ENVIA:</b></label>
            </td>
            <td style="font-size: 8pt; text-align: center; border-top: 1px solid black;">
                <?php echo $envia ?>
            </td>
        </tr>
        <tr>
            <td style="text-align: center; border-top: 1px solid black; padding: 15px">
                <label style="font-size: 8pt"><b>RECIBE:</b></label>
            </td>
            <td style="font-size: 12pt; text-align: center; border-top: 1px solid black;">
                <b><?php echo $recibe ?></b>
            </td>
        </tr>
        <tr>
            <td style="text-align: center; border-top: 1px solid black; padding-top: 15px; padding-bottom: 15px; font-size: 7pt">
                <b>DIRECCION:</b>
            </td>
            <td style="font-size: 7pt; text-align: center; border-top: 1px solid black;">
                <?php echo $direccion ?>
            </td>
        </tr>
        <tr>
            <td style="text-align: center; border-top: 1px solid black; padding: 2px">
                <label style="font-size: 7pt"><b>TELF DE CONTACTO:</b></label>
            </td>
            <td style="font-size: 7pt; text-align: center; border-top: 1px solid black;">
                <?php echo $telefono ?>
            </td>
        </tr>
    </table>
    <table class="mytable mytable-body">
        <col style="width: 5.95%">
        <col style="width: 94%">
        <tr>
            <td style="text-align: center; font-size: 8pt; padding: 0px !important;">
                <table class="mytable mytable-inside" style="padding: 0px !important;">
                    <col style="width: 100%">
                    <tr>
                        <td>
                            <b>L</b>
                            <br>
                            <b>E</b>
                            <br>
                            <b>Y</b>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="text-align: center; font-size: 7pt;" style="padding: 0px !important;">
                <table class="mytable mytable-inside" style="padding: 0px !important;">
                    <col style="width: 15%">
                    <col style="width: 65%">
                    <col style="width: 20%">
                    <tr>
                        <td style="text-align: center; font-size: 7pt; border-bottom: 1px solid black !important; border-right: 1px solid black !important;">
                            Cap&iacute;tulo:
                        </td>
                        <td style="text-align: center; font-size: 7pt; border-bottom: 1px solid black !important; border-right: 1px solid black !important;">
                            Art&iacute;culo:
                        </td>
                        <td style="text-align: center; font-size: 7pt; border-bottom: 1px solid black !important;">
                            Arancel:
                        </td>
                    </tr>
                    <tr>
                        <td style=" padding-top: 5px;padding-bottom: 5px; text-align: center; font-size: 7pt; border-right: 1px solid black !important;">
                            10
                        </td>
                        <td style="padding-top: 5px;padding-bottom: 5px;text-align: center; font-size: 7pt; border-right: 1px solid black !important;">
                            Preparaciones capitales para maquillajes y cuidados de la piel lociones, cremas,
                            delineadores, brillo y creyones labiales y similares.
                        </td>
                        <td style="padding-top: 5px;padding-bottom: 5px;text-align: center; font-size: 7pt;">
                            4.00
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table class="mytable mytable-body">
        <col style="width: 100%">
        <tr>
            <td style="text-align: center; font-size: 7pt; border-bottom: 1px solid black !important; ">
                <b>Descripci&oacute;n de la Mercanc&iacute;a:</b>
            </td>
        </tr>
        <tr>
            <td style="height: 70px;text-align: center; font-size: 7pt; ">
                <label> <?php echo $descripcion ?></label>
            </td>
        </tr>
        <tr>
            <td style="text-align: center; font-size: 18pt; ">
                <b> *<?php echo $codigo ?>*</b>
            </td>
        </tr>
        <tr>
            <td style="height:70px; text-align: center; font-size: 9pt;">
            <?php $generator = new \Belraysa\BackendBundle\Controller\Barcode\src\BarcodeGeneratorPNG();
            echo '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode('*'.$codigo.'*', $generator::TYPE_CODE_39)) . '">';
            ?><br>*<?php echo $codigo ?>*
            </td>
        </tr>

    </table>
</page>


