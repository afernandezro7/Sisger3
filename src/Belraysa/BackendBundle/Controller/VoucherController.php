<?php

namespace Belraysa\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Belraysa\BackendBundle\Controller\FPDF\PDFController;
use Belraysa\BackendBundle\Entity\Voucher;
use Belraysa\BackendBundle\Form\VoucherType;
use Symfony\Component\Validator\Constraints\Date;
use HTML2PDF;

/**
 * Voucher controller.
 *
 */
class VoucherController extends Controller
{

    /**
     * Lists all Voucher entities.
     *
     */
    public function indexAction($ws)
    {
        $em = $this->getDoctrine()->getManager();

        $ws = $em->getRepository('BackendBundle:Workspace')->findOneByName($ws);
        $entities = $em->getRepository('BackendBundle:Voucher')->findVouchersByWorkspaceOnDescendantOrder($ws);


        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);

        return $this->render('BackendBundle:Voucher:index.html.twig', array(
            'entities' => $pagination,
            'ws' => $ws
        ));
    }


    public function indexByReplyAction(Request $request, $idReply)
    {
        $em = $this->getDoctrine()->getManager();
        $aavv = null;
        $c507 = null;
        $lbrs = null;
        $from = null;
        $to = null;
        $sisgerCode = null;
        $user = null;
        $proveedor = null;
        $estado = null;



        $mes = null;
        $anno = null;

        $vouchers = $em->getRepository('BackendBundle:Voucher')->findByReply($idReply);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $vouchers,
            $this->get('request')->query->get('page', 1),
            10);

        if ($from) {
            $from = date_format($from, 'd/m/Y');
        }
        if ($to) {
            $to = date_format($to, 'd/m/Y');
        }

        $workspace = $em->getRepository('BackendBundle:Reply')->find($idReply)->getWorkspace();
        $session = $request->getSession();
        $session->set('workspace', $workspace);

        return $this->render('BackendBundle:Voucher:filtered.html.twig', array(
            'entities' => $pagination,
            'aavv' => $aavv,
            'c507' => $c507,
            'lbrs' => $lbrs,
            'sisgerCode' => $sisgerCode,
            'from' => $from,
            'to' => $to,
            'user' => $user,
            'reply' => $idReply,
            'estado' => $estado,
            'proveedor' => $proveedor
        ));
    }

    public function exportByReplyAction($idReply)
    {
        $em = $this->getDoctrine()->getManager();
        $reply = $em->getRepository('BackendBundle:Reply')->find($idReply);
        $vouchersReport = $em->getRepository('BackendBundle:Voucher')->findByReply($reply);

        return $this->render('BackendBundle:Voucher:reportePorExp.html.twig', array(
            'vouchers' => $vouchersReport,
            'reply' => $reply,

        ));

    }

    public function getVouchersAction()
    {

        $vouchers = $this->getDoctrine()
            ->getRepository('BackendBundle:Voucher')
            ->createQueryBuilder('a')
            ->select('a')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);


        return new JsonResponse($vouchers);
    }

    /**
     * Creates a new Voucher entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $reply = $em->getRepository('BackendBundle:Reply')->findOneBySisgerCode($_POST['idReply']);
        $entity = new Voucher();

$firma = false;

        $provider = 'Belraysa Tours and Travel Group S.A.';
        $client = $reply->getLeader();
        $pax = $reply->getPax();
        $beginDate = $reply->getBeginDate();
        $finishDate = $reply->getFinishDate();
        $services = $reply->getServices();

        //    $now = new Date();

        $entity->setBeginDate($beginDate);
        //$entity->setClient($client);
        //    $entity->setCreationDate($now);
        $entity->setFinishDate($finishDate);
        $entity->setPax($pax);
        $entity->setProvider($provider);
        $entity->setServices($services);
        $entity->setReply($reply);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setServices($_POST['jodit']);
            $entity->setReply($reply);
            $entity->setUser($this->get('security.context')->getToken()->getUser());
            $entity->setActivo(true);
            if(array_key_exists('firmar', $_POST)){
    $firma = true;
    $entity->setFirmado(true);
}
            //print_r($entity->getClient()->getFirstName());
            //die;
            $em->persist($entity);
            $em->flush();

            $user = $this->get('security.context')->getToken()->getUser();
            $session = $request->getSession();
            $workspace = $user->getWorkspace();
            if ($session->has('workspace')) {
                $workspace = $em->getRepository('BackendBundle:Workspace')->find($session->get('workspace')->getId());

            }

            $entity->setWorkspace($workspace);
            $workspace->addVoucher($entity);

            $idSisger = str_pad($entity->getId(), 5, "0", STR_PAD_LEFT);
            $letraUser = $entity->getReply()->getUser()->getLetra();
            $idReply = str_pad($entity->getReply()->getId(), 3, "0", STR_PAD_LEFT);
            $entity->getCreationDate();
            $now = $entity->getCreationDate();
            $day = $now->format('d');
            $month = $now->format('m');
            $year = $now->format('Y');
            $year = $year[2] . $year[3];
            //$em = $this->getDoctrine()->getManager();
            $entity->setSisgerCode($letraUser . ' ' . $day . $month . $year . $idReply . $idSisger);
            $em->flush();

            $this->generateVoucherPDFAction($request, $entity->getId());

        } else {
            $flash = $this->get('session')->getFlashBag();
            $flash->set('danger', 'Ha ocurrido un error en el procesamiento de los datos enviados. Revise que ha completado correctamente todos los campos.');
            return $this->redirect($this->generateUrl('reply_show', array('id' => $reply->getId())));
        }

    }

    public function generateVoucherPDFAction(Request $request, $voucherId)
    {
        $em = $this->getDoctrine()->getManager();

        $voucher = $em->getRepository('BackendBundle:Voucher')->find($voucherId);
        $user = $this->get('security.context')->getToken()->getUser();
        if($voucher->isFirmado()){
            $firma = $this->container->getParameter('belraysa.route.firmas_users') . $voucher->getUser()->getFirma();
        }else{
            $firma = null;
        }
        $session = $request->getSession();
        $workspace = $user->getWorkspace();
        if ($session->has('workspace')) {
            $workspace = $em->getRepository('BackendBundle:Workspace')->find($session->get('workspace')->getId());

        }
       
        // get the HTML
        ob_start();
        include(dirname(__FILE__) . '/Reportes/Voucher.php');

        $content = ob_get_clean();
        //print_r($content);
        //die;
        // convert in PDF
        require_once(dirname(__FILE__) . '/html2pdf/vendor/autoload.php');
        try {
            $html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', array(5, 5, 5, 8));
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
            $html2pdf->Output($voucher->getSisgerCode() . '.pdf');
        } catch (HTML2PDF_exception $e) {
            echo $e;
            exit;
        }
    }

    public function generateReportAction()
    {
        if (array_key_exists('daterange', $_POST)) {
            $range = $_POST['daterange'];

            $arrayDates = explode(" - ", $range);

            $date1 = date_create_from_format('d/m/Y', $arrayDates[0]);
            $date2 = date_create_from_format('d/m/Y', $arrayDates[1]);

            $from = new \DateTime($date1->format("Y-m-d") . " 00:00:00");
            $to = new \DateTime($date2->format("Y-m-d") . " 23:59:59");

        } else {
            $now = new \DateTime();

            $year = $now->format('Y');
            $date1 = date_create_from_format('Y-m-d', $year . '-01-01');
            $date2 = $now;

            $from = new \DateTime($date1->format("Y-m-d") . " 00:00:00");
            $to = new \DateTime($date2->format("Y-m-d") . " 23:59:59");
        }

        $em = $this->getDoctrine()->getManager();
        $fromString = date_format($from, 'd/m/Y');
        $toString = date_format($to, 'd/m/Y');
        $vouchersReport = $em->getRepository('BackendBundle:Voucher')->findVouchersByRange($from, $to);
        $totalPax = $em->getRepository('BackendBundle:Voucher')->findTotalPaxVouchersByRange($from, $to);
        //    print_r($totalPax);
        //    die;
        /*   ob_start();
           include(dirname(__FILE__) . '/Reportes/VouchersReport.php');

           $content = ob_get_clean();
           //print_r($content);
           //die;
           // convert in PDF
           require_once(dirname(__FILE__) . '/html2pdf/vendor/autoload.php');
           try {
               $html2pdf = new HTML2PDF('L', 'A4', 'fr', true, 'UTF-8', array(5, 5, 5, 8));
               $html2pdf->pdf->SetDisplayMode('fullpage');
               $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
               $html2pdf->Output($fromString . ' - ' . $toString . ' - ' . 'Vouchers' . '.pdf');
           } catch (HTML2PDF_exception $e) {
               echo $e;
               exit;
           }
   */
        return $this->render('BackendBundle:Voucher:reporte.html.twig', array(
            'vouchers' => $vouchersReport,
            'from' => $fromString,
            'to' => $toString,

        ));

    }

    public function generateReportByWsAction($ws)
    {
        if (array_key_exists('daterange', $_POST)) {
            $range = $_POST['daterange'];

            $arrayDates = explode(" - ", $range);

            $date1 = date_create_from_format('d/m/Y', $arrayDates[0]);
            $date2 = date_create_from_format('d/m/Y', $arrayDates[1]);

            $from = new \DateTime($date1->format("Y-m-d") . " 00:00:00");
            $to = new \DateTime($date2->format("Y-m-d") . " 23:59:59");

        } else {
            $now = new \DateTime();

            $year = $now->format('Y');
            $date1 = date_create_from_format('Y-m-d', $year . '-01-01');
            $date2 = $now;

            $from = new \DateTime($date1->format("Y-m-d") . " 00:00:00");
            $to = new \DateTime($date2->format("Y-m-d") . " 23:59:59");
        }

        $em = $this->getDoctrine()->getManager();
        $fromString = date_format($from, 'd/m/Y');
        $toString = date_format($to, 'd/m/Y');
        $vouchersReport = $em->getRepository('BackendBundle:Voucher')->findVouchersByRangeAndWs($from, $to, $ws);
        $totalPax = $em->getRepository('BackendBundle:Voucher')->findTotalPaxVouchersByRangeAndWs($from, $to, $ws);
        //    print_r($totalPax);
        //    die;
        /*   ob_start();
           include(dirname(__FILE__) . '/Reportes/VouchersReport.php');

           $content = ob_get_clean();
           //print_r($content);
           //die;
           // convert in PDF
           require_once(dirname(__FILE__) . '/html2pdf/vendor/autoload.php');
           try {
               $html2pdf = new HTML2PDF('L', 'A4', 'fr', true, 'UTF-8', array(5, 5, 5, 8));
               $html2pdf->pdf->SetDisplayMode('fullpage');
               $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
               $html2pdf->Output($fromString . ' - ' . $toString . ' - ' . 'Vouchers' . '.pdf');
           } catch (HTML2PDF_exception $e) {
               echo $e;
               exit;
           }
   */
        return $this->render('BackendBundle:Voucher:reporte.html.twig', array(
            'vouchers' => $vouchersReport,
            'from' => $fromString,
            'to' => $toString,

        ));

    }


    public function generateVoucherPDF1Action($voucherId)
    {
        $em = $this->getDoctrine()->getManager();
        $voucher = $em->getRepository('BackendBundle:Voucher')->find($voucherId);
        $image = $this->container->getParameter('belraysa.route.logos') . "logo-5678c0e74c6f5-foto1.jpg";
        $data = PDFController::ExportVoucherAsPDFAction($voucher, $image);
        return new Response($data, 200, array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="voucher_' . $voucher->getId() . '.pdf"'));
    }

    /**
     * Creates a form to create a Voucher entity.
     *
     * @param Voucher $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Voucher $entity)
    {
        $form = $this->createForm(new VoucherType(), $entity, array(
            'action' => $this->generateUrl('voucher_create'),
            'method' => 'POST',
        ));

        //  $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Voucher entity.
     *
     */
    public function newAction($idReply)
    {
        $entity = new Voucher();

        $em = $this->getDoctrine()->getManager();
        $reply = $em->getRepository('BackendBundle:Reply')->find($idReply);

        $provider = 'Belraysa Tours and Travel Group S.A.';
        $client = $reply->getLeader();
        $pax = $reply->getPax();
        $beginDate = $reply->getBeginDate();
        $finishDate = $reply->getFinishDate();
        $services = $reply->getServices();

        //    $now = new Date();

        $entity->setBeginDate($beginDate);
        //$entity->setClient($client);
        //    $entity->setCreationDate($now);
        $entity->setFinishDate($finishDate);
        $entity->setPax($pax);
        $entity->setProvider($provider);
        $entity->setServices($services);
        $entity->setReply($reply);


        $form = $this->createCreateForm($entity);

        return $this->render('BackendBundle:Voucher:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Voucher entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Voucher')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Voucher entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Voucher:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Voucher entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Voucher')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Voucher entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Voucher:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Voucher entity.
     *
     * @param Voucher $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Voucher $entity)
    {
        $form = $this->createForm(new VoucherType(), $entity, array(
            'action' => $this->generateUrl('voucher_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //  $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Voucher entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Voucher')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Voucher entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('voucher_edit', array('id' => $id)));
        }

        return $this->render('BackendBundle:Voucher:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Voucher entity.
     *
     */
    public function cancelAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Voucher')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Voucher entity.');
        }

        $entity->setActivo(false);
        $em->flush();
        $workspace = $entity->getWorkspace();
        $session = $request->getSession();
        $session->set('workspace', $workspace);

        return $this->redirect($this->generateUrl('voucher', array('ws' => $entity->getWorkspace()->getName())));
    }

    public function activateAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Voucher')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Voucher entity.');
        }

        $entity->setActivo(true);
        $em->flush();

        $workspace = $entity->getWorkspace();
        $session = $request->getSession();
        $session->set('workspace', $workspace);
        return $this->redirect($this->generateUrl('voucher', array('ws' => $entity->getWorkspace()->getName())));
    }

    /**
     * Creates a form to delete a Voucher entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('voucher_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    public function filtrarPorWSAction($ws)
    {
        $em = $this->getDoctrine()->getManager();

        $aavv = null;
        $c507 = null;
        $lbrs = null;

        $from = null;
        $to = null;

        $sisgerCode = null;
        $user = null;

        $reply = null;
        $proveedor = null;

        $estado = null;

        if (array_key_exists('voucher_estado', $_GET)) {
            if ($_GET['voucher_estado'] != '') {
                $estado = $_GET['voucher_estado'];
            }
        }


        $mes = null;
        $anno = null;


        if ($ws == 'AAVV') {
            $aavv = $em->getRepository('BackendBundle:Workspace')->findOneByName($ws);
        }
        if ($ws == 'C507') {
            $c507 = $em->getRepository('BackendBundle:Workspace')->findOneByName($ws);
        }
        if ($ws == 'L-BRS') {
            $lbrs = $em->getRepository('BackendBundle:Workspace')->findOneByName($ws);
        }

        if (array_key_exists('voucher_sisgerCode', $_GET)) {
            if ($_GET['voucher_sisgerCode'] != '') {
                $sisgerCode = $_GET['voucher_sisgerCode'];
            }
        }

        if (array_key_exists('voucher_from', $_GET)) {
            if ($_GET['voucher_from'] != '') {
                $date1 = date_create_from_format('d/m/Y', $_GET['voucher_from']);
                $from = new \DateTime($date1->format("Y-m-d") . " 00:00:00");
            }
        }

        if (array_key_exists('voucher_to', $_GET)) {
            if ($_GET['voucher_to'] != '') {
                $date2 = date_create_from_format('d/m/Y', $_GET['voucher_to']);
                $to = new \DateTime($date2->format("Y-m-d") . " 00:00:00");
            }
        }

        if (array_key_exists('voucher_user', $_GET)) {
            if ($_GET['voucher_user'] != '') {
                $user = $_GET['voucher_user'];
            }
        }


        if (array_key_exists('voucher_reply', $_GET)) {
            if ($_GET['voucher_reply'] != '') {
                $reply = $_GET['voucher_reply'];
            }
        }

        if (array_key_exists('voucher_proveedor', $_GET)) {
            if ($_GET['voucher_proveedor'] != '') {
                $proveedor = $_GET['voucher_proveedor'];
            }
        }

        $vouchers = $em->getRepository('BackendBundle:Voucher')->findBusquedaAvanzada(true, $aavv, $c507, $lbrs, $sisgerCode, $from, $to, $user, $proveedor, $reply, $estado);

        if (array_key_exists('buscar', $_GET)) {
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $vouchers,
                $this->get('request')->query->get('page', 1),
                10);

            if ($from) {
                $from = date_format($from, 'd/m/Y');
            }
            if ($to) {
                $to = date_format($to, 'd/m/Y');
            }


            return $this->render('BackendBundle:Voucher:filtered.html.twig', array(
                'entities' => $pagination,
                'aavv' => $aavv,
                'c507' => $c507,
                'lbrs' => $lbrs,
                'sisgerCode' => $sisgerCode,
                'from' => $from,
                'to' => $to,
                'user' => $user,
                'proveedor' => $proveedor,
                'reply' => $reply,
                'estado' => $estado
            ));
        } else {
            if ($from) {
                $from = date_format($from, 'd/m/Y');
            }
            if ($to) {
                $to = date_format($to, 'd/m/Y');
            }
            $range = '';
            if ($from && $to) {
                $range = $range . $from . ' - ' . $to;
            } elseif ($from) {
                $range = 'Desde ' . $from;
            } elseif ($to) {
                $range = 'Hasta ' . $to;
            }
            return $this->render('BackendBundle:Voucher:reporte.html.twig', array(
                'vouchers' => $vouchers,
                'range' => $range

            ));
        }
    }

    public function filtrarTreePorWSAction($month, $year, $usuario, $ws)
    {
        $em = $this->getDoctrine()->getManager();

        $aavv = null;
        $c507 = null;
        $lbrs = null;

        $from = null;
        $to = null;

        $sisgerCode = null;
        $user = null;

        $reply = null;
        $proveedor = null;

        $estado = null;

        $mes = null;
        $anno = null;


        if ($ws == 'AAVV') {
            $aavv = $em->getRepository('BackendBundle:Workspace')->findOneByName($ws);
        }
        if ($ws == 'C507') {
            $c507 = $em->getRepository('BackendBundle:Workspace')->findOneByName($ws);
        }
        if ($ws == 'L-BRS') {
            $lbrs = $em->getRepository('BackendBundle:Workspace')->findOneByName($ws);
        }


        if ($year != 'none') {
            $anno = $year;

            $date1 = date_create_from_format('d/m/Y', '1/1/' . $anno);
            $date2 = date_create_from_format('d/m/Y', '31/12/' . $anno);

            $from = new \DateTime($date1->format("Y-m-d") . " 00:00:00");
            $to = new \DateTime($date2->format("Y-m-d") . " 23:59:59");

            if ($month != 'none') {
                $mes = $month;

                $date1 = date_create_from_format('d/m/Y', '1/' . $mes . '/' . $anno);
                $date_temp = date_create_from_format('d/m/Y', '1/' . $mes . '/' . $anno);

                $date2 = $date_temp->modify('last day of this month');

                $from = new \DateTime($date1->format("Y-m-d") . " 00:00:00");
                $to = new \DateTime($date2->format("Y-m-d") . " 23:59:59");
            }

        }

        if ($usuario != 'none') {
            $user = $usuario;
        }


        $vouchers = $em->getRepository('BackendBundle:Voucher')->findBusquedaAvanzada(true, $aavv, $c507, $lbrs, $sisgerCode, $from, $to, $user, $proveedor, $reply, $estado);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $vouchers,
            $this->get('request')->query->get('page', 1),
            10);

        if ($from) {
            $from = date_format($from, 'd/m/Y');
        }
        if ($to) {
            $to = date_format($to, 'd/m/Y');
        }

        return $this->render('BackendBundle:Voucher:filtered.html.twig', array(
            'entities' => $pagination,
            'aavv' => $aavv,
            'c507' => $c507,
            'lbrs' => $lbrs,
            'sisgerCode' => $sisgerCode,
            'from' => $from,
            'to' => $to,
            'user' => $user,
            'proveedor' => $proveedor,
            'reply' => $reply,
            'estado' => $estado
        ));
    }
}
