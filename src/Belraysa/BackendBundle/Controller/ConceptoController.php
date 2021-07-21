<?php

namespace Belraysa\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Belraysa\BackendBundle\Entity\Concepto;
use Belraysa\BackendBundle\Form\ConceptoType;

/**
 * Concepto controller.
 *
 */
class ConceptoController extends Controller
{

    /**
     * Lists all Concepto entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:Concepto')->findAll();

        return $this->render('BackendBundle:Concepto:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    public function indexByContenedorAction($contenedor)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:Concepto')->findByContenedor($contenedor);

        return $this->render('BackendBundle:Concepto:index.html.twig', array(
            'entities' => $entities,
            'contenedor' => $contenedor,
        ));
    }

    /**
     * Creates a new Concepto entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Concepto();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('concepto_show', array('id' => $entity->getId())));
        }

        return $this->render('BackendBundle:Concepto:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Concepto entity.
     *
     * @param Concepto $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Concepto $entity)
    {
        $form = $this->createForm(new ConceptoType(), $entity, array(
            'action' => $this->generateUrl('concepto_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Concepto entity.
     *
     */
    public function newAction()
    {
        $entity = new Concepto();
        $form = $this->createCreateForm($entity);

        return $this->render('BackendBundle:Concepto:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Concepto entity.
     *
     */
    public function showAction()
    {
        $em = $this->getDoctrine()->getManager();
        $id = $_GET['id'];
        $entity = $em->getRepository('BackendBundle:Concepto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Concepto entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Concepto:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Concepto entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Concepto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Concepto entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Concepto:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Concepto entity.
     *
     * @param Concepto $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Concepto $entity)
    {
        $form = $this->createForm(new ConceptoType(), $entity, array(
            'action' => $this->generateUrl('concepto_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Concepto entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Concepto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Concepto entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('concepto_edit', array('id' => $id)));
        }

        return $this->render('BackendBundle:Concepto:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Concepto entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackendBundle:Concepto')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Concepto entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('concepto'));
    }

    /**
     * Creates a form to delete a Concepto entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('concepto_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    public function hblAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Concepto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Concepto entity.');
        }

        if ($entity->getTipo() == 'Envio') {
            $exporter = $entity->getRemitenteNombre();
        } else {
            $exporter = $entity->getRemitente()->getFirstName() . ' ' . $entity->getRemitente()->getLastName();
        }
        if ($entity->getConsignado()->getDni()) {
            $consigned = $entity->getConsignado()->getFirstName() . ' ' . $entity->getConsignado()->getLastName() . ', ' . $entity->getConsignado()->getDni();
        } else {
            $consigned = $entity->getConsignado()->getFirstName() . ' ' . $entity->getConsignado()->getLastName();
        }
        if ($entity->getFechaHBL()) {
            $dia = date_format($entity->getFechaHBL(), 'd');
            $mes = date_format($entity->getFechaHBL(), 'm');
            $anno = date_format($entity->getFechaHBL(), 'Y');
        } else {
            $dia = '';
            $mes = '';
            $anno = '';
        }
        $mbl = '';
        $origen = '';
        $destino = '';
        $motonave = '';
        $contenedor = '';
        if ($entity->getContenedor()) {
            $mbl = $entity->getContenedor()->getMbl();
            $origen = $entity->getContenedor()->getPuertoSalida();
            $destino = $entity->getContenedor()->getPuertoEntrada();
            $motonave = $entity->getContenedor()->getMotonave();

            $codigoContenedor = $entity->getContenedor()->getCodigo();
            $sello = $entity->getContenedor()->getSello();
            $dirContenedor = array();
            if ($codigoContenedor) {
                $dirContenedor[] = $codigoContenedor;
            }
            if ($sello) {
                $dirContenedor[] = $sello;
            }

            for ($j = 0; $j < sizeof($dirContenedor); $j++) {
                if ($j == 0) {
                    $contenedor = $contenedor . $dirContenedor[$j];
                } else {
                    $contenedor = $contenedor . ', ' . $dirContenedor[$j];
                }
            }
        }

        $address = $entity->getConsignado()->getAddress();
        $municipality = $entity->getConsignado()->getMunicipality();
        $province = $entity->getConsignado()->getProvince();
        $country = $entity->getConsignado()->getCountry();
        $telefono = $entity->getConsignado()->getPhones();
        $cell = $entity->getConsignado()->getCell();
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


        $code = $entity->getSisgerCode();

        $i = 0;
        $kilos = 0;
        $m3 = 0;
        $packages = sizeof($entity->getBultos());

        $descripcion = '';
        $descripciones = array();


        foreach ($entity->getBultos() as $bulto) {
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
        // get the XLS
        //aqui se ejecuta el codigo del reader
        $uploaddir = $this->container->getParameter('belraysa.route.lbrs');
        $uploadfile = $uploaddir . basename('HBL.xlsx');

        $objPHPExcel = \PHPExcel_IOFactory::load($uploadfile);
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

        /** Error reporting */
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        date_default_timezone_set('Europe/London');

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');


        // Create new PHPExcel object
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A3', $exporter)
            ->setCellValue('E3', 'MBL')
            ->setCellValue('E5', 'MBL: ' . $mbl)
            ->setCellValue('A17', $motonave)
            ->setCellValue('E19', 'BUQUE')
            ->setCellValue('C17', $origen)
            ->setCellValue('E10', $origen)
            ->setCellValue('A19', $destino)
            ->setCellValue('D19', $destino)
            ->setCellValue('G3', $code)
            ->setCellValue('A7', $consigned)
            ->setCellValue('A8', $direccion)
            ->setCellValue('A12', $consigned)
            ->setCellValue('A13', $direccion)
            ->setCellValue('D22', $descripcion)
            ->setCellValue('C22', $packages)
            ->setCellValue('G22', $kilos)
            ->setCellValue('H22', $m3)
            ->setCellValue('A22', $contenedor)
            ->setCellValue('G41', $code)
            ->setCellValue('F38', $mes)
            ->setCellValue('G38', $dia)
            ->setCellValue('H38', $anno)
            ->setCellValue('G35', $origen);

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("BELRAYSA TOURS & TRAVEL GROUP S.A")
            ->setLastModifiedBy("BELRAYSA TOURS & TRAVEL GROUP S.A")
            ->setTitle("HBL_" . $code)
            ->setSubject("HBL_" . $code)
            ->setDescription("HBL_" . $code)
            ->setKeywords("hbl " . $code)
            ->setCategory("HBL");


        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="HBL_' . $code . '.xlsx"');
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

    public function moveAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Concepto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Mercancia entity.');
        }

        $contenedor_original = $entity->getContenedor();
        $target = $_POST['target'];
        $target = $em->getRepository('BackendBundle:Contenedor')->find($target);

        $valorPeso = $entity->getPesoKg();
        $valorVolumen = $entity->getVolumenM3();

        $limitePeso = $target->getMaxPesoKg();
        $limiteVolumen = $target->getVolumenM3();

        if (($valorPeso <= $limitePeso) && ($valorVolumen <= $limiteVolumen)) {


            $contenedor_original->removeConcepto($entity);
            $target->addConcepto($entity);
            $em->flush();
        } else {
            $flash = $this->get('session')->getFlashBag();
            $flash->set('danger', 'La operación que está intentando realizar no es posible porque excede el límite en peso y/o volumen del contenedor.');
        }

        return $this->redirect($this->generateUrl('anno'));
    }

    public function toHBLAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Concepto')->find($id);
        //  $contenedor = $em->getRepository('BackendBundle:Contenedor')->findContenedorEnUso()[0];
        $entity->setEstado('HBL');
        /*    if ($entity->getContenedor()->getId() != $contenedor->getId()) {

                $limitePeso = $contenedor->getMaxPesoKg();
                $limiteVolumen = $contenedor->getVolumenM3();
                $valorPeso = $contenedor->getPesoKg() + $entity->getPesoKg();
                $valorVolumen = $contenedor->getVolumenConceptosM3() + $entity->getVolumenM3();
                if (($valorPeso <= $limitePeso) && ($valorVolumen <= $limiteVolumen)) {
                    $now = new \DateTime();
                    $sisgerCode = "LBRS" . date_format($now, 'y')[1] . date_format($now, 'm') . date_format($now, 'd') . str_pad($entity->getId(), 6, 0, STR_PAD_LEFT);
                    $entity->setContenedor($contenedor);
                    $entity->setSisgerCode($sisgerCode);
                    $entity->setFechaHBL(new \DateTime());
                    $em->flush();
                    $flash = $this->get('session')->getFlashBag();
                    $flash->set('success', 'Los cambios han sido guardados satisfactoriamente.');
                } else {
                    $flash = $this->get('session')->getFlashBag();
                    $flash->set('danger', 'No se ha podido completar la operación pues actualmente el contenedor en uso no tiene capacidad en peso y/o volumen para asumir este HBL.');
                }
            } else {*/
        $now = new \DateTime();
        //$sisgerCode = "LBRS" . date_format($now, 'y')[1] . date_format($now, 'm') . date_format($now, 'd') . str_pad($entity->getId(), 6, 0, STR_PAD_LEFT);
        $sisgerCode = "BRS". date_format($now, 'y') . "000" . str_pad($entity->getId(), 6, 0, STR_PAD_LEFT);
        $entity->setSisgerCode($sisgerCode);
        //$entity->setFechaHBL(new \DateTime());
        $em->flush();
        $flash = $this->get('session')->getFlashBag();
        $flash->set('success', 'Los cambios han sido guardados satisfactoriamente.');
        /*  }*/
        if ($entity->getTipo() == 'ENA') {
            return $this->redirect($this->generateUrl('ena'));
        } elseif ($entity->getTipo() == 'Envio') {
            return $this->redirect($this->generateUrl('envio'));
        } else {
            return $this->redirect($this->generateUrl('menaje'));
        }
    }

    public function toRVAAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Concepto')->find($id);

        $sisgerCode = substr_replace($entity->getSisgerCode(), 'RVA', 0, 4);
        $entity->setSisgerCode($sisgerCode);
        $em->flush();
        if ($entity->getTipo() == 'ENA') {
            return $this->redirect($this->generateUrl('ena'));
        } elseif ($entity->getTipo() == 'Envio') {
            return $this->redirect($this->generateUrl('envio'));
        } else {
            return $this->redirect($this->generateUrl('menaje'));
        }
    }
}
