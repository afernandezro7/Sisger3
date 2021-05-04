<?php
/**
 * HTML2PDF Library - example
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 *
 * isset($_GET['vuehtml']) is not mandatory
 * it allow to display the result in the HTML format
 */

ob_start();
?>
<style type="text/css">
<!--
    table.page_header {width: 100%; padding-top: 20px; padding-left: 30px; padding-right: 20px }
    table.page_footer {width: 100%; border: none; padding: 5mm; padding-bottom 50px}
		.mytable {
      border-collapse: collapse;
      width: 100%;
      background-color: white;
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
<page backtop="14mm" backbottom="14mm" backleft="10mm" backright="10mm" pagegroup="new">
    
    <page_footer>
        <table class="page_footer">
            <tr>
                <td style="width: 100%; text-align: center">
                    <p style="font-weight: bold;  font-size: 8pt;">ESTE DOCUMENTO NO ES VÁLIDO SIN FIRMA Y CUÑOS AUTORIZADOS</p>
                </td>
            </tr>
        </table>
    </page_footer>
	
	  <table  style="padding-top: 100px; width: 100%; text-align: center">
            <tr>
                <td style="width: 100%; text-align: center">
                    <p style="font-weight: bold;  font-size: 8pt;">VOUCHER DE SERVICIOS</p>
                </td>
            </tr>
        </table>
	
 <table class="mytable mytable-body" style="padding-top: 30px;">
  <col style="width: 30%">
  <col style="width: 35%">
  <col style="width: 35%">
    <tr>
      <td style=" border-top: 1px solid black;">
		<table class="mytable mytable-inside">
			<tr>
					<td style="font-weight: bold; font-size: 10pt;">REF. VOUCHER:</td>      
			</tr>
			<tr>
					<td style=" font-size: 9pt;">::REFVOUCHER::</td>      
			</tr>
		</table>
	  </td>
      <td style=" border-top: 1px solid black;"> 
		<table class="mytable mytable-inside">
			<tr>
					<td style="font-weight: bold; font-size: 10pt;">REF. EXPEDIENTE:</td>      
			</tr>
			<tr>
					<td style=" font-size: 9pt;">::REFEXPEDIENTE::</td>      
			</tr>
		</table>
	  </td>
      <td style=" border-top: 1px solid black;">
		<table class="mytable mytable-inside">
			<tr>
					<td style="font-weight: bold; font-size: 10pt;">REF. PROVEEDORES:</td>      
			</tr>
			<tr>
					<td style=" font-size: 9pt;">::REFPROVEEDORES::</td>      
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
					<td style="font-weight: bold; font-size: 10pt;">PROVEEDOR:</td>      
			</tr>
			<tr>
					<td style=" font-size: 9pt;">::PROVEEDOR::</td>      
			</tr>
		</table>
	  </td>
      <td>
		<table class="mytable mytable-inside">
			<tr>
					<td style="font-weight: bold; font-size: 10pt;">DIA</td>      
			</tr>
			<tr>
					<td style=" font-size: 9pt;">::DIA::</td>      
			</tr>
		</table>
	  </td>
      <td>
		<table class="mytable mytable-inside">
			<tr>
					<td style="font-weight: bold; font-size: 10pt;">MES:</td>      
			</tr>
			<tr>
					<td style=" font-size: 9pt;">::MES::</td>      
			</tr>
		</table>
	  </td>
	  <td>
		<table class="mytable mytable-inside">
			<tr>
					<td style="font-weight: bold; font-size: 10pt;">ANNO:</td>      
			</tr>
			<tr>
					<td style=" font-size: 9pt;">::ANNO::</td>      
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
					<td style="font-weight: bold; font-size: 10pt;">CLIENTE:</td>      
			</tr>
			<tr>
					<td style=" font-size: 9pt;">::CLIENTE::</td>      
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
					<td style="font-weight: bold; font-size: 10pt;">CANTIDAD DE PERSONAS:</td>      
			</tr>
			<tr>
					<td style=" font-size: 9pt;">::CANTIDADPERSONAS::</td>      
			</tr>
		</table>
	  </td>
      <td>
		<table class="mytable mytable-inside">
			<tr>
					<td style="font-weight: bold; font-size: 10pt;">ENTRADA:</td>      
			</tr>
			<tr>
					<td style=" font-size: 9pt;">::ENTRADA::</td>      
			</tr>
		</table>
	  </td>
      <td>
		<table class="mytable mytable-inside">
			<tr>
					<td style="font-weight: bold; font-size: 10pt;">SALIDA:</td>      
			</tr>
			<tr>
					<td style=" font-size: 9pt;">::SALIDA::</td>      
			</tr>
		</table>
	  </td>
    </tr>
  </table>
   <table class="mytable mytable-footer">
   <col style="width: 100%">
    <tr>
      <td>
		<table class="mytable mytable-inside">
			<tr>
					<td style="font-weight: bold; font-size: 10pt;">OPERACIONES:</td>      
			</tr>
			<tr>
					<td style=" font-size: 9pt;">::OPERACIONES::</td>      
			</tr>
		</table>
	  </td>      
    </tr>
  </table>	
		<table class="mytable mytable-inside" style="padding-top:120px">		
			<col style="width: 70%">
			<col style="width: 30%">
			<tr>
				<td></td>
				<td style="font-weight: bold; font-size: 10pt;border-bottom: 1px solid"></td>      
			</tr>
			<tr>
				<td></td>
				<td style=" font-weight: bold; font-size: 10pt; text-align: center;">FIRMA Y CUÑO</td>      
			</tr>
		</table>	
  <page_header>
        <table class="page_header" style="">
            <col style="width: 20%">
			<col style="width: 45%">
			<col style="width: 25%">
			<tr>
                <td style="text-align: center">
                    <img src="../img/belraysa.jpg"  WIDTH=170 HEIGHT=55><br><br><span style="font-weight: bold; font-size: 7pt;">RUC 1866733-1-716356 DV 40</span>
                </td>
				<td></td>
				<td style="text-align: right">
					<p style="font-weight: bold; margin-top: 0px; font-size: 8pt; float: right;">Calle Eric del Valle, Edificio Viky 2 e/ Via Argentina y Calle Alberto Navarro El Cangrejo, Bella Vista, Ciudad Panamá Tel. 214 3509 /  info@belraysatours.com</p>
				</td>
            </tr>
        </table> 
    </page_header>
</page>

<?php


$content = ob_get_clean();

require_once(dirname(__FILE__).'/../vendor/autoload.php');
try
{
    $html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', 0);
    $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
    $html2pdf->Output('groups.pdf');
}
catch(HTML2PDF_exception $e) {
    echo $e;
    exit;
}
