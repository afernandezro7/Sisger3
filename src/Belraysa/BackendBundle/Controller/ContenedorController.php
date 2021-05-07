<?php

namespace Belraysa\BackendBundle\Controller;

use Belraysa\BackendBundle\Entity\Anno;
use Belraysa\BackendBundle\Entity\Mes;
use Belraysa\BackendBundle\Form\ManifiestoType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Belraysa\BackendBundle\Entity\Contenedor;
use Belraysa\BackendBundle\Form\ContenedorType;

/**
 * Contenedor controller.
 *
 */
class ContenedorController extends Controller
{

    /**
     * Lists all Contenedor entities.
     *
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:Contenedor')->findOrdenados();

        $entity = new Contenedor();
        $form = $this->createCreateForm($entity);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);

        return $this->render('BackendBundle:Contenedor:index.html.twig', array(
            'entities' => $pagination,
            'form' => $form->createView(),
        ));
    }


    public function filtrarAction()
    {
        $em = $this->getDoctrine()->getManager();

        $codigo = null;
        $sello = null;
        $tipo = null;


        if (array_key_exists('contenedor_codigo', $_GET)) {
            if ($_GET['contenedor_codigo'] != '') {
                $codigo = $_GET['contenedor_codigo'];
            }
        }

        if (array_key_exists('contenedor_sello', $_GET)) {
            if ($_GET['contenedor_sello'] != '') {
                $sello = $_GET['contenedor_sello'];
            }
        }

        if (array_key_exists('contenedor_tipo', $_GET)) {
            if ($_GET['contenedor_tipo'] != '') {
                $tipo = $_GET['contenedor_tipo'];
            }
        }


        $contenedores = $em->getRepository('BackendBundle:Contenedor')->findBusquedaAvanzada($codigo, $sello, $tipo);


        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $contenedores,
            $this->get('request')->query->get('page', 1),
            10);


        return $this->render('BackendBundle:Contenedor:filtered.html.twig', array(
            'entities' => $pagination,
            'codigo' => $codigo,
            'sello' => $sello,
            'tipo' => $tipo
        ));

    }

    /**
     * Creates a new Contenedor entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Contenedor();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if (!$entity->getVolumenM3()) {
                $entity->setVolumenM3($entity->getTipo()->getVolumenM3());
            }
            if (!$entity->getMaxPesoKg()) {
                $entity->setMaxPesoKg($entity->getTipo()->getMaxPesoKg());
            }
            $fecha = $_POST['mes'];
            $fecha = explode('/', $fecha);
            $mes_numero = $fecha[0];
            $anno_nombre = $fecha[1];

            $anno = $em->getRepository('BackendBundle:Anno')->findOneByNombre($anno_nombre);
            if (!$anno) {
                $anno = new Anno();
                $anno->setNombre($anno_nombre);
                $em->persist($anno);
                $em->flush();
            }
            $mesNombre = 'ENERO';
            switch ($mes_numero) {
                case 1:
                    $mesNombre = 'ENERO';
                    break;
                case 2:
                    $mesNombre = 'FEBRERO';
                    break;
                case 3:
                    $mesNombre = 'MARZO';
                    break;
                case 4:
                    $mesNombre = 'ABRIL';
                    break;
                case 5:
                    $mesNombre = 'MAYO';
                    break;
                case 6:
                    $mesNombre = 'JUNIO';
                    break;
                case 7:
                    $mesNombre = 'JULIO';
                    break;
                case 8:
                    $mesNombre = 'AGOSTO';
                    break;
                case 9:
                    $mesNombre = 'SEPTIEMBRE';
                    break;
                case 10:
                    $mesNombre = 'OCTUBRE';
                    break;
                case 11:
                    $mesNombre = 'NOVIEMBRE';
                    break;
                case 12:
                    $mesNombre = 'DICIEMBRE';
            }

            $meses = $em->getRepository('BackendBundle:Mes')->findByNumero($mes_numero);
            $flagMes = true;
            if ($meses) {
                foreach ($meses as $mes) {
                    if ($mes->getAnno()->getId() == $anno->getId()) {
                        $flagMes = true;
                        break;
                    } else {
                        $flagMes = false;
                    }
                }
            } else {
                $flagMes = false;
            }
            if (!$flagMes) {
                $mes = new Mes();
                $mes->setNombre($mesNombre);
                $mes->setNumero($mes_numero);
                $mes->setAnno($anno);
                $em->persist($mes);
                $em->flush();
                $anno->addMes($mes);
                $em->flush();
            }
            $entity->setMes($mes);


            //asignar indice general
            $contenedoresAnteriores = $em->getRepository('BackendBundle:Contenedor')->findContenedoresAnteriores($anno->getNombre(), $mes->getNumero());
            if (sizeof($contenedoresAnteriores) == 0) {
                $entity->setIndice(1);
            } else {
                $entity->setIndice(sizeof($contenedoresAnteriores) + 1);

            }
            $contenedoresPosteriores = $em->getRepository('BackendBundle:Contenedor')->findContenedoresPosteriores($anno->getNombre(), $mes->getNumero());
            foreach ($contenedoresPosteriores as $posterior) {
                $posterior->setIndice($posterior->getIndice() + 1);
            }

            //asignar indice del anno
            $contenedoresAnteriores = $em->getRepository('BackendBundle:Contenedor')->findContenedoresAnterioresAnno($anno->getNombre(), $mes->getNumero());
            if (sizeof($contenedoresAnteriores) == 0) {
                $entity->setIndiceAnual(1);
            } else {
                $entity->setIndiceAnual(sizeof($contenedoresAnteriores) + 1);

            }
            $contenedoresPosteriores = $em->getRepository('BackendBundle:Contenedor')->findContenedoresPosterioresAnno($anno->getNombre(), $mes->getNumero());
            foreach ($contenedoresPosteriores as $posterior) {
                $posterior->setIndiceAnual($posterior->getIndiceAnual() + 1);
            }


            //asignar estado
            $activo = $em->getRepository('BackendBundle:Contenedor')->findContenedorEnUso('COMPLETANDO');
            if (sizeof($activo) == 0) {
                $entity->setEstado('COMPLETANDO');
            } else {
                if ($anno->getNombre() <= $activo[0]->getMes()->getAnno()->getNombre()) {
                    if ($mes->getNumero() < $activo[0]->getMes()->getNumero()) {
                        $activo[0]->setEstado('RESERVANDO');
                        $entity->setEstado('COMPLETANDO');
                    } else {
                        $entity->setEstado('RESERVANDO');
                    }
                } else {
                    $entity->setEstado('RESERVANDO');
                }

            }


            $em->persist($entity);
            $em->flush();
            $flash = $this->get('session')->getFlashBag();
            $flash->set('success', 'Los cambios han sido guardados satisfactoriamente.');

        } else {
            $flash = $this->get('session')->getFlashBag();
            $flash->set('danger', 'Los cambios no han sido guardados satisfactoriamente.');
        }

        if (array_key_exists('programador', $_POST)) {
            return $this->redirect($this->generateUrl('anno'));
        } else {
            return $this->redirect($this->generateUrl('contenedor'));
        }
    }

    /**
     * Creates a form to create a Contenedor entity.
     *
     * @param Contenedor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Contenedor $entity)
    {
        $form = $this->createForm(new ContenedorType(), $entity, array(
            'action' => $this->generateUrl('contenedor_create'),
            'method' => 'POST',
        ));


        return $form;
    }

    /**
     * Displays a form to create a new Contenedor entity.
     *
     */
    public function newAction()
    {
        $entity = new Contenedor();
        $form = $this->createCreateForm($entity);

        return $this->render('BackendBundle:Contenedor:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Contenedor entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Contenedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contenedor entity.');
        }

        return $this->render('BackendBundle:Contenedor:show.html.twig', array(
            'entity' => $entity
        ));
    }

    public function show1Action()
    {
        $em = $this->getDoctrine()->getManager();
        $id = $_GET['id'];
        $entity = $em->getRepository('BackendBundle:Contenedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contenedor entity.');
        }

        $enas = $em->getRepository('BackendBundle:Concepto')->findXTipoXContenedor('ENA', $entity);
        $envios = $em->getRepository('BackendBundle:Concepto')->findXTipoXContenedor('Envio', $entity);
        $menajes = $em->getRepository('BackendBundle:Concepto')->findXTipoXContenedor('Menaje', $entity);

        $total_bultos = 0;
        foreach ($enas as $concepto) {
            $total_bultos += sizeof($concepto->getBultos());
        }
        foreach ($envios as $concepto) {
            $total_bultos += sizeof($concepto->getBultos());
        }
        foreach ($menajes as $concepto) {
            $total_bultos += sizeof($concepto->getBultos());
        }

        return $this->render('BackendBundle:Contenedor:show1.html.twig', array(
            'contenedor' => $entity, 'enas' => sizeof($enas), 'envios' => sizeof($envios), 'menajes' => sizeof($menajes), 'total_bultos' => $total_bultos
        ));
    }

    public function manifiestoAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Contenedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contenedor entity.');
        }


        return $this->render('BackendBundle:Contenedor:show.html.twig', array(
            'entity' => $entity
        ));
    }

    /**
     * Displays a form to edit an existing Contenedor entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Contenedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contenedor entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('BackendBundle:Contenedor:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView()
        ));
    }

    public function editManifiestoAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Contenedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contenedor entity.');
        }

        $editForm = $this->createEditManifiestoForm($entity);

        return $this->render('BackendBundle:Contenedor:editManifiesto.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Creates a form to edit a Contenedor entity.
     *
     * @param Contenedor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Contenedor $entity)
    {
        $form = $this->createForm(new ContenedorType(), $entity, array(
            'action' => $this->generateUrl('contenedor_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    private function createEditManifiestoForm(Contenedor $entity)
    {
        $form = $this->createForm(new ManifiestoType(), $entity, array(
            'action' => $this->generateUrl('contenedor_update_manifiesto', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Edits an existing Contenedor entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Contenedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contenedor entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            if (!$entity->getVolumenM3()) {
                $entity->setVolumenM3($entity->getTipo()->getVolumenM3());
            }
            if (!$entity->getMaxPesoKg()) {
                $entity->setMaxPesoKg($entity->getTipo()->getMaxPesoKg());
            }

            $pesoActual = $entity->getPesoKg();
            $volumenActual = $entity->getVolumenConceptosM3();
            if (($pesoActual <= $entity->getMaxPesoKg()) && ($volumenActual <= $entity->getVolumenM3())) {
                $em->flush();
                $flash = $this->get('session')->getFlashBag();
                $flash->set('success', 'Los cambios han sido guardados satisfactoriamente.');
            } else {
                $flash = $this->get('session')->getFlashBag();
                $flash->set('danger', 'No se ha podido completar la operación pues actualmente el contenedor contiene bultos con peso y/o volumen totales superiores a los valores especificados.');
            }

            if (array_key_exists('programador', $_POST)) {
                return $this->redirect($this->generateUrl('anno'));
            } else {
                return $this->redirect($this->generateUrl('contenedor'));
            }
        } else {
            print_r($editForm->getErrors());
            die;
        }

        return $this->render('BackendBundle:Contenedor:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView()
        ));
    }

    public function updateManifiestoAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Contenedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contenedor entity.');
        }
        $editForm = $this->createEditManifiestoForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            //actualizar la fecha HBL de los HBLs que contiene el contenedor
            foreach ($entity->getConceptos() as $concepto) {
                $concepto->setFechaHBL($entity->getFechaSalida());
            }
            $em->flush();
            return $this->redirect($this->generateUrl('contenedor'));
        } else {
            print_r($editForm->getErrors());
            die;
        }

        return $this->render('BackendBundle:Contenedor:editManifiesto.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a Contenedor entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Contenedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contenedor entity.');
        }
        $anno = $entity->getMes()->getAnno()->getNombre();
        $indice = $entity->getIndiceAnual();
        $contenedoresPosteriores = $em->getRepository('BackendBundle:Contenedor')->findContenedoresPosterioresIndice($anno, $indice);
        foreach ($contenedoresPosteriores as $posterior) {
            $posterior->setIndice($posterior->getIndice() - 1);
            if ($posterior->getMes()->getAnno()->getNombre() == $anno) {
                $posterior->setIndiceAnual($posterior->getIndiceAnual() - 1);
            }
        }
        $em->remove($entity);
        $em->flush();
        $flash = $this->get('session')->getFlashBag();
        $flash->set('success', 'Los cambios han sido guardados satisfactoriamente.');
        return $this->redirect($this->generateUrl('contenedor'));
    }


    public function agregarHBLSAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Contenedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contenedor entity.');
        }

        return $this->render('BackendBundle:Contenedor:agregarHBLS.html.twig', array(
            'entity' => $entity,
        ));
    }


    public function cerrarAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Contenedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contenedor entity.');
        }

        $entity->setEstado('CERRADO');
        $reservado = $em->getRepository('BackendBundle:Contenedor')->findBy(array('estado' => 'RESERVANDO'), array('indice' => 'ASC'));
        if($reservado){
            $nominado = $reservado[0];
            if ($nominado) {
                $nominado->setEstado('COMPLETANDO');
            }
        }

        $em->flush();

        return $this->redirect($this->generateUrl('contenedor'));
    }

    public function manifiestoV1Action($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Contenedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contenedor entity.');
        }

        $motonave = $entity->getMotonave();
        $puertoSalida = $entity->getPuertoSalida();
        $puertoEntrada = $entity->getPuertoEntrada();
        $fechaSalida = "";
        if($entity->getFechaSalida())
            $fechaSalida = date_format($entity->getFechaSalida(), 'd/m/Y');
        $fechaEntrada = "";
        if($entity->getFechaEntrada())
            $fechaEntrada = date_format($entity->getFechaEntrada(), 'd/m/Y');
        $mbl = $entity->getMBL();
        $codigo = $entity->getCodigo();
        $sello = $entity->getSello();
        $cantHBL = sizeof($entity->getConceptos());
        $cantENA = sizeof($em->getRepository('BackendBundle:Concepto')->findXTipoXContenedor('ENA', $entity));
        $cantEnvio = sizeof($em->getRepository('BackendBundle:Concepto')->findXTipoXContenedor('Envio', $entity));
        $cantMenaje = sizeof($em->getRepository('BackendBundle:Concepto')->findXTipoXContenedor('Menaje', $entity));


        // get the XLS
        //aqui se ejecuta el codigo del reader
        $uploaddir = $this->container->getParameter('belraysa.route.lbrs');
        $uploadfile = $uploaddir . basename('ManifiestoV.1.xlsx');

        $objPHPExcel = \PHPExcel_IOFactory::load($uploadfile);
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

        /** Error reporting */
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        date_default_timezone_set('Europe/London');

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B4', $motonave)
            ->setCellValue('B5', $puertoSalida)
            ->setCellValue('B6', $fechaSalida)
            ->setCellValue('E5', $puertoEntrada)
            ->setCellValue('E6', $fechaEntrada)
            ->setCellValue('C8', $mbl)
            ->setCellValue('C9', $codigo)
            ->setCellValue('C10', $sello)
            ->setCellValue('F7', $cantHBL)
            ->setCellValue('F8', $cantENA)
            ->setCellValue('F9', $cantEnvio)
            ->setCellValue('F10', $cantMenaje);

        $indice = 14;
        $style = $objPHPExcel->getActiveSheet()->getStyle('A13');
        foreach ($entity->getConceptos() as $concepto) {
            $objPHPExcel->getActiveSheet()->duplicateStyle($style,
                'A' . $indice . ':J' . $indice
            );

            $address = $concepto->getConsignado()->getAddress();
            $municipality = $concepto->getConsignado()->getMunicipality();
            $province = $concepto->getConsignado()->getProvince();
            $country = $concepto->getConsignado()->getCountry();
            $telefono = $concepto->getConsignado()->getPhones();
            $cell = $concepto->getConsignado()->getCell();

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
            if ($telefono) {
                $dirArray[] = $telefono;
            }
            if ($cell) {
                $dirArray[] = $cell;
            }


            $direccion = '';
            for ($j = 0; $j < sizeof($dirArray); $j++) {
                if ($j == 0) {
                    $direccion = $direccion . $dirArray[$j];
                } else {
                    $direccion = $direccion . ', ' . $dirArray[$j];
                }
            }

            $i = 0;
            $kilos = 0;
            $m3 = 0;
            $packages = sizeof($concepto->getBultos());

            $descripcion = '';
            $descripciones = array();


            foreach ($concepto->getBultos() as $bulto) {
                foreach ($bulto->getMercancias() as $mercancia) {
                    $descripciones[] = $mercancia->getDescripcion();
                    $kilos = $kilos + $mercancia->getPesoKg();
                    $m3 = $m3 + $mercancia->getVolumenM3();
                    $i++;
                }
            }

            for ($j = 0; $j < sizeof($descripciones); $j++) {
                if ($j == 0) {
                    $descripcion = $descripcion . $descripciones[$j];
                } else {
                    $descripcion = $descripcion . ', ' . $descripciones[$j];
                }
            }
            if ($concepto->getTipo() == 'Envio') {
                $rtteNombre = $concepto->getRemitenteNombre();
                $rttePassport = $concepto->getRemitenteCedula();
            } else {
                $rtteNombre = $concepto->getRemitente()->getFirstName() . ' ' . $concepto->getRemitente()->getLastName();
                $rttePassport = $concepto->getRemitente()->getPassport();
            }
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indice, $concepto->getSisgerCode())
                ->setCellValue('B' . $indice, $rtteNombre)
                ->setCellValue('C' . $indice, $rttePassport)
                ->setCellValue('D' . $indice, $concepto->getConsignado()->getFirstName() . ' ' . $concepto->getConsignado()->getLastName() . ' ' . $direccion)
                ->setCellValue('E' . $indice, $concepto->getConsignado()->getDni())
                ->setCellValue('F' . $indice, $packages)
                ->setCellValue('G' . $indice, $kilos)
                ->setCellValue('H' . $indice, $m3)
                ->setCellValue('I' . $indice, $descripcion)
                ->setCellValue('J' . $indice, $concepto->getTipo());

            $indice += 1;
        }
        $formula = $indice - 1;
        $objPHPExcel->getActiveSheet()
            ->setCellValue('F' . $indice, '=SUM(F13:F' . $formula . ')');
        $objPHPExcel->getActiveSheet()
            ->setCellValue('G' . $indice, '=SUM(G13:G' . $formula . ')');
        $objPHPExcel->getActiveSheet()
            ->setCellValue('H' . $indice, '=SUM(H13:H' . $formula . ')');

        // Create new PHPExcel object


        // Set document properties
        $objPHPExcel->getProperties()->setCreator("BELRAYSA TOURS & TRAVEL GROUP S.A")
            ->setLastModifiedBy("BELRAYSA TOURS & TRAVEL GROUP S.A")
            ->setTitle("MANIFIESTO_" . $codigo . '_BELRAYSA TOURS & TRAVEL GROUP S.A')
            ->setSubject("MANIFIESTO_" . $codigo . '_BELRAYSA TOURS & TRAVEL GROUP S.A')
            ->setDescription("MANIFIESTO_" . $codigo . '_BELRAYSA TOURS & TRAVEL GROUP S.A')
            ->setKeywords("manifiesto " . $codigo)
            ->setCategory("MANIFIESTO_" . $codigo . '_BELRAYSA TOURS & TRAVEL GROUP S.A');


        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="MANIFIESTO_' . $codigo . '_BELRAYSA TOURS & TRAVEL GROUP S.A.xlsx"');
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

    public function manifiestoV2Action($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Contenedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contenedor entity.');
        }

        $motonave = $entity->getMotonave();
        $puertoSalida = $entity->getPuertoSalida();
        $puertoEntrada = $entity->getPuertoEntrada();
        $fechaSalida = "";
        if($entity->getFechaSalida())
            $fechaSalida = date_format($entity->getFechaSalida(), 'd/m/Y');
        $fechaEntrada = "";
        if($entity->getFechaEntrada())
            $fechaEntrada = date_format($entity->getFechaEntrada(), 'd/m/Y');
        $mbl = $entity->getMBL();
        $codigo = $entity->getCodigo();
        $sello = $entity->getSello();
        $cantHBL = sizeof($entity->getConceptos());
        $cantENA = sizeof($em->getRepository('BackendBundle:Concepto')->findXTipoXContenedor('ENA', $entity));
        $cantEnvio = sizeof($em->getRepository('BackendBundle:Concepto')->findXTipoXContenedor('Envio', $entity));
        $cantMenaje = sizeof($em->getRepository('BackendBundle:Concepto')->findXTipoXContenedor('Menaje', $entity));


        // get the XLS
        //aqui se ejecuta el codigo del reader
        $uploaddir = $this->container->getParameter('belraysa.route.lbrs');
        $uploadfile = $uploaddir . basename('ManifiestoV.2.xls');

        $objPHPExcel = \PHPExcel_IOFactory::load($uploadfile);
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

        /** Error reporting */
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        date_default_timezone_set('Europe/London');

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B4', $mbl)
            ->setCellValue('B5', $codigo)
            ->setCellValue('B6', $cantHBL)
            ->setCellValue('B7', $entity->getPesoKg())
            ->setCellValue('B8', $fechaSalida)
            ->setCellValue('D5', $sello);

        $indice = 12;
        $style = $objPHPExcel->getActiveSheet()->getStyle('A12');
        foreach ($entity->getConceptos() as $concepto) {
            if ($indice > 12) {
                $objPHPExcel->getActiveSheet()->duplicateStyle($style,
                    'A' . $indice . ':M' . $indice
                );
            }
            $address = $concepto->getConsignado()->getAddress();
            $municipality = $concepto->getConsignado()->getMunicipality();
            $province = $concepto->getConsignado()->getProvince();
            $country = $concepto->getConsignado()->getCountry();
            $telefono = $concepto->getConsignado()->getPhones();
            $cell = $concepto->getConsignado()->getCell();

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
            if ($telefono) {
                $dirArray[] = $telefono;
            }
            if ($cell) {
                $dirArray[] = $cell;
            }


            $direccion = '';
            for ($j = 0; $j < sizeof($dirArray); $j++) {
                if ($j == 0) {
                    $direccion = $direccion . $dirArray[$j];
                } else {
                    $direccion = $direccion . ', ' . $dirArray[$j];
                }
            }

            $i = 0;
            $kilos = 0;
            $m3 = 0;
            $packages = sizeof($concepto->getBultos());

            $descripcion = '';
            $descripciones = array();


            foreach ($concepto->getBultos() as $bulto) {
                foreach ($bulto->getMercancias() as $mercancia) {
                    $descripciones[] = $mercancia->getDescripcion();
                    $kilos = $kilos + $mercancia->getPesoKg();
                    $m3 = $m3 + $mercancia->getVolumenM3();
                    $i++;
                }
            }

            for ($j = 0; $j < sizeof($descripciones); $j++) {
                if ($j == 0) {
                    $descripcion = $descripcion . $descripciones[$j];
                } else {
                    $descripcion = $descripcion . ', ' . $descripciones[$j];
                }
            }

            if ($concepto->getTipo() == 'Envio') {
                $rtte = $concepto->getRemitenteNombre();
            } else {
                $rtte = $concepto->getRemitente()->getFirstName() . ' ' . $concepto->getRemitente()->getLastName();
            }
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indice, $concepto->getSisgerCode())
                ->setCellValue('B' . $indice, $rtte)
                ->setCellValue('C' . $indice, $concepto->getConsignado()->getFirstName() . ' ' . $concepto->getConsignado()->getLastName())
                ->setCellValue('D' . $indice, $direccion)
                ->setCellValue('E' . $indice, $concepto->getConsignado()->getDni())
                ->setCellValue('F' . $indice, $telefono)
                ->setCellValue('G' . $indice, $cell)
                ->setCellValue('H' . $indice, $concepto->getConsignado()->getEmail())
                ->setCellValue('I' . $indice, sizeof($concepto->getBultos()))
                ->setCellValue('J' . $indice, $concepto->getPesoKg())
                ->setCellValue('K' . $indice, $concepto->getVolumenM3())
                ->setCellValue('L' . $indice, $descripcion)
                ->setCellValue('M' . $indice, $concepto->getTipo());

            $indice += 1;
        }

        // Create new PHPExcel object


        // Set document properties
        $objPHPExcel->getProperties()->setCreator("BELRAYSA TOURS & TRAVEL GROUP S.A")
            ->setLastModifiedBy("BELRAYSA TOURS & TRAVEL GROUP S.A")
            ->setTitle("MANIFIESTO_" . $codigo . '_BELRAYSA TOURS & TRAVEL GROUP S.A')
            ->setSubject("MANIFIESTO_" . $codigo . '_BELRAYSA TOURS & TRAVEL GROUP S.A')
            ->setDescription("MANIFIESTO_" . $codigo . '_BELRAYSA TOURS & TRAVEL GROUP S.A')
            ->setKeywords("manifiesto " . $codigo)
            ->setCategory("MANIFIESTO_" . $codigo . '_BELRAYSA TOURS & TRAVEL GROUP S.A');


        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="MANIFIESTO_' . $codigo . '_BELRAYSA TOURS & TRAVEL GROUP S.A.xlsx"');
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

    public function manifiestoV3Action($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Contenedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contenedor entity.');
        }

        $motonave = $entity->getMotonave();
        $puertoSalida = $entity->getPuertoSalida();
        $puertoEntrada = $entity->getPuertoEntrada();
        $fechaSalida = "";
        if($entity->getFechaSalida())
            $fechaSalida = date_format($entity->getFechaSalida(), 'd/m/Y');
        $fechaEntrada = "";
        if($entity->getFechaEntrada())
            $fechaEntrada = date_format($entity->getFechaEntrada(), 'd/m/Y');
        $mbl = $entity->getMBL();
        $codigo = $entity->getCodigo();
        $sello = $entity->getSello();
        $cantHBL = sizeof($entity->getConceptos());
        $cantENA = sizeof($em->getRepository('BackendBundle:Concepto')->findXTipoXContenedor('ENA', $entity));
        $cantEnvio = sizeof($em->getRepository('BackendBundle:Concepto')->findXTipoXContenedor('Envio', $entity));
        $cantMenaje = sizeof($em->getRepository('BackendBundle:Concepto')->findXTipoXContenedor('Menaje', $entity));


        // get the XLS
        //aqui se ejecuta el codigo del reader
        $uploaddir = $this->container->getParameter('belraysa.route.lbrs');
        $uploadfile = $uploaddir . basename('ManifiestoV.3.xls');

        $objPHPExcel = \PHPExcel_IOFactory::load($uploadfile);
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

        /** Error reporting */
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        date_default_timezone_set('Europe/London');

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B4', $mbl)
            ->setCellValue('B5', $codigo)
            ->setCellValue('B6', $cantHBL)
            ->setCellValue('B7', $entity->getPesoKg())
            ->setCellValue('B8', $fechaSalida)
            ->setCellValue('D5', $sello);

        $indice1 = 12;
        $indice2 = 12;
        $style = $objPHPExcel->getActiveSheet()->getStyle('A12:P12');
        $reciboContado = array();
        $expedientes = array();
        foreach ($entity->getConceptos() as $concepto) {
            $exp = $concepto->getExpediente();
            $expedientes[] = $exp;
        }

        $expedientes = array_unique($expedientes);
        $totalBultos = 0;
        $totalPeso = 0;
        $totalVolumen = 0;

        foreach ($expedientes as $exp) {
            if(!$exp) continue;
            $conceptos = sizeof($exp->getConceptos());
            $recibos = sizeof($exp->getRecibos());
            $objPHPExcel->getActiveSheet()->duplicateStyle($style,
                'A' . $indice1 . ':Q' . ($indice1 + $conceptos * $recibos - 1)
            );

            $to = $indice1 + $recibos * $conceptos - 1;
            if($to < $indice1)
                $to = $indice1;
            $objPHPExcel->setActiveSheetIndex(0)
                ->mergeCells('O' . $indice1 . ':O' . $to);

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('O' . $indice1, "E-" . $exp->getId());

            foreach ($exp->getConceptos() as $concepto) {
                $totalPeso += $concepto->getPesoKg();
                $totalVolumen += $concepto->getVolumenM3();
                $totalBultos += sizeof($concepto->getBultos());

                $passport = $concepto->getConsignado()->getPassport();
                $address = $concepto->getConsignado()->getAddress();
                $municipality = $concepto->getConsignado()->getMunicipality();
                $province = $concepto->getConsignado()->getProvince();
                $country = $concepto->getConsignado()->getCountry();
                $telefono = $concepto->getConsignado()->getPhones();
                $cell = $concepto->getConsignado()->getCell();

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
                if ($telefono) {
                    $dirArray[] = $telefono;
                }
                if ($cell) {
                    $dirArray[] = $cell;
                }

                $direccion = '';
                for ($j = 0; $j < sizeof($dirArray); $j++) {
                    if ($j == 0) {
                        $direccion = $direccion . $dirArray[$j];
                    } else {
                        $direccion = $direccion . ', ' . $dirArray[$j];
                    }
                }

                $i = 0;
                $kilos = 0;
                $m3 = 0;

                $descripcion = '';
                $descripciones = array();

                foreach ($concepto->getBultos() as $bulto) {
                    foreach ($bulto->getMercancias() as $mercancia) {
                        $descripciones[] = $mercancia->getDescripcion();
                        $kilos = $kilos + $mercancia->getPesoKg();
                        $m3 = $m3 + $mercancia->getVolumenM3();
                        $i++;
                    }
                }

                for ($j = 0; $j < sizeof($descripciones); $j++) {
                    if ($j == 0) {
                        $descripcion = $descripcion . $descripciones[$j];
                    } else {
                        $descripcion = $descripcion . ', ' . $descripciones[$j];
                    }
                }

                if ($concepto->getTipo() == 'Envio') {
                    $rtte = $concepto->getRemitenteNombre();
                } else {
                    $rtte = $concepto->getRemitente()->getFirstName() . ' ' . $concepto->getRemitente()->getLastName();
                }

                $to = $indice1 + $recibos - 1;
                if($to < $indice1)
                    $to = $indice1;

                $objPHPExcel->setActiveSheetIndex(0)
                    ->mergeCells('A' . $indice1 . ':A' . $to)
                    ->mergeCells('B' . $indice1 . ':B' . $to)
                    ->mergeCells('C' . $indice1 . ':C' . $to)
                    ->mergeCells('D' . $indice1 . ':D' . $to)
                    ->mergeCells('E' . $indice1 . ':E' . $to)
                    ->mergeCells('F' . $indice1 . ':F' . $to)
                    ->mergeCells('G' . $indice1 . ':G' . $to)
                    ->mergeCells('H' . $indice1 . ':H' . $to)
                    ->mergeCells('I' . $indice1 . ':I' . $to)
                    ->mergeCells('J' . $indice1 . ':J' . $to)
                    ->mergeCells('K' . $indice1 . ':K' . $to)
                    ->mergeCells('L' . $indice1 . ':L' . $to)
                    ->mergeCells('M' . $indice1 . ':M' . $to)
                    ->mergeCells('N' . $indice1 . ':N' . $to);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $indice1, $concepto->getSisgerCode())
                    ->setCellValue('B' . $indice1, $rtte)
                    ->setCellValue('C' . $indice1, $concepto->getConsignado()->getFirstName() . ' ' . $concepto->getConsignado()->getLastName())
                    ->setCellValue('D' . $indice1, $direccion)
                    ->setCellValue('E' . $indice1, $concepto->getConsignado()->getDni())
                    ->setCellValue('F' . $indice1, $passport)
                    ->setCellValue('G' . $indice1, $telefono)
                    ->setCellValue('H' . $indice1, $cell)
                    ->setCellValue('I' . $indice1, $concepto->getConsignado()->getEmail())
                    ->setCellValue('J' . $indice1, sizeof($concepto->getBultos()))
                    ->setCellValue('K' . $indice1, $concepto->getPesoKg())
                    ->setCellValue('L' . $indice1, $concepto->getVolumenM3())
                    ->setCellValue('M' . $indice1, $descripcion)
                    ->setCellValue('N' . $indice1, $concepto->getTipo());
                $indice1 += $recibos;
            }

            foreach ($exp->getRecibos() as $recibo) {
                $to = $indice2 + $conceptos - 1;
                if($to < $indice2)
                    $to = $indice2;

                $objPHPExcel->setActiveSheetIndex(0)
                    ->mergeCells('P' . $indice2 . ':P' . $to)
                    ->mergeCells('Q' . $indice2 . ':Q' . $to);

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('P' . $indice2, "I-" . $recibo->getId())
                    ->setCellValue('Q' . $indice2, $recibo->getImporte());

                $reciboContado[$recibo->getId()] = $recibo->getImporte();
                $indice2 += $conceptos;
            }
        }

        $totalIngreso = array_sum($reciboContado);
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('J9', $totalBultos)
            ->setCellValue('K9', $totalPeso)
            ->setCellValue('L9', $totalVolumen)
            ->setCellValue('Q9', $totalIngreso);

        // Create new PHPExcel object


        // Set document properties
        $objPHPExcel->getProperties()->setCreator("BELRAYSA TOURS & TRAVEL GROUP S.A")
            ->setLastModifiedBy("BELRAYSA TOURS & TRAVEL GROUP S.A")
            ->setTitle("MANIFIESTO_" . $codigo . '_BELRAYSA TOURS & TRAVEL GROUP S.A')
            ->setSubject("MANIFIESTO_" . $codigo . '_BELRAYSA TOURS & TRAVEL GROUP S.A')
            ->setDescription("MANIFIESTO_" . $codigo . '_BELRAYSA TOURS & TRAVEL GROUP S.A')
            ->setKeywords("manifiesto " . $codigo)
            ->setCategory("MANIFIESTO_" . $codigo . '_BELRAYSA TOURS & TRAVEL GROUP S.A');


        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="MANIFIESTO_' . $codigo . '_BELRAYSA TOURS & TRAVEL GROUP S.A.xlsx"');
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
}
