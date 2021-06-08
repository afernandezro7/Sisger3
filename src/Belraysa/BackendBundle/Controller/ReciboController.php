<?php



namespace Belraysa\BackendBundle\Controller;



use Belraysa\BackendBundle\Entity\Ajuste;

use Belraysa\BackendBundle\Entity\BankingEntry;



use Belraysa\BackendBundle\Entity\Costo;

use Belraysa\BackendBundle\Entity\CostoRecurrente;

use Belraysa\BackendBundle\Entity\Gasto;

use Belraysa\BackendBundle\Entity\IdEgreso;

use Belraysa\BackendBundle\Entity\IdIngreso;

use Belraysa\BackendBundle\Entity\Ingreso;

use Belraysa\BackendBundle\Form\CostType;

use Belraysa\BackendBundle\Form\ExpendationType;

use Belraysa\BackendBundle\Form\ReciboEditType;

use Belraysa\BackendBundle\Form\IncomeType;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Belraysa\BackendBundle\Controller\FPDF\PDFController;

use Belraysa\BackendBundle\Entity\Recibo;

use HTML2PDF;



/**

 * Recibo controller.

 *

 */

class ReciboController extends Controller

{

    public function indexAction($ws)

    {

        $em = $this->getDoctrine()->getManager();

        $ws = $em->getRepository('BackendBundle:Workspace')->findOneByName($ws);

        $user = $this->get('security.context')->getToken()->getUser();

        if ($user->getRole()->getName() != 'ROLE_OPERATOR') {

            $entities = $em->getRepository('BackendBundle:Recibo')->findRecibosByWorkspaceOnDescendantOrder($ws);

        } else {

            $entities = $em->getRepository('BackendBundle:Recibo')->findIngresosByWorkspaceOnDescendantOrder($ws);

        }

        /*

                foreach ($entities as $recibo) {

                    if (!$recibo->getCreacion()) {

                        $recibo->setCreacion($recibo->getFecha());

                    }

                }

                $em->flush();*/



        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10
        );





        return $this->render('BackendBundle:Recibo:index.html.twig', array(

            'entities' => $pagination,

            'ws' => $ws

        ));

    }



    public function egresosAction($ws)

    {

        $em = $this->getDoctrine()->getManager();

        $ws = $em->getRepository('BackendBundle:Workspace')->findOneByName($ws);

        $user = $this->get('security.context')->getToken()->getUser();



        $entities = $em->getRepository('BackendBundle:Recibo')->findEgresosByWorkspaceOnDescendantOrder($ws);

        $metodos = $em->getRepository('BackendBundle:PaymentMethod')->findAll();





        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(

            $entities,

            $this->get('request')->query->get('page', 1),

            10);





        return $this->render('BackendBundle:Recibo:egresos.html.twig', array(

            'entities' => $pagination,

            'ws' => $ws,

            'metodosPago' => $metodos

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

        $tipo = null;

        $importe_desde = null;

        $importe_hasta = null;

        $cuenta = null;

        $concepto = null;

        $recibide = null;

        $estado = null;

        $mes = null;

        $anno = null;



        $user = $this->get('security.context')->getToken()->getUser();

        if ($user->getRole()->getName() != 'ROLE_OPERATOR') {

            $recibos = $em->getRepository('BackendBundle:Recibo')->findByExpediente($idReply);

        } else {

            $recibos = $em->getRepository('BackendBundle:Recibo')->findBy(array('expediente' => $idReply, 'tipo' => 'Ingreso'));

        }





        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(

            $recibos,

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

        if ($user) {

            $user = $user->getId();

        }

        return $this->render('BackendBundle:Recibo:filtered.html.twig', array(

            'entities' => $pagination,

            'aavv' => $aavv,

            'c507' => $c507,

            'lbrs' => $lbrs,

            'sisgerCode' => $sisgerCode,

            'from' => $from,

            'to' => $to,

            'user' => $user,

            'tipo' => $tipo,

            'importe_desde' => $importe_desde,

            'importe_hasta' => $importe_hasta,

            'cuenta' => $cuenta,

            'concepto' => $concepto,

            'recibi_de' => $recibide,

            'reply' => $idReply,

            'estado' => $estado

        ));

    }



    public function indexAllAction()

    {

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:Recibo')->findOrdenados();



        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(

            $entities,

            $this->get('request')->query->get('page', 1),

            10);

        /*      foreach ($entities1 as $entity) {

                  if ($entity->getTipo() == 'Ingreso') {

                      $idIngreso = new IdIngreso();

                      $em->persist($idIngreso);

                      $em->flush();

                      $entity->setIdIngreso($idIngreso);

                      $entity->setSisgerCode('I-' . $idIngreso->getId());

                      $em->flush();

                  } else {

                      $idEgreso = new IdEgreso();

                      $em->persist($idEgreso);

                      $em->flush();

                      $entity->setIdEgreso($idEgreso);

                      $entity->setSisgerCode('E-' . $idEgreso->getId());

                      $em->flush();

                  }

              }

      */

        return $this->render('BackendBundle:Recibo:indexAll.html.twig', array(

            'entities' => $pagination,

            'servicios' => $em->getRepository('BackendBundle:Service')->findAll(),

            'conceptosGasto' => $em->getRepository('BackendBundle:ConceptoGasto')->findAll(),

            'conceptosCosto' => $em->getRepository('BackendBundle:ConceptoCosto')->findAll(),

            'inventarios' => $em->getRepository('BackendBundle:Inventario')->findAll(),

            'metodosPago' => $em->getRepository('BackendBundle:PaymentMethod')->findAll(),

            'replies' => $em->getRepository('BackendBundle:Reply')->findAll(),

        ));

    }



    public function exportByReplyAction($idReply)

    {

        $em = $this->getDoctrine()->getManager();

        $reply = $em->getRepository('BackendBundle:Reply')->find($idReply);

        $recibosReport = $em->getRepository('BackendBundle:Recibo')->findByExpediente($reply);

        $totalImporte = $em->getRepository('BackendBundle:Recibo')->findTotalImporteRecibosByReply($idReply);



        return $this->render('BackendBundle:Recibo:reportePorExp.html.twig', array(

            'recibos' => $recibosReport,

            'total' => $totalImporte[0][1],

            'reply' => $reply

        ));



    }



    public function getRecibosAction()

    {



        $recibos = $this->getDoctrine()

            ->getRepository('BackendBundle:Recibo')

            ->createQueryBuilder('a')

            ->select('a')

            ->getQuery()

            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);





        return new JsonResponse($recibos);

    }



    public function generateReciboPDFAction(Request $request, $reciboId)

    {



        $em = $this->getDoctrine()->getManager();

        $recibo = $em->getRepository('BackendBundle:Recibo')->find($reciboId);



        if (!$recibo->getRefExpediente() && $recibo->getExpediente() != null) {

            $recibo->setRefExpediente($recibo->getExpediente()->getSisgerCode());

        }



        $user = $this->get('security.context')->getToken()->getUser();

        $session = $request->getSession();

        $workspace = $user->getWorkspace();

        if ($session->has('workspace')) {

            $workspace = $em->getRepository('BackendBundle:Workspace')->find($session->get('workspace')->getId());



        }

        $paymentMethods = $em->getRepository('BackendBundle:PaymentMethod')->findAll();



        if ($recibo->isFirmado()) {

            $firma = $this->container->getParameter('belraysa.route.firmas_users') . $recibo->getUsuario()->getFirma();

            $sello = $this->container->getParameter('belraysa.route.firmas_users') . 'sello.jpg';

        } else {

            $firma = null;

            $sello = null;

        }



        // get the HTML

        ob_start();

        include(dirname(__FILE__) . '/Reportes/Recibo.php');





        $content = ob_get_clean();



        // convert in PDF

        require_once(dirname(__FILE__) . '/html2pdf/vendor/autoload.php');

        try {

            $html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', array(5, 5, 5, 8));

            $html2pdf->pdf->SetDisplayMode('fullpage');

            $html2pdf->writeHTML($content, isset($_GET['vuehtml']));

            $html2pdf->Output($recibo->getSisgerCode() . '.pdf');

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

        $recibosReport = $em->getRepository('BackendBundle:Recibo')->findRecibosByRange($from, $to);

        $totalImporte = $em->getRepository('BackendBundle:Recibo')->findTotalImporteRecibosByRange($from, $to);





        if (array_key_exists('pdf', $_POST)) {

            return $this->render('BackendBundle:Recibo:reporte.html.twig', array(

                'recibos' => $recibosReport,

                'total' => $totalImporte[0][1],

                'from' => $fromString,

                'to' => $toString,

                'type' => 'Pago/Costo',

            ));

        } else {

            $flash = $this->get('session')->getFlashBag();

            $flash->set('success', 'Filtro aplicado. Los recibos que se muestran están comprendidos en el rango de fechas especificado.');



            $paginator = $this->get('knp_paginator');

            $pagination = $paginator->paginate(

                $recibosReport,

                $this->get('request')->query->get('page', 1),

                sizeof($recibosReport));



            return $this->render('BackendBundle:Recibo:index.html.twig', array(

                'entities' => $pagination

            ));

        }





    }



    public function generateExpendationsReportAction()

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

        $recibosReport = $em->getRepository('BackendBundle:Recibo')->findExpendationsByRange($from, $to);

        $totalImporte = $em->getRepository('BackendBundle:Recibo')->findTotalImporteExpendationsByRange($from, $to);



        return $this->render('BackendBundle:Recibo:reporte.html.twig', array(

            'recibos' => $recibosReport,

            'total' => $totalImporte[0][1],

            'from' => $fromString,

            'to' => $toString,

            'type' => 'Gasto',

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

        $ws = $em->getRepository('BackendBundle:Workspace')->findOneByName($ws);

        $recibosReport = $em->getRepository('BackendBundle:Recibo')->findRecibosByRangeAndWs($from, $to, $ws);



        $totalImporte = $em->getRepository('BackendBundle:Recibo')->findTotalImporteRecibosByRangeAndWs($from, $to, $ws);





        if (sizeof($recibosReport) == 0) {

            $paginas = 1;

        } else {

            $paginas = sizeof($recibosReport);

        }

        if (array_key_exists('pdf', $_POST)) {

            return $this->render('BackendBundle:Recibo:reporte.html.twig', array(

                'recibos' => $recibosReport,

                'total' => $totalImporte[0][1],

                'from' => $fromString,

                'to' => $toString,



            ));

        } else {

            $flash = $this->get('session')->getFlashBag();

            $flash->set('success', 'Filtro aplicado. Los recibos que se muestran están comprendidos en el rango de fechas especificado.');



            $paginator = $this->get('knp_paginator');

            $pagination = $paginator->paginate(

                $recibosReport,

                $this->get('request')->query->get('page', 1),

                $paginas

            );



            return $this->render('BackendBundle:Recibo:index.html.twig', array(

                'entities' => $pagination

            ));

        }





    }



    public function generateReciboPDF1Action($reciboId)

    {

        $em = $this->getDoctrine()->getManager();

        $recibo = $em->getRepository('BackendBundle:Recibo')->find($reciboId);

        $image = $this->container->getParameter('belraysa.route.logos') . "logo-5678c0e74c6f5-foto1.jpg";

        $data = PDFController::ExportReciboAsPDFAction($recibo, $image);

        return new Response($data, 200, array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="recipe_' . $recibo->getId() . '.pdf"'));

    }



    private function createCreateForm(Recibo $entity, $type)

    {

        if ($type == 'income') {

            $form = $this->createForm(new IncomeType(), $entity, array(

                'action' => $this->generateUrl('recibo_create_income'),

                'method' => 'POST',

            ));

        } elseif ($type == 'expendation') {

            $form = $this->createForm(new ExpendationType(), $entity, array(

                'action' => $this->generateUrl('recibo_create_expendation'),

                'method' => 'POST',

            ));

        } else {

            $form = $this->createForm(new CostType(), $entity, array(

                'action' => $this->generateUrl('recibo_create_cost'),

                'method' => 'POST',

            ));

        }



        //    $form->add('submit', 'submit', array('label' => 'Create'));



        return $form;

    }



    public function newIncomeAction(Request $request, $idReply)

    {

        $entity = new Recibo();



        $em = $this->getDoctrine()->getManager();

        $reply = $em->getRepository('BackendBundle:Reply')->find($idReply);



        $entity->setReply($reply);



        $form = $this->createCreateForm($entity, 'income');

        $user = $this->get('security.context')->getToken()->getUser();

        $session = $request->getSession();

        $workspace = $user->getWorkspace();

        if ($session->has('workspace')) {

            $workspace = $em->getRepository('BackendBundle:Workspace')->find($session->get('workspace')->getId());



        }





        return $this->render('BackendBundle:Recibo:newIncome.html.twig', array(

            'entity' => $entity,

            'form' => $form->createView(),

            'services' => $em->getRepository('BackendBundle:Service')->findAll(),

            'workspace' => $workspace

        ));

    }



    public function newExpendationAction(Request $request)

    {

        $entity = new Recibo();

        $em = $this->getDoctrine()->getManager();

        $form = $this->createCreateForm($entity, 'expendation');



        return $this->render('BackendBundle:Recibo:newExpendation.html.twig', array(

            'entity' => $entity,

            'form' => $form->createView(),

            'services' => $em->getRepository('BackendBundle:Service')->findAll(),

            'bankings' => $em->getRepository('BackendBundle:Banking')->findAll()

        ));

    }



    public function newCostAction(Request $request, $idReply)

    {

        $entity = new Recibo();



        $em = $this->getDoctrine()->getManager();

        $reply = $em->getRepository('BackendBundle:Reply')->find($idReply);



        $entity->setReply($reply);



        $form = $this->createCreateForm($entity, 'cost');

        $user = $this->get('security.context')->getToken()->getUser();

        $session = $request->getSession();

        $workspace = $user->getWorkspace();

        if ($session->has('workspace')) {

            $workspace = $em->getRepository('BackendBundle:Workspace')->find($session->get('workspace')->getId());

        }



        return $this->render('BackendBundle:Recibo:newCost.html.twig', array(

            'entity' => $entity,

            'form' => $form->createView(),

            'services' => $em->getRepository('BackendBundle:Service')->findAll(),

            'workspace' => $workspace

        ));

    }



    public function showAction($id)

    {

        $em = $this->getDoctrine()->getManager();



        $entity = $em->getRepository('BackendBundle:Recibo')->find($id);



        if (!$entity) {

            throw $this->createNotFoundException('Unable to find Recibo entity.');

        }



        return $this->render('BackendBundle:Recibo:show.html.twig', array(

            'entity' => $entity,

        ));

    }



    public function editAction($id)

    {

        $em = $this->getDoctrine()->getManager();



        $entity = $em->getRepository('BackendBundle:Recibo')->find($id);



        if (!$entity) {

            throw $this->createNotFoundException('Unable to find Recibo entity.');

        }



        $editForm = $this->createEditForm($entity);



        $user = $this->get('security.context')->getToken()->getUser();

        $isSuperAdmin = false;

        if ($user->getRole()->getName() == "ROLE_SUPER_ADMIN")

            $isSuperAdmin = true;





        return $this->render('BackendBundle:Recibo:edit.html.twig', array(

            'entity' => $entity,

            'edit_form' => $editForm->createView(),

            'servicios' => $em->getRepository('BackendBundle:Service')->findAll(),

            'conceptosGasto' => $em->getRepository('BackendBundle:ConceptoGasto')->findAll(),

            'conceptosCosto' => $em->getRepository('BackendBundle:ConceptoCosto')->findAll(),

            'inventarios' => $em->getRepository('BackendBundle:Inventario')->findAll(),

            'isSuperAdmin' => $isSuperAdmin,

            'cuentas' => $em->getRepository('BackendBundle:Banking')->findAll()

        ));

    }



    private function createEditForm(Recibo $entity)

    {

        $form = $this->createForm(new ReciboEditType(), $entity, array(

            'action' => $this->generateUrl('recibo_update', array('id' => $entity->getId())),

            'method' => 'PUT',

        ));



        $form->add('detalles');

        $form->add('suma');



        // $form->add('submit', 'submit', array('label' => 'Update'));



        return $form;

    }



    public function updateAction(Request $request, $id)

    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Recibo')->find($id);

        //$ef = $em->getRepository('BackendBundle:Banking')->findAll();



        if (!$entity) {

            throw $this->createNotFoundException('Unable to find Recibo entity.');

        }



        if ($_POST['belraysa_backendbundle_recibo']['expediente'] != "") {

            $entity->setExpediente($em->getRepository('BackendBundle:Reply')->find($_POST['belraysa_backendbundle_recibo']['expediente']));

        }



        if ($_POST['belraysa_backendbundle_recibo']['workspace'] != "") {

            $entity->setWorkspace($em->getRepository('BackendBundle:Workspace')->find($_POST['belraysa_backendbundle_recibo']['workspace']));

        }



        if ($_POST['belraysa_backendbundle_recibo']['detalles'] != "") {

            $entity->setDetalles($_POST['belraysa_backendbundle_recibo']['detalles']);

        }



        if ($entity->getTipo() == 'Ingreso') {

            foreach ($entity->getServicios() as $s) {

                $entity->removeServicio($s);

            }

        } elseif ($entity->getTipo() == 'Gasto') {

            foreach ($entity->getConceptosGasto() as $s) {

                $entity->removeConcepto($s);

            }

        }elseif ($entity->getTipo() == 'Costo') {

            foreach ($entity->getConceptosCosto() as $s) {

                $entity->removeConcepto($s);

            }

        }else {

            foreach ($entity->getInventarios() as $s) {

                $entity->removeInventario($s);

            }

        }



        foreach ($_POST['concepto'] as $concepto_id) {

            if ($entity->getTipo() == 'Ingreso') {

                $concepto = $em->getRepository('BackendBundle:Service')->find($concepto_id);

                $entity->addServicio($concepto);

            } elseif ($entity->getTipo() == 'Gasto') {

                $concepto = $em->getRepository('BackendBundle:ConceptoGasto')->find($concepto_id);

                $entity->addConcepto($concepto);

            }elseif ($entity->getTipo() == 'Costo') {

                $concepto = $em->getRepository('BackendBundle:ConceptoCosto')->find($concepto_id);

                $entity->addConcepto($concepto);

            }else {

                $concepto = $em->getRepository('BackendBundle:Inventario')->find($concepto_id);

                $entity->addInventario($concepto);

            }

        }



        if ($entity->getExpediente()) {

            $entity->setWorkspace($entity->getExpediente()->getWorkspace());

        }

        $em->flush();



        if ($entity->getTipo() == 'Ingreso' && $entity->getCuentaXCobrar() == 1 && $_POST['belraysa_backendbundle_recibo']['importe'] != 0) {

            $importe = $_POST['belraysa_backendbundle_recibo']['importe'];



            if($importe != $entity->getSaldoPendiente()){

                $ingreso = clone $entity;

                $ingreso->setCuentaXCobrar(0);

                $ingreso->setImporte($importe);

                $ingreso->setSaldoAnterior($entity->getSaldoPendiente());

                $ingreso->setSaldoPendiente($entity->getSaldoPendiente() - $importe);

                $ingreso->setFecha(new \DateTime());

                $ingreso->setCreacion(new \DateTime());

                $ingreso->setFechaLimite(null);

                if ($_POST['belraysa_backendbundle_recibo']['suma'] != "")

                    $ingreso->setSuma($_POST['belraysa_backendbundle_recibo']['suma']);



                $idIngreso = new IdIngreso();

                $em->persist($idIngreso);

                $em->flush();

                $ingreso->setIdIngreso($idIngreso);

                $ingreso->setSisgerCode('I-' . $idIngreso->getId());

                $em->persist($ingreso);

                $em->flush();



                $ingreso->setServicios($entity->getServicios());



                $bankingEntry = $entity->getEntrada();

                $entry = clone $bankingEntry;

                $entry->setCredit($ingreso->getImporte());

                $entry->setRecibo($ingreso);

                $entry->setDate(new \DateTime());



                $banking = $em->getRepository('BackendBundle:Banking')->find($_POST['cuenta']);

                $banking->setBalance($banking->getBalance() + $importe);

                $entry->setBalance($banking->getBalance());



                $em->persist($entry);

                $banking->addEntry($entry);



                $ingreso->setEntrada($entry);

                $em->flush();



                $entity->setSaldoPendiente($entity->getSaldoPendiente() - $importe);

            }

            else{

                $entity->setCuentaXCobrar(0);

                $entity->setImporte($importe);

                $entity->setSaldoPendiente(0);

                $entity->setFechaLimite(null);

                $entity->setFecha(new \DateTime());

                if($_POST['belraysa_backendbundle_recibo']['suma'] != "")

                    $entity->setSuma($_POST['belraysa_backendbundle_recibo']['suma']);



                $bankingEntry = $entity->getEntrada();

                $bankingEntry->setDate(new \DateTime());

                $bankingEntry->setCredit($importe);

                $banking = $em->getRepository('BackendBundle:Banking')->find($_POST['cuenta']);

                $banking->setBalance($banking->getBalance() + $importe);

                $bankingEntry->setBalance($banking->getBalance());

                $banking->addEntry($bankingEntry);

            }

            $em->flush();

        }



        return $this->redirect($this->generateUrl('recibo_all'));

    }



    public function cancelAction(Request $request)

    {



        $id = $_GET['id'];

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Recibo')->find($id);





        if (!$entity) {

            throw $this->createNotFoundException('Unable to find Recibo entity.');

        }

        $entity->setMotivoCancelacion($_GET['motivo']);

        $entity->setActivo(false);

        if ($entity->getEntrada()) {

            //cancelando la entrada

            $entity->getEntrada()->setActivo(false);

            //creando el ajuste

            $ajuste = new Ajuste();

            $banking = $entity->getEntrada()->getBanking();

            $ajuste->setBanking($banking);

            $ajuste->setFecha(new \DateTime());

            $importe = 0 - $entity->getImporte();

            $ajuste->setImporte($importe);



            $ajuste->setConcepto('Ajuste de la cuenta ' . $banking->getName() . ' tras la cancelación del recibo ' . $entity->getSisgerCode());

            $em->persist($ajuste);

            $em->flush();



            $idSisger = str_pad($ajuste->getId(), 4, "0", STR_PAD_LEFT);

            $idBanking = str_pad($banking->getId(), 4, "0", STR_PAD_LEFT);



            $ajuste->setSisgerCode('A-' . $idBanking . '-' . $idSisger);



            $em->flush();



            $banking->setBalance($banking->getBalance() + $ajuste->getImporte());



            $entry = new BankingEntry();

            $entry->setDate($ajuste->getFecha());

            if ($importe < 0) {

                $entry->setCredit(0);

                $entry->setDebit($importe);

            } else {

                $entry->setCredit($importe);

                $entry->setDebit(0);

            }

            $entry->setBalance($banking->getBalance());

            $entry->setDetails($ajuste->getConcepto());

            $entry->setAjuste($ajuste);

            $entry->setActivo(true);

            $em->persist($entry);

            $em->flush();

            $ajuste->setEntrada($entry);

            $banking->addEntry($entry);

            $em->flush();

        }

        /* $importe = $entity->getImporte();

         if ($entity->getTipo() == 'Ingreso') {

             $entity->getEntrada()->getBanking()->setBalance($entity->getEntrada()->getBanking()->getBalance() - $importe);

         } else {

             $entity->getEntrada()->getBanking()->setBalance($entity->getEntrada()->getBanking()->getBalance() + $importe);

         }*/

        $em->flush();



        $workspace = $entity->getWorkspace();

        $session = $request->getSession();

        $session->set('workspace', $workspace);



        //notificar a los superadmins

        $template = 'recibo_cancelado';



        //Get data from the submitted form



        $mail_params = array(

            'recibo' => $entity



        );

        //grab the addresses defined in parameters.yml

        foreach ($em->getRepository('BackendBundle:User')->findByRole($em->getRepository('BackendBundle:Role')->findOneByName('ROLE_SUPER_ADMIN')) as $super) {

            if ($super->getEmail()) {

                $to[] = $super->getEmail();

            }

        }





        $from = 'notificaciones@sisger.center';

        $fromName = 'Sisger III';



        //use the MailManager service to send emails

        $message = $this->container->get('mail_manager');



        $message->sendEmail($template, $mail_params, $to, array(), $from, $fromName);



        $flash = $this->get('session')->getFlashBag();

        $flash->set('success', 'Recibo cancelado satisfactoriamente.');



        return $this->redirect($this->generateUrl('recibo', array('ws' => $entity->getWorkspace()->getName())));



    }



    public function activateAction(Request $request, $id)

    {



        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Recibo')->find($id);



        if (!$entity) {

            throw $this->createNotFoundException('Unable to find Recibo entity.');

        }

        $entity->setMotivoCancelacion("");

        $entity->setActivo(true);

        $em->flush();

        $workspace = $entity->getWorkspace();

        $session = $request->getSession();

        $session->set('workspace', $workspace);

        return $this->redirect($this->generateUrl('recibo', array('ws' => $entity->getWorkspace()->getName())));

    }



    public function getRecibosByMonthYearAndWSAction($month, $year, $ws)

    {

        $em = $this->getDoctrine()->getManager();

        $recibos = array();

        $user = $this->get('security.context')->getToken()->getUser();

        if ($user->getRole()->getName() == "ROLE_SUPER_ADMIN") {

            $recibos = $em->getRepository('BackendBundle:Recibo')->findRecibosByMonthOfYearAndWS($month, $year, $ws);

        } else {

            $recibos = $em->getRepository('BackendBundle:Recibo')->findRecibosByMonthOfYearWithUserAndWS($month, $year, $user, $ws);

        }





        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(

            $recibos,

            $this->get('request')->query->get('page', 1),

            10);

        if ($user) {

            $user = $user->getId();

        }

        return $this->render('BackendBundle:Recibo:filtered.html.twig', array(

            'entities' => $pagination,

            'checked' => true,

            'user' => $user,

            'ws' => $em->getRepository('BackendBundle:Workspace')->find($ws)



        ));

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



        $tipo = null;

        $importe_desde = null;

        $importe_hasta = null;

        $cuenta = null;

        $concepto = null;

        $recibide = null;

        $reply = null;



        $estado = null;



        if (array_key_exists('recibo_estado', $_GET)) {

            if ($_GET['recibo_estado'] != '') {

                $estado = $_GET['recibo_estado'];

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



        if (array_key_exists('recibo_sisgerCode', $_GET)) {

            if ($_GET['recibo_sisgerCode'] != '') {

                $sisgerCode = $_GET['recibo_sisgerCode'];

            }

        }



        if (array_key_exists('recibo_from', $_GET)) {

            if ($_GET['recibo_from'] != '') {

                $date1 = date_create_from_format('d/m/Y', $_GET['recibo_from']);

                $from = new \DateTime($date1->format("Y-m-d") . " 00:00:00");

            }

        }



        if (array_key_exists('recibo_to', $_GET)) {

            if ($_GET['recibo_to'] != '') {

                $date2 = date_create_from_format('d/m/Y', $_GET['recibo_to']);

                $to = new \DateTime($date2->format("Y-m-d") . " 23:59:59");

                $to->setTime(23, 59, 59);

            }

        }



        if (array_key_exists('recibo_user', $_GET)) {

            if ($_GET['recibo_user'] != '') {

                $user = $_GET['recibo_user'];

            }

        }



        if (array_key_exists('recibo_tipo', $_GET)) {

            if ($_GET['recibo_tipo'] != '') {

                $tipo = $_GET['recibo_tipo'];

                if (array_key_exists('recibo_concepto', $_GET)) {

                    if ($_GET['recibo_concepto'] != '') {

                        $concepto = $_GET['recibo_concepto'];

                    }

                }



                if (array_key_exists('recibo_recibi_de', $_GET)) {

                    if ($_GET['recibo_recibi_de'] != '') {

                        $recibide = $_GET['recibo_recibi_de'];

                    }

                }

            }

        }



        if (array_key_exists('recibo_importe_desde', $_GET)) {

            if ($_GET['recibo_importe_desde'] != '') {

                $importe_desde = $_GET['recibo_importe_desde'];

            }

        }



        if (array_key_exists('recibo_importe_hasta', $_GET)) {

            if ($_GET['recibo_importe_hasta'] != '') {

                $importe_hasta = $_GET['recibo_importe_hasta'];

            }

        }



        if (array_key_exists('recibo_cuenta', $_GET)) {

            if ($_GET['recibo_cuenta'] != '') {

                $cuenta = $_GET['recibo_cuenta'];

            }

        }





        if (array_key_exists('recibo_reply', $_GET)) {

            if ($_GET['recibo_reply'] != '') {

                $reply = $_GET['recibo_reply'];

            }

        }



        $recibos = $em->getRepository('BackendBundle:Recibo')->findBusquedaAvanzada(true, $aavv, $c507, $lbrs, $sisgerCode, $from, $to, $user, $tipo, $importe_desde, $importe_hasta, $cuenta, $concepto, $recibide, $reply, $estado);

        $total = $em->getRepository('BackendBundle:Recibo')->findImporteBusquedaAvanzada(true, $aavv, $c507, $lbrs, $sisgerCode, $from, $to, $user, $tipo, $importe_desde, $importe_hasta, $cuenta, $concepto, $recibide, $reply, $estado);



        if (array_key_exists('buscar', $_GET)) {



            $paginator = $this->get('knp_paginator');

            $pagination = $paginator->paginate(

                $recibos,

                $this->get('request')->query->get('page', 1),

                10);



            if ($from) {

                $from = date_format($from, 'd/m/Y');

            }

            if ($to) {

                $to = date_format($to, 'd/m/Y');

            }





            return $this->render('BackendBundle:Recibo:filtered.html.twig', array(

                'entities' => $pagination,

                'aavv' => $aavv,

                'c507' => $c507,

                'lbrs' => $lbrs,

                'sisgerCode' => $sisgerCode,

                'from' => $from,

                'to' => $to,

                'user' => $user,

                'tipo' => $tipo,

                'importe_desde' => $importe_desde,

                'importe_hasta' => $importe_hasta,

                'cuenta' => $cuenta,

                'concepto' => $concepto,

                'recibi_de' => $recibide,

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





            return $this->render('BackendBundle:Recibo:reporte.html.twig', array(

                'recibos' => $recibos,

                'total' => $total[0][1],

                'range' => $range

            ));

        }

    }



    public function filtrarAction()

    {

        $em = $this->getDoctrine()->getManager();



        $aavv = null;

        $c507 = null;

        $lbrs = null;



        $from = null;

        $to = null;



        $sisgerCode = null;

        $user = null;



        $tipo = null;

        $importe_desde = null;

        $importe_hasta = null;

        $cuenta = null;

        $concepto = null;

        $recibide = null;

        $reply = null;



        $estado = null;



        if (array_key_exists('recibo_estado', $_GET)) {

            if ($_GET['recibo_estado'] != '') {

                $estado = $_GET['recibo_estado'];

            }

        }



        $ws = null;

        if (array_key_exists('recibo_entorno', $_GET)) {

            if ($_GET['recibo_entorno'] != '') {

                $ws = $_GET['recibo_entorno'];

            }

        }





        $mes = null;

        $anno = null;





        if ($ws == 2) {

            $aavv = $em->getRepository('BackendBundle:Workspace')->findOneByName('AAVV');

        }

        if ($ws == 3) {

            $c507 = $em->getRepository('BackendBundle:Workspace')->findOneByName('C507');

        }

        if ($ws == 4) {

            $lbrs = $em->getRepository('BackendBundle:Workspace')->findOneByName('L-BRS');

        }



        if (array_key_exists('recibo_sisgerCode', $_GET)) {

            if ($_GET['recibo_sisgerCode'] != '') {

                $sisgerCode = $_GET['recibo_sisgerCode'];

            }

        }



        if (array_key_exists('recibo_from', $_GET)) {

            if ($_GET['recibo_from'] != '') {

                $date1 = date_create_from_format('d/m/Y', $_GET['recibo_from']);

                $from = new \DateTime($date1->format("Y-m-d") . " 00:00:00");

            }

        }



        if (array_key_exists('recibo_to', $_GET)) {

            if ($_GET['recibo_to'] != '') {

                $date2 = date_create_from_format('d/m/Y', $_GET['recibo_to']);

                $to = new \DateTime($date2->format("Y-m-d") . " 23:59:59");



            }

        }



        if (array_key_exists('recibo_user', $_GET)) {

            if ($_GET['recibo_user'] != '') {

                $user = $_GET['recibo_user'];

            }

        }



        if (array_key_exists('recibo_tipo', $_GET)) {

            if ($_GET['recibo_tipo'] != '') {

                $tipo = $_GET['recibo_tipo'];

                if (array_key_exists('recibo_concepto', $_GET)) {

                    if ($_GET['recibo_concepto'] != '') {

                        $concepto = $_GET['recibo_concepto'];

                    }

                }



                if (array_key_exists('recibo_recibi_de', $_GET)) {

                    if ($_GET['recibo_recibi_de'] != '') {

                        $recibide = $_GET['recibo_recibi_de'];

                    }

                }

            }

        }



        if (array_key_exists('recibo_importe_desde', $_GET)) {

            if ($_GET['recibo_importe_desde'] != '') {

                $importe_desde = $_GET['recibo_importe_desde'];

            }

        }



        if (array_key_exists('recibo_importe_hasta', $_GET)) {

            if ($_GET['recibo_importe_hasta'] != '') {

                $importe_hasta = $_GET['recibo_importe_hasta'];

            }

        }



        if (array_key_exists('recibo_cuenta', $_GET)) {

            if ($_GET['recibo_cuenta'] != '') {

                $cuenta = $_GET['recibo_cuenta'];

            }

        }





        if (array_key_exists('recibo_reply', $_GET)) {

            if ($_GET['recibo_reply'] != '') {

                $reply = $_GET['recibo_reply'];

            }

        }



        $recibos = $em->getRepository('BackendBundle:Recibo')->findBusquedaAvanzada(true, $aavv, $c507, $lbrs, $sisgerCode, $from, $to, $user, $tipo, $importe_desde, $importe_hasta, $cuenta, $concepto, $recibide, $reply, $estado);

        $total = $em->getRepository('BackendBundle:Recibo')->findImporteBusquedaAvanzada(true, $aavv, $c507, $lbrs, $sisgerCode, $from, $to, $user, $tipo, $importe_desde, $importe_hasta, $cuenta, $concepto, $recibide, $reply, $estado);



        if (array_key_exists('buscar', $_GET)) {

            $paginator = $this->get('knp_paginator');

            $pagination = $paginator->paginate(

                $recibos,

                $this->get('request')->query->get('page', 1),

                10);



            if ($from) {

                $from = date_format($from, 'd/m/Y');

            }

            if ($to) {

                $to = date_format($to, 'd/m/Y');

            }



            return $this->render('BackendBundle:Recibo:filteredAll.html.twig', array(

                'entities' => $pagination,

                'aavv' => $aavv,

                'c507' => $c507,

                'lbrs' => $lbrs,

                'sisgerCode' => $sisgerCode,

                'from' => $from,

                'to' => $to,

                'user' => $user,

                'tipo' => $tipo,

                'importe_desde' => $importe_desde,

                'importe_hasta' => $importe_hasta,

                'cuenta' => $cuenta,

                'concepto' => $concepto,

                'recibi_de' => $recibide,

                'reply' => $reply,

                'estado' => $estado,

                'entorno' => $ws,

                'servicios' => $em->getRepository('BackendBundle:Service')->findAll(),

                'conceptosGasto' => $em->getRepository('BackendBundle:ConceptoGasto')->findAll(),

                'conceptosCosto' => $em->getRepository('BackendBundle:ConceptoCosto')->findAll(),

                'inventarios' => $em->getRepository('BackendBundle:Inventario')->findAll()

            ));

        } else if (array_key_exists('excel', $_GET)){

            if ($from) {

                $from = date_format($from, 'd/m/Y');

            }

            if ($to) {

                $to = date_format($to, 'd/m/Y');

            }

            $this->balanceExcel($recibos, $from, $to);

        } else {

            if ($from) {

                $from = date_format($from, 'd/m/Y');

            }

            if ($to) {

                $to->setTime(23, 59, 59);

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



            $extras = $em->getRepository('BackendBundle:Extra')->findPorFechas($from, $to);

            $totalExtras = $em->getRepository('BackendBundle:Extra')->findImportePorFechas($from, $to);





            return $this->render('BackendBundle:Recibo:reporteAll.html.twig', array(

                'recibos' => $recibos,

                'extras' => $extras,

                'total' => $total[0][1],

                'totalExtras' => $totalExtras[0][1],

                'range' => $range



            ));

        }

    }



    public function balanceExcel($recibos, $from, $to)

    {

        $uploaddir = $this->container->getParameter('belraysa.route.lbrs');

        $uploadfile = $uploaddir . basename('Balance.xlsx');



        $objPHPExcel = \PHPExcel_IOFactory::load($uploadfile);



        $styleArray = array(

            'font'  => array(

                'bold'  => true,

                'color' => array('rgb' => '8b0000')

            ));





        if($from && $to){

            $objPHPExcel->setActiveSheetIndex(0)

                ->setCellValue('F5', $from . ' - ' . $to);

        }



        $count = 8;

        $total = 0;

        $style = $objPHPExcel->getActiveSheet()->getStyle('A8:M8');

        $totalCxC = 0;

        foreach($recibos as $recibo){

            if($count > 8)

                $objPHPExcel->getActiveSheet()->duplicateStyle($style, 'A' . $count . ':M' . $count);

            $objPHPExcel->getActiveSheet()

                ->getStyle('F'.$count)

                ->getAlignment()

                ->setHorizontal('left');

            $objPHPExcel->getActiveSheet()

                ->getStyle('F'.$count)

                ->getAlignment()

                ->setWrapText(true);

            $objPHPExcel->getActiveSheet()

                ->getStyle('J'.$count)

                ->getAlignment()

                ->setHorizontal('left');

            $objPHPExcel->getActiveSheet()

                ->getStyle('J'.$count)

                ->getAlignment()

                ->setWrapText(true);

            $objPHPExcel->getActiveSheet()

                ->getStyle('K'.$count)

                ->getAlignment()

                ->setHorizontal('left');

            $objPHPExcel->getActiveSheet()

                ->getStyle('K'.$count)

                ->getAlignment()

                ->setWrapText(true);

            $objPHPExcel->getActiveSheet()

                ->getStyle('L'.$count)

                ->getAlignment()

                ->setHorizontal('left');

            $objPHPExcel->getActiveSheet()

                ->getStyle('L'.$count)

                ->getAlignment()

                ->setWrapText(true);

            $objPHPExcel->getActiveSheet()->getStyle('M' . $count)->getNumberFormat()->applyFromArray(

	 		    array('code' => '#,##0.00')

	        );



            $activo = $recibo->isActivo()? 'Sí' : 'No';

            $tipo = $recibo->getTipo();

            $deA = $tipo == 'Ingreso'? $recibo->getRecibiDe() : $recibo->getPagueA();

            $metodoPago = $recibo->getMetodoPago()? $recibo->getMetodoPago()->getName(): '';

            $cuenta = $recibo->getEntrada()? $recibo->getEntrada()->getBanking()? $recibo->getEntrada()->getBanking()->getName() : '' : '';

            $importe = $recibo->getImporte() < 0? -$recibo->getImporte(): $recibo->getImporte();

            $detalles = $recibo->getEntrada()? $recibo->getEntrada()->getDetails(): '';

            $conceptos = '';

            if($tipo == 'Ingreso'){

                foreach ($recibo->getServicios() as $servicio) {

                    $conceptos = $conceptos . $servicio->getName() . ', ';

                }

                if($recibo->getCuentaXCobrar()){

                    $tipo = 'CxC';

                    $importe = $recibo->getSaldoPendiente();

                    if($recibo->isActivo()){

                        $totalCxC += $importe;

                    }

                }

            }

            else if($tipo == 'Gasto')

            {

                foreach ($recibo->getConceptosGasto() as $servicio) {

                    $conceptos = $conceptos . $servicio->getNombre() . ', ';

                }

            }

            else if($tipo == 'Costo')

            {

                foreach ($recibo->getConceptosCosto() as $servicio) {

                    $conceptos = $conceptos . $servicio->getNombre() . ', ';

                }

            }

            else

            {

                foreach ($recibo->getInventarios() as $servicio) {

                    $conceptos = $conceptos . $servicio->getNombre() . ', ';

                }

            }



            $conceptos = substr($conceptos, 0, -2);



//            if($tipo == 'Ingreso' && $recibo->getCuentaXCobrar()){

//                $phpColor = new \PHPExcel_Style_Color();

//                $phpColor->setRGB("0070C0");

//

//                $objPHPExcel->getActiveSheet()->getStyle('M'.$count)->getFont()->setColor($phpColor);

//            }



            //return new JsonResponse(json_encode($conceptos));

            $objPHPExcel->setActiveSheetIndex(0)

                ->setCellValue('A'.$count, $count - 7)

                ->setCellValue('B'.$count, $activo)

                ->setCellValue('C'.$count, $recibo->getSisgerCode())

                ->setCellValue('D'.$count, $tipo)

                ->setCellValue('E'.$count, $metodoPago)

                ->setCellValue('F'.$count, $cuenta)

                ->setCellValue('G'.$count, $recibo->getRefExpediente())

                ->setCellValue('H'.$count, date_format($recibo->getFecha(), 'd/m/Y'))

                ->setCellValue('I'.$count, $recibo->getUsuario()->getLetra())

                ->setCellValue('J'.$count, $deA)

                ->setCellValue('K'.$count, $detalles)

                ->setCellValue('L'.$count, $conceptos)

                ->setCellValue('M'.$count, $importe);



            $count = $count + 1;

            $total += $recibo->getImporte();

        }

        $objPHPExcel->getActiveSheet()->duplicateStyle($style, 'A' . $count . ':M' . $count);

        $objPHPExcel->getActiveSheet()

            ->getStyle('L'.$count)

            ->getAlignment()

            ->setHorizontal('right')

            ->setVertical('center');

        $objPHPExcel->getActiveSheet()

            ->getStyle('M'.$count)

            ->getAlignment()

            ->setHorizontal('center')

            ->setVertical('center');

        $objPHPExcel->setActiveSheetIndex(0)

            ->setCellValue('L'.$count, 'TOTAL')

            ->setCellValue('M'.$count, $total);

        $objPHPExcel->getActiveSheet()->getRowDimension($count)->setRowHeight(40);

        $count++;

        $objPHPExcel->getActiveSheet()->duplicateStyle($style, 'A' . $count . ':M' . $count);

        $objPHPExcel->getActiveSheet()

            ->getStyle('L'.$count)

            ->getAlignment()

            ->setHorizontal('right')

            ->setVertical('center');

        $objPHPExcel->setActiveSheetIndex(0)

            ->setCellValue('L'.$count, 'TOTAL CxC')

            ->setCellValue('M'.$count, $totalCxC);



        // Set document properties

        $objPHPExcel->getProperties()->setCreator("BELRAYSA TOURS & TRAVEL GROUP S.A")

            ->setLastModifiedBy("BELRAYSA TOURS & TRAVEL GROUP S.A")

            ->setTitle("Balance")

            ->setSubject("Balance")

            ->setDescription("Balance")

            ->setKeywords("balance")

            ->setCategory("Balance");





        // Redirect output to a client’s web browser (Excel2007)

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=UTF-8');

        //header('Content-Type: text/html; charset=ISO-8859-1');

        header('Content-Disposition: attachment;filename="Balance' . '.xlsx"');

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



        $tipo = null;

        $importe_desde = null;

        $importe_hasta = null;

        $cuenta = null;

        $concepto = null;

        $recibide = null;

        $reply = null;



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



        $recibos = $em->getRepository('BackendBundle:Recibo')->findBusquedaAvanzada(true, $aavv, $c507, $lbrs, $sisgerCode, $from, $to, $user, $tipo, $importe_desde, $importe_hasta, $cuenta, $concepto, $recibide, $reply, $estado);



        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(

            $recibos,

            $this->get('request')->query->get('page', 1),

            10);



        if ($from) {

            $from = date_format($from, 'd/m/Y');

        }

        if ($to) {

            $to = date_format($to, 'd/m/Y');

        }



        return $this->render('BackendBundle:Recibo:filtered.html.twig', array(

            'entities' => $pagination,

            'aavv' => $aavv,

            'c507' => $c507,

            'lbrs' => $lbrs,

            'sisgerCode' => $sisgerCode,

            'from' => $from,

            'to' => $to,

            'user' => $user,

            'tipo' => $tipo,

            'importe_desde' => $importe_desde,

            'importe_hasta' => $importe_hasta,

            'cuenta' => $cuenta,

            'concepto' => $concepto,

            'recibi_de' => $recibide,

            'reply' => $reply,

            'estado' => $estado

        ));

    }



    public function salvarEgresosAction()

    {

        if (array_key_exists('egreso', $_POST)) {



            $em = $this->getDoctrine()->getManager();

            $egresos = array();



            if ($_POST['position'] != "") {

                $egresos[] = $_POST['egreso']['position'];

            } else {

                $egresos[] = $_POST['egreso'];

            }

            foreach ($egresos as $egresoPos) {

                foreach ($egresoPos as $egreso) {

                    if ($egreso['tipo'] == 'gasto') {

                        $entity = new Gasto();

                        $entity->setTipo('Gasto');



                        $fecha = date_create($egreso['fecha']);

                        if ($fecha) {

                            $entity->setFecha($fecha);

                        } else {

                            $entity->setFecha(new \DateTime());

                        }



                        $entity->setCreacion(new \DateTime());

                        $entity->setImporte(0 - $egreso['importe']);

                        $entity->setSuma($egreso['suma']);

                        $entity->setPagueA($egreso['a']);

                        $entity->setDetalles($egreso['detalles']);

                        $entity->setMetodoPago($em->getRepository('BackendBundle:PaymentMethod')->find($egreso['metodoPago']));

                        $user = $this->get('security.context')->getToken()->getUser();

                        $workspace = $this->get('belraysa.workspace')->getCurrentWorkspace();

                        $entity->setWorkspace($workspace);

                        $entity->setUsuario($user);

                        $entity->setActivo(true);

                        if ($egreso['expediente'] != "") {

                            $entity->setExpediente($em->getRepository('BackendBundle:Reply')->find($egreso['expediente']));

                            $entity->setRefExpediente($em->getRepository('BackendBundle:Reply')->find($egreso['expediente'])->getSisgerCode());

                        }

                        $em->persist($entity);

                        $em->flush();

                        $workspace->addRecibo($entity);

                        $idEgreso = new IdEgreso();

                        $em->persist($idEgreso);

                        $em->flush();

                        $entity->setIdEgreso($idEgreso);

                        $entity->setSisgerCode('E-' . $idEgreso->getId());

                        $em->flush();

                        foreach ($egreso['conceptos'] as $concepto) {

                            $entity->addConcepto($em->getRepository('BackendBundle:ConceptoGasto')->find($concepto));

                        }

                        if ($egreso['banking'] != "") {

                            $banking = $em->getRepository('BackendBundle:Banking')->find($egreso['banking']);

                            // print_r($_POST['receptor']);

                            // die;

                            $banking->setBalance($banking->getBalance() + $entity->getImporte());

                            $entry = new BankingEntry();

                            $entry->setDate($entity->getFecha());

                            $entry->setCredit(0);

                            $entry->setDebit($entity->getImporte());

                            $entry->setBalance($banking->getBalance());

                            $entry->setDetails($egreso['detalles']);

                            $entry->setRecibo($entity);

                            $entry->setActivo(true);

                            $em->persist($entry);

                            $em->flush();

                            $entity->setEntrada($entry);

                            $banking->addEntry($entry);

                            $em->flush();

                        }

                    } elseif ($egreso['tipo'] == 'costo') {

                        $entity = new Costo();

                        $entity->setTipo('Costo');



                        $fecha = date_create($egreso['fecha']);

                        if ($fecha) {

                            $entity->setFecha($fecha);

                        } else {

                            $entity->setFecha(new \DateTime());

                        }

                        $entity->setCreacion(new \DateTime());

                        $entity->setImporte(0 - $egreso['importe']);

                        //$entity->setSuma($egreso['suma']);

                        $entity->setPagueA($egreso['a']);

                        $entity->setDetalles($egreso['detalles']);

                        $entity->setMetodoPago($em->getRepository('BackendBundle:PaymentMethod')->find($egreso['metodoPago']));

                        $user = $this->get('security.context')->getToken()->getUser();

                        $workspace = $this->get('belraysa.workspace')->getCurrentWorkspace();

                        $entity->setWorkspace($workspace);

                        $entity->setUsuario($user);

                        $entity->setActivo(true);

                        if ($egreso['expediente'] != "") {

                            $entity->setExpediente($em->getRepository('BackendBundle:Reply')->find($egreso['expediente']));

                            $entity->setRefExpediente($em->getRepository('BackendBundle:Reply')->find($egreso['expediente'])->getSisgerCode());

                        }

                        $em->persist($entity);

                        $em->flush();

                        $workspace->addRecibo($entity);

                        $idEgreso = new IdEgreso();

                        $em->persist($idEgreso);

                        $em->flush();

                        $entity->setIdEgreso($idEgreso);

                        $entity->setSisgerCode('E-' . $idEgreso->getId());

                        $em->flush();

                        foreach ($egreso['conceptos'] as $concepto) {

                            $entity->addConcepto($em->getRepository('BackendBundle:ConceptoCosto')->find($concepto));

                        }

                        if ($egreso['banking'] != "") {

                            $banking = $em->getRepository('BackendBundle:Banking')->find($egreso['banking']);

                            // print_r($_POST['receptor']);

                            // die;

                            $banking->setBalance($banking->getBalance() + $entity->getImporte());

                            $entry = new BankingEntry();

                            $entry->setDate($entity->getFecha());

                            $entry->setCredit(0);

                            $entry->setDebit($entity->getImporte());

                            $entry->setBalance($banking->getBalance());

                            $entry->setDetails($egreso['detalles']);

                            $entry->setRecibo($entity);

                            $entry->setActivo(true);

                            $em->persist($entry);

                            $em->flush();

                            $entity->setEntrada($entry);

                            $banking->addEntry($entry);

                            $em->flush();

                        }

                    } else {

                        $entity = new CostoRecurrente();

                        $entity->setTipo('Costo recurrente');



                        $fecha = date_create($egreso['fecha']);

                        if ($fecha) {

                            $entity->setFecha($fecha);

                        } else {

                            $entity->setFecha(new \DateTime());

                        }

                        $entity->setCreacion(new \DateTime());

                        $entity->setImporte(0 - $egreso['importe']);

                        // $entity->setSuma($egreso['suma']);

                        $entity->setPagueA($egreso['a']);

                        $entity->setDetalles($egreso['detalles']);

                        $entity->setMetodoPago($em->getRepository('BackendBundle:PaymentMethod')->find($egreso['metodoPago']));

                        $user = $this->get('security.context')->getToken()->getUser();

                        $workspace = $this->get('belraysa.workspace')->getCurrentWorkspace();

                        $entity->setWorkspace($workspace);

                        $entity->setUsuario($user);

                        $entity->setActivo(true);

                        if ($egreso['expediente'] != "") {

                            $entity->setExpediente($em->getRepository('BackendBundle:Reply')->find($egreso['expediente']));

                            $entity->setRefExpediente($em->getRepository('BackendBundle:Reply')->find($egreso['expediente'])->getSisgerCode());

                        }

                        $em->persist($entity);

                        $em->flush();

                        $workspace->addRecibo($entity);

                        $idEgreso = new IdEgreso();

                        $em->persist($idEgreso);

                        $em->flush();

                        $entity->setIdEgreso($idEgreso);

                        $entity->setSisgerCode('E-' . $idEgreso->getId());

                        $em->flush();

                        foreach ($egreso['conceptos'] as $concepto) {

                            $entity->addInventario($em->getRepository('BackendBundle:Inventario')->find($concepto));

                        }

                        if ($egreso['banking'] != "") {

                            $banking = $em->getRepository('BackendBundle:Banking')->find($egreso['banking']);

                            // print_r($_POST['receptor']);

                            // die;

                            $banking->setBalance($banking->getBalance() + $entity->getImporte());

                            $entry = new BankingEntry();

                            $entry->setDate($entity->getFecha());

                            $entry->setCredit(0);

                            $entry->setDebit($entity->getImporte());

                            $entry->setBalance($banking->getBalance());

                            $entry->setDetails($egreso['detalles']);

                            $entry->setRecibo($entity);

                            $entry->setActivo(true);

                            $em->persist($entry);

                            $em->flush();

                            $entity->setEntrada($entry);

                            $banking->addEntry($entry);

                            $em->flush();

                        }

                    }

                }

            }

        }

        return $this->redirect($this->generateUrl('recibo', array('ws' => $workspace)));



    }





    public function salvarRecibosAction()

    {



        if (array_key_exists('recibo', $_POST)) {

            $em = $this->getDoctrine()->getManager();

            $recibos = array();

            if ($_POST['position'] != "") {

                $recibos[] = $_POST['recibo']['position'];

            } else {

                $recibos[] = $_POST['recibo'];

            }

            foreach ($recibos as $reciboPos) {

                foreach ($reciboPos as $recibo) {

                    if ($recibo['tipo'] == 'ingreso') {

                        $entity = new Ingreso();

                        $entity->setTipo('Ingreso');



                        $fecha = date_create($recibo['fecha']);

                        if ($fecha) {

                            $entity->setFecha($fecha);

                        } else {

                            $entity->setFecha(new \DateTime());

                        }



                        $entity->setCreacion(new \DateTime());

                        $entity->setSaldoAnterior($recibo['saldoAnterior']);

                        $entity->setImporte($recibo['importe']);

                        $entity->setFechaLimite(null);

                        $entity->setCuentaXCobrar(0);



                        // $entity->setSuma($recibo['suma']);



                        $entity->setRecibiDe($recibo['a']);



                        $entity->setMetodoPago($em->getRepository('BackendBundle:PaymentMethod')->find($recibo['metodoPago']));

                        $user = $this->get('security.context')->getToken()->getUser();

                        $entity->setUsuario($user);

                        $entity->setActivo(true);

                        $workspace = $em->getRepository('BackendBundle:Workspace')->find($recibo['workspace']);

                        if ($workspace) {

                            $entity->setWorkspace($workspace);

                        }

                        if ($recibo['expediente'] != "") {

                            $entity->setExpediente($em->getRepository('BackendBundle:Reply')->find($recibo['expediente']));

                            $entity->setRefExpediente($em->getRepository('BackendBundle:Reply')->find($recibo['expediente'])->getSisgerCode());



                            $workspace = $em->getRepository('BackendBundle:Reply')->find($recibo['expediente'])->getWorkspace();

                            $entity->setWorkspace($workspace);

                        } else {

                            if ($entity->getWorkspace() == null) {

                                $workspace = $this->get('belraysa.workspace')->getCurrentWorkspace();

                                $entity->setWorkspace($workspace);

                            }

                        }



                        $entity2 = clone $entity;

                        if($entity->getSaldoAnterior() > $entity->getImporte() && $entity->getImporte() == 0){

                            $entity->setCuentaXCobrar(1);

                            $entity->setFechaLimite(date_create($recibo['fechaLimite']));

                            $entity->setSaldoPendiente($entity->getSaldoAnterior());

                        }



                        else if($entity->getSaldoAnterior() > $entity->getImporte()){

                            $entity->setCuentaXCobrar(0);

                            $entity->setFechaLimite(null);

                            $entity->setSaldoPendiente($entity->getSaldoAnterior() - $entity->getImporte());



                            $entity2->setCuentaXCobrar(1);

                            $entity2->setFechaLimite(date_create($recibo['fechaLimite']));

                            $entity2->setSaldoAnterior($entity->getSaldoPendiente());

                            $entity2->setSaldoPendiente($entity2->getSaldoAnterior());

                            $entity2->setImporte(0);

                            $em->persist($entity2);

                            $entity2->setDetalles($recibo['detalles']);

                        }



                        $em->persist($entity);

                        $em->flush();



                        //  $entity->setSaldoAnterior($recibo['saldoAnterior']);

                        $entity->setDetalles($recibo['detalles']);

                        //  $entity->setAbono($recibo['abono']);

                        $em->flush();





                        if($entity->getSaldoAnterior() > $entity->getImporte() && $entity->getImporte() != 0){

                            $idIngreso2 = new IdIngreso();

                            $em->persist($idIngreso2);

                            $em->flush();

                            $entity2->setIdIngreso($idIngreso2);

                            $entity2->setSisgerCode('I-' . $idIngreso2->getId());

                            $em->flush();

                        }



                        $idIngreso = new IdIngreso();

                        $em->persist($idIngreso);

                        $em->flush();

                        $entity->setIdIngreso($idIngreso);

                        $entity->setSisgerCode('I-' . $idIngreso->getId());

                        $em->flush();

                        foreach ($recibo['conceptos'] as $concepto) {

                            $entity->addServicio($em->getRepository('BackendBundle:Service')->find($concepto));

                        }

                        if($entity->getSaldoAnterior() > $entity->getImporte() && $entity->getImporte() != 0){

                            $entity2->setServicios($entity->getServicios());

                        }

                        if ($recibo['banking'] != "") {

                            $banking = $em->getRepository('BackendBundle:Banking')->find($recibo['banking']);

                            // print_r($_POST['receptor']);

                            // die;

                            $banking->setBalance($banking->getBalance() + $entity->getImporte());

                            $entry = new BankingEntry();

                            $entry->setDate($entity->getFecha());

                            $entry->setCredit($entity->getImporte());

                            $entry->setDebit(0);

                            $entry->setBalance($banking->getBalance());

                            $entry->setDetails($recibo['detalles']);

                            $entry->setRecibo($entity);

                            $entry->setActivo(true);

                            if($entity->getSaldoAnterior() > $entity->getImporte() && $entity->getImporte() != 0){

                                $entry2 = clone $entry;

                                $entry2->setCredit(0);

                                $entry2->setRecibo($entity2);

                                $em->persist($entry2);

                                $em->flush();

                                $entity2->setEntrada($entry2);

                                $banking->addEntry($entry2);

                                $em->flush();

                            }

                            $em->persist($entry);

                            $em->flush();

                            $entity->setEntrada($entry);

                            $banking->addEntry($entry);

                            $em->flush();

                        }

                    }

                    /////////////////////////////////////////////////////////////////

                    elseif ($recibo['tipo'] == 'gasto') {

                        $entity = new Gasto();

                        $entity->setTipo('Gasto');



                        $fecha = date_create($recibo['fecha']);

                        if ($fecha) {

                            $entity->setFecha($fecha);

                        } else {

                            $entity->setFecha(new \DateTime());

                        }



                        $entity->setCreacion(new \DateTime());

                        $entity->setImporte(0 - $recibo['importe']);

                        // $entity->setSuma($recibo['suma']);

                        $entity->setPagueA($recibo['a']);

                        $entity->setDetalles($recibo['detalles']);

                        $entity->setMetodoPago($em->getRepository('BackendBundle:PaymentMethod')->find($recibo['metodoPago']));

                        $user = $this->get('security.context')->getToken()->getUser();

                        $workspace = $em->getRepository('BackendBundle:Workspace')->find($recibo['workspace']);

                        if ($workspace) {

                            $entity->setWorkspace($workspace);

                        }

                        $entity->setUsuario($user);

                        $entity->setActivo(true);

                        if ($recibo['expediente'] != "") {

                            $entity->setExpediente($em->getRepository('BackendBundle:Reply')->find($recibo['expediente']));

                            $entity->setRefExpediente($em->getRepository('BackendBundle:Reply')->find($recibo['expediente'])->getSisgerCode());



                            $workspace = $em->getRepository('BackendBundle:Reply')->find($recibo['expediente'])->getWorkspace();

                            $entity->setWorkspace($workspace);

                        } else {

                            if ($entity->getWorkspace() == null) {

                                $workspace = $this->get('belraysa.workspace')->getCurrentWorkspace();

                                $entity->setWorkspace($workspace);

                            }

                        }

                        $em->persist($entity);

                        $em->flush();



                        $idEgreso = new IdEgreso();

                        $em->persist($idEgreso);

                        $em->flush();

                        $entity->setIdEgreso($idEgreso);

                        $entity->setSisgerCode('E-' . $idEgreso->getId());

                        $em->flush();

                        foreach ($recibo['conceptos'] as $concepto) {

                            $entity->addConcepto($em->getRepository('BackendBundle:ConceptoGasto')->find($concepto));

                        }

                        if ($recibo['banking'] != "") {

                            $banking = $em->getRepository('BackendBundle:Banking')->find($recibo['banking']);

                            // print_r($_POST['receptor']);

                            // die;

                            $banking->setBalance($banking->getBalance() + $entity->getImporte());

                            $entry = new BankingEntry();

                            $entry->setDate($entity->getFecha());

                            $entry->setCredit(0);

                            $entry->setDebit($entity->getImporte());

                            $entry->setBalance($banking->getBalance());

                            $entry->setDetails($recibo['detalles']);

                            $entry->setRecibo($entity);

                            $entry->setActivo(true);

                            $em->persist($entry);

                            $em->flush();

                            $entity->setEntrada($entry);

                            $banking->addEntry($entry);

                            $em->flush();

                        }

                    } elseif ($recibo['tipo'] == 'costo') {

                        $entity = new Costo();

                        $entity->setTipo('Costo');



                        $fecha = date_create($recibo['fecha']);

                        if ($fecha) {

                            $entity->setFecha($fecha);

                        } else {

                            $entity->setFecha(new \DateTime());

                        }

                        $entity->setCreacion(new \DateTime());

                        $entity->setImporte(0 - $recibo['importe']);

                        //$entity->setSuma($egreso['suma']);

                        $entity->setPagueA($recibo['a']);

                        $entity->setDetalles($recibo['detalles']);

                        $entity->setMetodoPago($em->getRepository('BackendBundle:PaymentMethod')->find($recibo['metodoPago']));

                        $user = $this->get('security.context')->getToken()->getUser();



                        $entity->setUsuario($user);

                        $entity->setActivo(true);

                        $workspace = $em->getRepository('BackendBundle:Workspace')->find($recibo['workspace']);

                        if ($workspace) {

                            $entity->setWorkspace($workspace);

                        }

                        if ($recibo['expediente'] != "") {

                            $entity->setExpediente($em->getRepository('BackendBundle:Reply')->find($recibo['expediente']));

                            $entity->setRefExpediente($em->getRepository('BackendBundle:Reply')->find($recibo['expediente'])->getSisgerCode());



                            $workspace = $em->getRepository('BackendBundle:Reply')->find($recibo['expediente'])->getWorkspace();

                            $entity->setWorkspace($workspace);

                        } else {

                            if ($entity->getWorkspace() == null) {

                                $workspace = $this->get('belraysa.workspace')->getCurrentWorkspace();

                                $entity->setWorkspace($workspace);

                            }

                        }



                        $em->persist($entity);

                        $em->flush();



                        $idEgreso = new IdEgreso();

                        $em->persist($idEgreso);

                        $em->flush();

                        $entity->setIdEgreso($idEgreso);

                        $entity->setSisgerCode('E-' . $idEgreso->getId());

                        $em->flush();

                        foreach ($recibo['conceptos'] as $concepto) {

                            $entity->addConcepto($em->getRepository('BackendBundle:ConceptoCosto')->find($concepto));

                        }

                        if ($recibo['banking'] != "") {

                            $banking = $em->getRepository('BackendBundle:Banking')->find($recibo['banking']);

                            // print_r($_POST['receptor']);

                            // die;

                            $banking->setBalance($banking->getBalance() + $entity->getImporte());

                            $entry = new BankingEntry();

                            $entry->setDate($entity->getFecha());

                            $entry->setCredit(0);

                            $entry->setDebit($entity->getImporte());

                            $entry->setBalance($banking->getBalance());

                            $entry->setDetails($recibo['detalles']);

                            $entry->setRecibo($entity);

                            $entry->setActivo(true);

                            $em->persist($entry);

                            $em->flush();

                            $entity->setEntrada($entry);

                            $banking->addEntry($entry);

                            $em->flush();

                        }

                    } else {

                        $entity = new CostoRecurrente();

                        $entity->setTipo('Costo recurrente');



                        $fecha = date_create($recibo['fecha']);

                        if ($fecha) {

                            $entity->setFecha($fecha);

                        } else {

                            $entity->setFecha(new \DateTime());

                        }

                        $entity->setCreacion(new \DateTime());

                        $entity->setImporte(0 - $recibo['importe']);

                        // $entity->setSuma($egreso['suma']);

                        $entity->setPagueA($recibo['a']);

                        $entity->setDetalles($recibo['detalles']);

                        $entity->setMetodoPago($em->getRepository('BackendBundle:PaymentMethod')->find($recibo['metodoPago']));

                        $user = $this->get('security.context')->getToken()->getUser();



                        $entity->setUsuario($user);

                        $entity->setActivo(true);

                        $workspace = $em->getRepository('BackendBundle:Workspace')->find($recibo['workspace']);

                        if ($workspace) {

                            $entity->setWorkspace($workspace);

                        }

                        if ($recibo['expediente'] != "") {

                            $entity->setExpediente($em->getRepository('BackendBundle:Reply')->find($recibo['expediente']));

                            $entity->setRefExpediente($em->getRepository('BackendBundle:Reply')->find($recibo['expediente'])->getSisgerCode());



                            $workspace = $em->getRepository('BackendBundle:Reply')->find($recibo['expediente'])->getWorkspace();

                            $entity->setWorkspace($workspace);

                        } else {

                            if ($entity->getWorkspace() == null) {

                                $workspace = $this->get('belraysa.workspace')->getCurrentWorkspace();

                                $entity->setWorkspace($workspace);

                            }

                        }



                        $em->persist($entity);

                        $em->flush();



                        $idEgreso = new IdEgreso();

                        $em->persist($idEgreso);

                        $em->flush();

                        $entity->setIdEgreso($idEgreso);

                        $entity->setSisgerCode('E-' . $idEgreso->getId());

                        $em->flush();

                        foreach ($recibo['conceptos'] as $concepto) {

                            $entity->addInventario($em->getRepository('BackendBundle:Inventario')->find($concepto));

                        }

                        if ($recibo['banking'] != "") {

                            $banking = $em->getRepository('BackendBundle:Banking')->find($recibo['banking']);

                            // print_r($_POST['receptor']);

                            // die;

                            $banking->setBalance($banking->getBalance() + $entity->getImporte());

                            $entry = new BankingEntry();

                            $entry->setDate($entity->getFecha());

                            $entry->setCredit(0);

                            $entry->setDebit($entity->getImporte());

                            $entry->setBalance($banking->getBalance());

                            $entry->setDetails($recibo['detalles']);

                            $entry->setRecibo($entity);

                            $entry->setActivo(true);

                            $em->persist($entry);

                            $em->flush();

                            $entity->setEntrada($entry);

                            $banking->addEntry($entry);

                            $em->flush();

                        }

                    }

                }

            }

        }



        return $this->redirect($this->generateUrl('recibo_all'));



    }

}

