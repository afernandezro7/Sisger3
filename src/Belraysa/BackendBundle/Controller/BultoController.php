<?php

namespace Belraysa\BackendBundle\Controller;

use Belraysa\BackendBundle\Entity\Mercancia;
use Belraysa\BackendBundle\Form\MercanciaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Belraysa\BackendBundle\Entity\Bulto;
use Belraysa\BackendBundle\Form\BultoType;

/**
 * Bulto controller.
 *
 */
class BultoController extends Controller
{

    /**
     * Lists all Bulto entities.
     *
     */
    public function indexAction($concepto)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:Bulto')->findByConcepto($concepto);
        $concepto = $em->getRepository('BackendBundle:Concepto')->find($concepto);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            100);
        $entity = new Mercancia();
        $form = $this->createForm(new MercanciaType(), $entity, array(
            'action' => $this->generateUrl('mercancia_create'),
            'method' => 'POST',
        ));

        if ($concepto->getTipo() == 'ENA') {
            return $this->render('BackendBundle:Bulto:indexENA.html.twig', array(
                'entities' => $pagination,
                'concepto' => $concepto,
                'form' => $form->createView(),
            ));
        } elseif ($concepto->getTipo() == 'Envio') {
            return $this->render('BackendBundle:Bulto:indexEnvio.html.twig', array(
                'entities' => $pagination,
                'concepto' => $concepto,
                'form' => $form->createView(),
            ));
        } else {
            return $this->render('BackendBundle:Bulto:indexMenaje.html.twig', array(
                'entities' => $pagination,
                'concepto' => $concepto,
                'form' => $form->createView(),
            ));
        }
    }

    /**
     * Creates a new Bulto entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if ($_GET['id']) {
            $bultoService = $this->get('bulto_service');
            $entity = $bultoService->createBultoHelper($_GET['id'], $em);

            return new JsonResponse(array('indice' => $entity->getIndice(), 'id' => $entity->getId()));
        } else {
            return new JsonResponse(-1);
        }
    }

    /**
     * Creates a form to create a Bulto entity.
     *
     * @param Bulto $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Bulto $entity)
    {
        $form = $this->createForm(new BultoType(), $entity, array(
            'action' => $this->generateUrl('bulto_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Bulto entity.
     *
     */
    public function newAction()
    {
        $entity = new Bulto();
        $form = $this->createCreateForm($entity);

        return $this->render('BackendBundle:Bulto:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Bulto entity.
     *
     */
    public function showAction()
    {
        $em = $this->getDoctrine()->getManager();
        $id = $_GET['id'];
        $entity = $em->getRepository('BackendBundle:Bulto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Bulto entity.');
        }

        return $this->render('BackendBundle:Bulto:show.html.twig', array(
            'entity' => $entity
        ));
    }

    /**
     * Displays a form to edit an existing Bulto entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Bulto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Bulto entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Bulto:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Bulto entity.
     *
     * @param Bulto $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Bulto $entity)
    {
        $form = $this->createForm(new BultoType(), $entity, array(
            'action' => $this->generateUrl('bulto_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Bulto entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Bulto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Bulto entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('bulto_edit', array('id' => $id)));
        }

        return $this->render('BackendBundle:Bulto:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Bulto entity.
     *
     */
    public function deleteAction(Request $request)
    {
        $now = new \DateTime();
        $em = $this->getDoctrine()->getManager();
        $id = $_GET['id'];
        $entity = $em->getRepository('BackendBundle:Bulto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Bulto entity.');
        }

        $concepto = $entity->getConcepto();
        $indice = $entity->getIndice();
        $bultos = $concepto->getBultos();
        for ($i = $indice; $i < sizeof($concepto->getBultos()); $i++) {
            $bultos[$i]->setIndice($bultos[$i]->getIndice() - 1);
        }
        $em->remove($entity);
        $em->flush();
        $concepto->setSisgerCode("BRS" . date_format($now, 'y'). str_pad(sizeof($concepto->getBultos()), 3, 0, STR_PAD_LEFT) . str_pad($concepto->getId(), 6, 0, STR_PAD_LEFT));
        $em->flush();
        return $this->redirect($this->generateUrl('bulto', array('concepto' => $concepto->getId())));
    }

    /**
     * Creates a form to delete a Bulto entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bulto_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    public function etiquetaAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $id = $_GET['id'];

        $bulto = $em->getRepository('BackendBundle:Bulto')->find($id);

        $indice = $bulto->getIndice();
        $cantidad = sizeof($bulto->getConcepto()->getBultos());
        $concepto = $bulto->getConcepto()->getTipo();
        $peso = $bulto->getPesoKg();
        $volumen = $bulto->getVolumenM3();
        $envia = ($concepto == 'Envio') ? $bulto->getConcepto()->getRemitenteNombre() : $bulto->getConcepto()->getRemitente()->getFirstName() . ' ' . $bulto->getConcepto()->getRemitente()->getLastName();
        $recibe = $bulto->getConcepto()->getConsignado()->getFirstName() . ' ' . $bulto->getConcepto()->getConsignado()->getLastName();
        $address = $bulto->getConcepto()->getConsignado()->getAddress();
        $municipality = $bulto->getConcepto()->getConsignado()->getMunicipality();
        $province = $bulto->getConcepto()->getConsignado()->getProvince();
        $country = $bulto->getConcepto()->getConsignado()->getCountry();
        $dirArray = array();
        if ($address) {
            $dirArray[] = $address;
        }
        if ($municipality) {
            $dirArray[] = $municipality;
        }
        if ($province) {
            $dirArray[] = $province;
        }
        if ($country) {
            $dirArray[] = $country;
        }

        $direccion = '';
        for ($j = 0; $j < sizeof($dirArray); $j++) {
            if ($j == 0) {
                $direccion = $direccion . $dirArray[$j];
            } else {
                $direccion = $direccion . ', ' . $dirArray[$j];
            }
        }

        if ($bulto->getConcepto()->getConsignado()->getCell()) {
            $telefono = $bulto->getConcepto()->getConsignado()->getCell();
        } else {
            $telefono = $bulto->getConcepto()->getConsignado()->getPhones();
        }

        $descripcion = '';
        $descripciones = array();
        foreach ($bulto->getMercancias() as $mercancia) {
            $descripciones[] = $mercancia->getDescripcion();
        }

        for ($j = 0; $j < sizeof($descripciones); $j++) {
            if ($j == 0) {
                $descripcion = $descripcion . $descripciones[$j];
            } else {
                $descripcion = $descripcion . ', ' . $descripciones[$j];
            }
        }
        $capitulo = '';
        $articulo = '';
        $arancel = '';
        if ($concepto == 'ENA' || $concepto == 'Menaje') {
            if (sizeof($bulto->getMercancias()) == 1) {
                $mercancia = $bulto->getMercancias()[0];
                $arancel = $mercancia->getArancel();
                $capitulo = $arancel->getCapitulo()->getNumero();
                $articulo = $arancel->getArticulos();

                $arancel = $arancel->getArancel();

            }
        } else {
            if (sizeof($bulto->getMercancias()) == 1) {
                $mercancia = $bulto->getMercancias()[0];
                $arancel = $mercancia->getArancel();
                $capitulo = $arancel->getCapitulo()->getNumero();
                $articulo = $arancel->getArticulos();
                $arancel = $arancel->getArancel();
            }
        }

        $codigo = $bulto->getSisgerCode();
        // get the HTML
        /*     ob_start();
             include(dirname(__FILE__) . '/Reportes/Etiqueta.php');

             $content = ob_get_clean();

             // convert in PDF
             require_once(dirname(__FILE__) . '/html2pdf/vendor/autoload.php');



             try {
                 $html2pdf = new \HTML2PDF('P', array(101.6, 152.4, 101.6, 152.4), 'fr', true, 'UTF-8', array(-10, -13, -10, -13));
                 $html2pdf->pdf->SetDisplayMode('fullpage');
                 $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
                 $html2pdf->Output($codigo . ' ' . $indice . '.pdf');
             } catch (\HTML2PDF_exception $e) {
                 echo $e;
                 exit;
             }*/
        // get the XLS
//aqui se ejecuta el codigo del reader
        $uploaddir = $this->container->getParameter('belraysa.route.lbrs');
        $uploadfile = $uploaddir . basename('Etiqueta.xlsx');

        $objPHPExcel = \PHPExcel_IOFactory::load($uploadfile);
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

        /** Error reporting */
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        date_default_timezone_set('Europe/London');

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');

        $bultos = $indice . '/' . $cantidad;
// Create new PHPExcel object
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('I3', $bultos)
            ->setCellValue('D8', $concepto)
            ->setCellValue('F10', $peso)
            ->setCellValue('F11', $volumen)
            ->setCellValue('F12', $envia)
            ->setCellValue('F15', $recibe)
            ->setCellValue('F18', $direccion)
            ->setCellValue('F21', $telefono)
            ->setCellValue('E24', $capitulo)
            ->setCellValue('F24', $articulo)
            ->setCellValue('I24', $arancel)
            ->setCellValue('D28', $descripcion)
            ->setCellValue('B35', $codigo);

        $generator = new \Belraysa\BackendBundle\Controller\Barcode\src\BarcodeGeneratorPNG();
        $objDrawing = new \PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('Barcode_' . $codigo);
        $objDrawing->setDescription('Barcode_' . $codigo);

        $path = $this->barcode1Action($request, $codigo, 20, "horizontal", 'horizontal_code.png');

        $objDrawing->setPath($path);
        $objDrawing->setResizeProportional(false);
        $objDrawing->setWidth(550);
        $objDrawing->setHeight(165);
        $objDrawing->setCoordinates('B39');
        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

        $objDrawing2 = new \PHPExcel_Worksheet_Drawing();
        $objDrawing2->setName('Barcode_' . $codigo);
        $objDrawing2->setDescription('Barcode_' . $codigo);

        $path = $this->barcode1Action($request, $codigo, 20, "vertical", 'vertical_code.png');

        $objDrawing2->setPath($path);
        $objDrawing2->setResizeProportional(false);
        $objDrawing2->setWidth(60);
        $objDrawing2->setHeight(590);
        $objDrawing2->setCoordinates('C8');
        $objDrawing2->setWorksheet($objPHPExcel->getActiveSheet());

// Set document properties
        $objPHPExcel->getProperties()->setCreator("BELRAYSA TOURS & TRAVEL GROUP S.A")
            ->setLastModifiedBy("BELRAYSA TOURS & TRAVEL GROUP S.A")
            ->setTitle("ETIQUETA_" . $codigo)
            ->setSubject("ETIQUETA_" . $codigo)
            ->setDescription("ETIQUETA_" . $codigo)
            ->setKeywords("etiqueta " . $codigo)
            ->setCategory("ETIQUETA");


// Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ETIQUETA_' . $codigo . '.xlsx"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function barcodeAction(Request $request, $id, $size)
    {
        $filepath = "";
        $text = $id;
        if ($size == "") {
            $size = "20";
        }
        $orientation = "horizontal";
        $code_type = "Code39";
        $print = true;
        $SizeFactor = 1;

        $code_string = "";
        // Translate the $text into barcode the correct $code_type
        if (in_array(strtolower($code_type), array("code128", "code128b"))) {
            $chksum = 104;
            // Must not change order of array elements as the checksum depends on the array's key to validate final code
            $code_array = array(" " => "212222", "!" => "222122", "\"" => "222221", "#" => "121223", "$" => "121322", "%" => "131222", "&" => "122213", "'" => "122312", "(" => "132212", ")" => "221213", "*" => "221312", "+" => "231212", "," => "112232", "-" => "122132", "." => "122231", "/" => "113222", "0" => "123122", "1" => "123221", "2" => "223211", "3" => "221132", "4" => "221231", "5" => "213212", "6" => "223112", "7" => "312131", "8" => "311222", "9" => "321122", ":" => "321221", ";" => "312212", "<" => "322112", "=" => "322211", ">" => "212123", "?" => "212321", "@" => "232121", "A" => "111323", "B" => "131123", "C" => "131321", "D" => "112313", "E" => "132113", "F" => "132311", "G" => "211313", "H" => "231113", "I" => "231311", "J" => "112133", "K" => "112331", "L" => "132131", "M" => "113123", "N" => "113321", "O" => "133121", "P" => "313121", "Q" => "211331", "R" => "231131", "S" => "213113", "T" => "213311", "U" => "213131", "V" => "311123", "W" => "311321", "X" => "331121", "Y" => "312113", "Z" => "312311", "[" => "332111", "\\" => "314111", "]" => "221411", "^" => "431111", "_" => "111224", "\`" => "111422", "a" => "121124", "b" => "121421", "c" => "141122", "d" => "141221", "e" => "112214", "f" => "112412", "g" => "122114", "h" => "122411", "i" => "142112", "j" => "142211", "k" => "241211", "l" => "221114", "m" => "413111", "n" => "241112", "o" => "134111", "p" => "111242", "q" => "121142", "r" => "121241", "s" => "114212", "t" => "124112", "u" => "124211", "v" => "411212", "w" => "421112", "x" => "421211", "y" => "212141", "z" => "214121", "{" => "412121", "|" => "111143", "}" => "111341", "~" => "131141", "DEL" => "114113", "FNC 3" => "114311", "FNC 2" => "411113", "SHIFT" => "411311", "CODE C" => "113141", "FNC 4" => "114131", "CODE A" => "311141", "FNC 1" => "411131", "Start A" => "211412", "Start B" => "211214", "Start C" => "211232", "Stop" => "2331112");
            $code_keys = array_keys($code_array);
            $code_values = array_flip($code_keys);
            for ($X = 1; $X <= strlen($text); $X++) {
                $activeKey = substr($text, ($X - 1), 1);
                $code_string .= $code_array[$activeKey];
                $chksum = ($chksum + ($code_values[$activeKey] * $X));
            }
            $code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];

            $code_string = "211214" . $code_string . "2331112";
        } elseif (strtolower($code_type) == "code128a") {
            $chksum = 103;
            $text = strtoupper($text); // Code 128A doesn't support lower case
            // Must not change order of array elements as the checksum depends on the array's key to validate final code
            $code_array = array(" " => "212222", "!" => "222122", "\"" => "222221", "#" => "121223", "$" => "121322", "%" => "131222", "&" => "122213", "'" => "122312", "(" => "132212", ")" => "221213", "*" => "221312", "+" => "231212", "," => "112232", "-" => "122132", "." => "122231", "/" => "113222", "0" => "123122", "1" => "123221", "2" => "223211", "3" => "221132", "4" => "221231", "5" => "213212", "6" => "223112", "7" => "312131", "8" => "311222", "9" => "321122", ":" => "321221", ";" => "312212", "<" => "322112", "=" => "322211", ">" => "212123", "?" => "212321", "@" => "232121", "A" => "111323", "B" => "131123", "C" => "131321", "D" => "112313", "E" => "132113", "F" => "132311", "G" => "211313", "H" => "231113", "I" => "231311", "J" => "112133", "K" => "112331", "L" => "132131", "M" => "113123", "N" => "113321", "O" => "133121", "P" => "313121", "Q" => "211331", "R" => "231131", "S" => "213113", "T" => "213311", "U" => "213131", "V" => "311123", "W" => "311321", "X" => "331121", "Y" => "312113", "Z" => "312311", "[" => "332111", "\\" => "314111", "]" => "221411", "^" => "431111", "_" => "111224", "NUL" => "111422", "SOH" => "121124", "STX" => "121421", "ETX" => "141122", "EOT" => "141221", "ENQ" => "112214", "ACK" => "112412", "BEL" => "122114", "BS" => "122411", "HT" => "142112", "LF" => "142211", "VT" => "241211", "FF" => "221114", "CR" => "413111", "SO" => "241112", "SI" => "134111", "DLE" => "111242", "DC1" => "121142", "DC2" => "121241", "DC3" => "114212", "DC4" => "124112", "NAK" => "124211", "SYN" => "411212", "ETB" => "421112", "CAN" => "421211", "EM" => "212141", "SUB" => "214121", "ESC" => "412121", "FS" => "111143", "GS" => "111341", "RS" => "131141", "US" => "114113", "FNC 3" => "114311", "FNC 2" => "411113", "SHIFT" => "411311", "CODE C" => "113141", "CODE B" => "114131", "FNC 4" => "311141", "FNC 1" => "411131", "Start A" => "211412", "Start B" => "211214", "Start C" => "211232", "Stop" => "2331112");
            $code_keys = array_keys($code_array);
            $code_values = array_flip($code_keys);
            for ($X = 1; $X <= strlen($text); $X++) {
                $activeKey = substr($text, ($X - 1), 1);
                $code_string .= $code_array[$activeKey];
                $chksum = ($chksum + ($code_values[$activeKey] * $X));
            }
            $code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];

            $code_string = "211412" . $code_string . "2331112";
        } elseif (strtolower($code_type) == "code39") {
            $code_array = array("0" => "111221211", "1" => "211211112", "2" => "112211112", "3" => "212211111", "4" => "111221112", "5" => "211221111", "6" => "112221111", "7" => "111211212", "8" => "211211211", "9" => "112211211", "A" => "211112112", "B" => "112112112", "C" => "212112111", "D" => "111122112", "E" => "211122111", "F" => "112122111", "G" => "111112212", "H" => "211112211", "I" => "112112211", "J" => "111122211", "K" => "211111122", "L" => "112111122", "M" => "212111121", "N" => "111121122", "O" => "211121121", "P" => "112121121", "Q" => "111111222", "R" => "211111221", "S" => "112111221", "T" => "111121221", "U" => "221111112", "V" => "122111112", "W" => "222111111", "X" => "121121112", "Y" => "221121111", "Z" => "122121111", "-" => "121111212", "." => "221111211", " " => "122111211", "$" => "121212111", "/" => "121211121", "+" => "121112121", "%" => "111212121", "*" => "121121211");

            // Convert to uppercase
            $upper_text = strtoupper($text);

            for ($X = 1; $X <= strlen($upper_text); $X++) {
                $code_string .= $code_array[substr($upper_text, ($X - 1), 1)] . "1";
            }

            $code_string = "1211212111" . $code_string . "121121211";
        } elseif (strtolower($code_type) == "code25") {
            $code_array1 = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
            $code_array2 = array("3-1-1-1-3", "1-3-1-1-3", "3-3-1-1-1", "1-1-3-1-3", "3-1-3-1-1", "1-3-3-1-1", "1-1-1-3-3", "3-1-1-3-1", "1-3-1-3-1", "1-1-3-3-1");

            for ($X = 1; $X <= strlen($text); $X++) {
                for ($Y = 0; $Y < count($code_array1); $Y++) {
                    if (substr($text, ($X - 1), 1) == $code_array1[$Y])
                        $temp[$X] = $code_array2[$Y];
                }
            }

            for ($X = 1; $X <= strlen($text); $X += 2) {
                if (isset($temp[$X]) && isset($temp[($X + 1)])) {
                    $temp1 = explode("-", $temp[$X]);
                    $temp2 = explode("-", $temp[($X + 1)]);
                    for ($Y = 0; $Y < count($temp1); $Y++)
                        $code_string .= $temp1[$Y] . $temp2[$Y];
                }
            }

            $code_string = "1111" . $code_string . "311";
        } elseif (strtolower($code_type) == "codabar") {
            $code_array1 = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "-", "$", ":", "/", ".", "+", "A", "B", "C", "D");
            $code_array2 = array("1111221", "1112112", "2211111", "1121121", "2111121", "1211112", "1211211", "1221111", "2112111", "1111122", "1112211", "1122111", "2111212", "2121112", "2121211", "1121212", "1122121", "1212112", "1112122", "1112221");

            // Convert to uppercase
            $upper_text = strtoupper($text);

            for ($X = 1; $X <= strlen($upper_text); $X++) {
                for ($Y = 0; $Y < count($code_array1); $Y++) {
                    if (substr($upper_text, ($X - 1), 1) == $code_array1[$Y])
                        $code_string .= $code_array2[$Y] . "1";
                }
            }
            $code_string = "11221211" . $code_string . "1122121";
        }

        // Pad the edges of the barcode
        $code_length = 20;
        if ($print) {
            $text_height = 30;
        } else {
            $text_height = 0;
        }

        for ($i = 1; $i <= strlen($code_string); $i++) {
            $code_length = $code_length + (integer)(substr($code_string, ($i - 1), 1));
        }

        if (strtolower($orientation) == "horizontal") {
            $img_width = $code_length * $SizeFactor;
            $img_height = $size;
        } else {
            $img_width = $size;
            $img_height = $code_length * $SizeFactor;
        }

        $image = imagecreate($img_width, $img_height + $text_height);
        $black = imagecolorallocate($image, 0, 0, 0);
        $white = imagecolorallocate($image, 255, 255, 255);

        imagefill($image, 0, 0, $white);
        if ($print) {
            imagestring($image, 5, 31, $img_height, $text, $black);
        }

        $location = 10;
        for ($position = 1; $position <= strlen($code_string); $position++) {
            $cur_size = $location + (substr($code_string, ($position - 1), 1));
            if (strtolower($orientation) == "horizontal")
                imagefilledrectangle($image, $location * $SizeFactor, 0, $cur_size * $SizeFactor, $img_height, ($position % 2 == 0 ? $white : $black));
            else
                imagefilledrectangle($image, 0, $location * $SizeFactor, $img_width, $cur_size * $SizeFactor, ($position % 2 == 0 ? $white : $black));
            $location = $cur_size;
        }

        // Draw barcode to the screen or save in a file
        if ($filepath == "") {

            header('Content-type: image/png');
            imagepng($image);
            imagedestroy($image);
        } else {
            imagepng($image, $filepath);
            imagedestroy($image);
        }


    }

    public function barcode1Action(Request $request, $id, $size, $orientation = "horizontal", $name = 'code.png')
    {
        // $filepath = 'C:/xampp/htdocs/sisger3/web/uploads/barcodes/code.png';
        $filepath = $this->container->getParameter('belraysa.route.barcodes') . $name;
        $text = $id;
        if ($size == "") {
            $size = "20";
        }
        //$orientation = "horizontal";
        $code_type = "Code128";
        $print = true;
        $SizeFactor = 1;

        $code_string = "";
        // Translate the $text into barcode the correct $code_type
        if (in_array(strtolower($code_type), array("code128", "code128b"))) {
            $chksum = 104;
            // Must not change order of array elements as the checksum depends on the array's key to validate final code
            $code_array = array(" " => "212222", "!" => "222122", "\"" => "222221", "#" => "121223", "$" => "121322", "%" => "131222", "&" => "122213", "'" => "122312", "(" => "132212", ")" => "221213", "*" => "221312", "+" => "231212", "," => "112232", "-" => "122132", "." => "122231", "/" => "113222", "0" => "123122", "1" => "123221", "2" => "223211", "3" => "221132", "4" => "221231", "5" => "213212", "6" => "223112", "7" => "312131", "8" => "311222", "9" => "321122", ":" => "321221", ";" => "312212", "<" => "322112", "=" => "322211", ">" => "212123", "?" => "212321", "@" => "232121", "A" => "111323", "B" => "131123", "C" => "131321", "D" => "112313", "E" => "132113", "F" => "132311", "G" => "211313", "H" => "231113", "I" => "231311", "J" => "112133", "K" => "112331", "L" => "132131", "M" => "113123", "N" => "113321", "O" => "133121", "P" => "313121", "Q" => "211331", "R" => "231131", "S" => "213113", "T" => "213311", "U" => "213131", "V" => "311123", "W" => "311321", "X" => "331121", "Y" => "312113", "Z" => "312311", "[" => "332111", "\\" => "314111", "]" => "221411", "^" => "431111", "_" => "111224", "\`" => "111422", "a" => "121124", "b" => "121421", "c" => "141122", "d" => "141221", "e" => "112214", "f" => "112412", "g" => "122114", "h" => "122411", "i" => "142112", "j" => "142211", "k" => "241211", "l" => "221114", "m" => "413111", "n" => "241112", "o" => "134111", "p" => "111242", "q" => "121142", "r" => "121241", "s" => "114212", "t" => "124112", "u" => "124211", "v" => "411212", "w" => "421112", "x" => "421211", "y" => "212141", "z" => "214121", "{" => "412121", "|" => "111143", "}" => "111341", "~" => "131141", "DEL" => "114113", "FNC 3" => "114311", "FNC 2" => "411113", "SHIFT" => "411311", "CODE C" => "113141", "FNC 4" => "114131", "CODE A" => "311141", "FNC 1" => "411131", "Start A" => "211412", "Start B" => "211214", "Start C" => "211232", "Stop" => "2331112");
            $code_keys = array_keys($code_array);
            $code_values = array_flip($code_keys);
            for ($X = 1; $X <= strlen($text); $X++) {
                $activeKey = substr($text, ($X - 1), 1);
                $code_string .= $code_array[$activeKey];
                $chksum = ($chksum + ($code_values[$activeKey] * $X));
            }
            $code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];

            $code_string = "211214" . $code_string . "2331112";
        } elseif (strtolower($code_type) == "code128a") {
            $chksum = 103;
            $text = strtoupper($text); // Code 128A doesn't support lower case
            // Must not change order of array elements as the checksum depends on the array's key to validate final code
            $code_array = array(" " => "212222", "!" => "222122", "\"" => "222221", "#" => "121223", "$" => "121322", "%" => "131222", "&" => "122213", "'" => "122312", "(" => "132212", ")" => "221213", "*" => "221312", "+" => "231212", "," => "112232", "-" => "122132", "." => "122231", "/" => "113222", "0" => "123122", "1" => "123221", "2" => "223211", "3" => "221132", "4" => "221231", "5" => "213212", "6" => "223112", "7" => "312131", "8" => "311222", "9" => "321122", ":" => "321221", ";" => "312212", "<" => "322112", "=" => "322211", ">" => "212123", "?" => "212321", "@" => "232121", "A" => "111323", "B" => "131123", "C" => "131321", "D" => "112313", "E" => "132113", "F" => "132311", "G" => "211313", "H" => "231113", "I" => "231311", "J" => "112133", "K" => "112331", "L" => "132131", "M" => "113123", "N" => "113321", "O" => "133121", "P" => "313121", "Q" => "211331", "R" => "231131", "S" => "213113", "T" => "213311", "U" => "213131", "V" => "311123", "W" => "311321", "X" => "331121", "Y" => "312113", "Z" => "312311", "[" => "332111", "\\" => "314111", "]" => "221411", "^" => "431111", "_" => "111224", "NUL" => "111422", "SOH" => "121124", "STX" => "121421", "ETX" => "141122", "EOT" => "141221", "ENQ" => "112214", "ACK" => "112412", "BEL" => "122114", "BS" => "122411", "HT" => "142112", "LF" => "142211", "VT" => "241211", "FF" => "221114", "CR" => "413111", "SO" => "241112", "SI" => "134111", "DLE" => "111242", "DC1" => "121142", "DC2" => "121241", "DC3" => "114212", "DC4" => "124112", "NAK" => "124211", "SYN" => "411212", "ETB" => "421112", "CAN" => "421211", "EM" => "212141", "SUB" => "214121", "ESC" => "412121", "FS" => "111143", "GS" => "111341", "RS" => "131141", "US" => "114113", "FNC 3" => "114311", "FNC 2" => "411113", "SHIFT" => "411311", "CODE C" => "113141", "CODE B" => "114131", "FNC 4" => "311141", "FNC 1" => "411131", "Start A" => "211412", "Start B" => "211214", "Start C" => "211232", "Stop" => "2331112");
            $code_keys = array_keys($code_array);
            $code_values = array_flip($code_keys);
            for ($X = 1; $X <= strlen($text); $X++) {
                $activeKey = substr($text, ($X - 1), 1);
                $code_string .= $code_array[$activeKey];
                $chksum = ($chksum + ($code_values[$activeKey] * $X));
            }
            $code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];

            $code_string = "211412" . $code_string . "2331112";
        } elseif (strtolower($code_type) == "code39") {
            $code_array = array("0" => "111221211", "1" => "211211112", "2" => "112211112", "3" => "212211111", "4" => "111221112", "5" => "211221111", "6" => "112221111", "7" => "111211212", "8" => "211211211", "9" => "112211211", "A" => "211112112", "B" => "112112112", "C" => "212112111", "D" => "111122112", "E" => "211122111", "F" => "112122111", "G" => "111112212", "H" => "211112211", "I" => "112112211", "J" => "111122211", "K" => "211111122", "L" => "112111122", "M" => "212111121", "N" => "111121122", "O" => "211121121", "P" => "112121121", "Q" => "111111222", "R" => "211111221", "S" => "112111221", "T" => "111121221", "U" => "221111112", "V" => "122111112", "W" => "222111111", "X" => "121121112", "Y" => "221121111", "Z" => "122121111", "-" => "121111212", "." => "221111211", " " => "122111211", "$" => "121212111", "/" => "121211121", "+" => "121112121", "%" => "111212121", "*" => "121121211");

            // Convert to uppercase
            $upper_text = strtoupper($text);

            for ($X = 1; $X <= strlen($upper_text); $X++) {
                $code_string .= $code_array[substr($upper_text, ($X - 1), 1)] . "1";
            }

            $code_string = "1211212111" . $code_string . "121121211";
        } elseif (strtolower($code_type) == "code25") {
            $code_array1 = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
            $code_array2 = array("3-1-1-1-3", "1-3-1-1-3", "3-3-1-1-1", "1-1-3-1-3", "3-1-3-1-1", "1-3-3-1-1", "1-1-1-3-3", "3-1-1-3-1", "1-3-1-3-1", "1-1-3-3-1");

            for ($X = 1; $X <= strlen($text); $X++) {
                for ($Y = 0; $Y < count($code_array1); $Y++) {
                    if (substr($text, ($X - 1), 1) == $code_array1[$Y])
                        $temp[$X] = $code_array2[$Y];
                }
            }

            for ($X = 1; $X <= strlen($text); $X += 2) {
                if (isset($temp[$X]) && isset($temp[($X + 1)])) {
                    $temp1 = explode("-", $temp[$X]);
                    $temp2 = explode("-", $temp[($X + 1)]);
                    for ($Y = 0; $Y < count($temp1); $Y++)
                        $code_string .= $temp1[$Y] . $temp2[$Y];
                }
            }

            $code_string = "1111" . $code_string . "311";
        } elseif (strtolower($code_type) == "codabar") {
            $code_array1 = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "-", "$", ":", "/", ".", "+", "A", "B", "C", "D");
            $code_array2 = array("1111221", "1112112", "2211111", "1121121", "2111121", "1211112", "1211211", "1221111", "2112111", "1111122", "1112211", "1122111", "2111212", "2121112", "2121211", "1121212", "1122121", "1212112", "1112122", "1112221");

            // Convert to uppercase
            $upper_text = strtoupper($text);

            for ($X = 1; $X <= strlen($upper_text); $X++) {
                for ($Y = 0; $Y < count($code_array1); $Y++) {
                    if (substr($upper_text, ($X - 1), 1) == $code_array1[$Y])
                        $code_string .= $code_array2[$Y] . "1";
                }
            }
            $code_string = "11221211" . $code_string . "1122121";
        }

        // Pad the edges of the barcode
        $code_length = 20;
        if ($print) {
            $text_height = 30;
        } else {
            $text_height = 0;
        }

        for ($i = 1; $i <= strlen($code_string); $i++) {
            $code_length = $code_length + (integer)(substr($code_string, ($i - 1), 1));
        }

        if (strtolower($orientation) == "horizontal") {
            $img_width = $code_length * $SizeFactor;
            $img_height = $size;
        } else {
            $img_width = $size;
            $img_height = $code_length * $SizeFactor;
        }

        $image = imagecreate($img_width, $img_height + $text_height);
        $black = imagecolorallocate($image, 0, 0, 0);
        $white = imagecolorallocate($image, 255, 255, 255);

        imagefill($image, 0, 0, $white);
        if ($print) {
            imagestring($image, 5, 31, $img_height, $text, $black);
        }

        $location = 10;
        for ($position = 1; $position <= strlen($code_string); $position++) {
            $cur_size = $location + (substr($code_string, ($position - 1), 1));
            if (strtolower($orientation) == "horizontal")
                imagefilledrectangle($image, $location * $SizeFactor, 0, $cur_size * $SizeFactor, $img_height, ($position % 2 == 0 ? $white : $black));
            else
                imagefilledrectangle($image, 0, $location * $SizeFactor, $img_width, $cur_size * $SizeFactor, ($position % 2 == 0 ? $white : $black));
            $location = $cur_size;
        }

        // Draw barcode to the screen or save in a file
        if ($filepath == "") {

            header('Content-type: image/png');
            imagepng($image);
            imagedestroy($image);
        } else {
            imagepng($image, $filepath);
            imagedestroy($image);
        }
        return $filepath;

    }
}
