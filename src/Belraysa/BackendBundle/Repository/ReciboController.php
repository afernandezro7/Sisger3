<?php

namespace Belraysa\BackendBundle\Controller;

use Belraysa\BackendBundle\Entity\Ajuste;
use Belraysa\BackendBundle\Entity\BankingEntry;

use Belraysa\BackendBundle\Entity\IdEgreso;
use Belraysa\BackendBundle\Entity\IdIngreso;
use Belraysa\BackendBundle\Form\CostType;
use Belraysa\BackendBundle\Form\ExpendationType;
use Belraysa\BackendBundle\Form\IncomeType;
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

    /**
     * Lists all Recibo entities.
     *
     */
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

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);


        return $this->render('BackendBundle:Recibo:index.html.twig', array(
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
        //    $entities1 = $em->getRepository('BackendBundle:Recibo')->findOrdenados1();

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

    /**
     * Creates a form to create a Recibo entity.
     *
     * @param Recibo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
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

    /**
     * Displays a form to create a new Recibo entity.
     *
     */
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

    /**
     * Finds and displays a Recibo entity.
     *
     */
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

    /**
     * Displays a form to edit an existing Recibo entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Recibo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Recibo entity.');
        }

        $editForm = $this->createEditForm($entity);


        return $this->render('BackendBundle:Recibo:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),

        ));
    }

    /**
     * Creates a form to edit a Recibo entity.
     *
     * @param Recibo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Recibo $entity)
    {
        $form = $this->createForm(new ReciboType(), $entity, array(
            'action' => $this->generateUrl('recibo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Recibo entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Recibo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Recibo entity.');
        }


        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('recibo_edit', array('id' => $id)));
        }

        return $this->render('BackendBundle:Recibo:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),

        ));
    }

    /**
     * Deletes a Recibo entity.
     *
     */
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
            $ajuste->setBanking($entity->getEntrada()->getBanking());
            $ajuste->setFecha(new \DateTime());
            $importe = 0;
            if ($entity->getTipo() == 'Ingreso') {
                $importe = 0 - $entity->getImporte();
            } else {
                $importe = $entity->getImporte();
            }

            $ajuste->setImporte($importe);
            $banking = $ajuste->getBanking();
            $ajuste->setConcepto('Ajuste de la cuenta ' . $banking->getName() . ' tras la cancelación del recibo ' . $entity->getSisgerCode());
            $em->persist($ajuste);
            $em->flush();

            $idSisger = str_pad($ajuste->getId(), 4, "0", STR_PAD_LEFT);
            $idBanking = str_pad($ajuste->getEntrada()->getBanking()->getId(), 4, "0", STR_PAD_LEFT);

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
                'entorno' => $ws
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
                'range' => $range,


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
}
