<?php

namespace Belraysa\BackendBundle\Controller;

use Belraysa\BackendBundle\Entity\BankingEntry;
use Belraysa\BackendBundle\Form\CostType;
use Belraysa\BackendBundle\Form\ExpendationType;
use Belraysa\BackendBundle\Form\IncomeType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Belraysa\BackendBundle\Controller\FPDF\PDFController;
use Belraysa\BackendBundle\Entity\Receipe;
use HTML2PDF;

/**
 * Receipe controller.
 *
 */
class ReceipeController extends Controller
{

    /**
     * Lists all Receipe entities.
     *
     */
    public function indexAction($ws)
    {
        $em = $this->getDoctrine()->getManager();
        $ws = $em->getRepository('BackendBundle:Workspace')->find($ws);
        $entities = $em->getRepository('BackendBundle:Receipe')->findReceipesByWorkspaceOnDescendantOrder($ws);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);

        return $this->render('BackendBundle:Receipe:index.html.twig', array(
            'entities' => $pagination,
            'workspace' => $ws
        ));
    }


    public function indexByReplyAction($idReply)
    {
        $em = $this->getDoctrine()->getManager();
        $reply = $em->getRepository('BackendBundle:Reply')->find($idReply);
        $entities = $em->getRepository('BackendBundle:Receipe')->findByReply($reply, array('creationDate' => 'DESC'));

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);

        return $this->render('BackendBundle:Receipe:index.html.twig', array(
            'entities' => $pagination,
        ));
    }

    public function indexAllAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('BackendBundle:Receipe')->findAll();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);

        return $this->render('BackendBundle:Receipe:indexAll.html.twig', array(
            'entities' => $pagination,
        ));
    }

    public function exportByReplyAction($idReply)
    {
        $em = $this->getDoctrine()->getManager();
        $reply = $em->getRepository('BackendBundle:Reply')->find($idReply);
        $receipesReport = $em->getRepository('BackendBundle:Receipe')->findByReply($reply);
        $totalImporte = $em->getRepository('BackendBundle:Receipe')->findTotalImporteReceipesByReply($idReply);

        return $this->render('BackendBundle:Receipe:reportePorExp.html.twig', array(
            'recibos' => $receipesReport,
            'total' => $totalImporte[0][1],
            'reply' => $reply
        ));

    }

    /**
     * Creates a new Receipe entity.
     *
     */
    public function createIncomeAction(Request $request)
    {
        $entity = new Receipe();
        $form = $this->createCreateForm($entity, 'income');
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $reply = null;
        if (array_key_exists('idReply', $_POST)) {
            $reply = $em->getRepository('BackendBundle:Reply')->findOneBySisgerCode($_POST['idReply']);
            $entity->setReply($reply);
        }
        if ($form->isValid()) {
            $entity->setType('Ingreso');
            $concept = "";
            $i = 0;
            foreach ($_POST['concept'] as $service) {
                if ($i > 0) {
                    $concept = $concept . '. ' . $service;
                } else {
                    $concept = $service;
                }
                $i++;
            }
            $entity->setConcepto($concept);
            $user = $this->get('security.context')->getToken()->getUser();
            $session = $request->getSession();
            $workspace = $user->getWorkspace();
            if ($session->has('workspace')) {
                $workspace = $em->getRepository('BackendBundle:Workspace')->find($session->get('workspace')->getId());

            }
            if ($reply) {
                $entity->setReply($reply);
                $reply->setWorkspace($workspace);
            }
            $entity->setWorkspace($workspace);

            $entity->setUser($this->get('security.context')->getToken()->getUser());

            $entity->setActivo(true);

            $em->persist($entity);
            $em->flush();

            $workspace->addReceipe($entity);
            $idSisger = str_pad($entity->getId(), 4, "0", STR_PAD_LEFT);
            //$em = $this->getDoctrine()->getManager();
            $entity->setSisgerCode('R-' . $idSisger);
            $em->flush();

            if (array_key_exists('receptor', $_POST)) {
                $banking = $em->getRepository('BackendBundle:Banking')->find($_POST['receptor']);
                $banking->setBalance($banking->getBalance() + $entity->getImporte());
                $entry = new BankingEntry();
                $entry->setDate($entity->getCreationDate());
                $entry->setCredit($entity->getImporte());
                $entry->setDebit(0);
                $entry->setBalance($banking->getBalance());
                $entry->setDetails($entity->getConcepto());
                $entry->setReceipe($entity);
                $em->persist($entry);
                $em->flush();
                $entity->setEntry($entry);
                $banking->addEntry($entry);
                $em->flush();
            }

            $this->generateReceipePDFAction($request, $entity->getId());

        } else {
            $flash = $this->get('session')->getFlashBag();
            $flash->set('danger', 'Ha ocurrido un error en el procesamiento de los datos enviados. Revise que ha completado correctamente todos los campos.');
            return $this->redirect($this->generateUrl('reply_show', array('id' => $reply->getId())));
        }
    }

    public function createExpendationAction(Request $request)
    {
        $entity = new Receipe();
        $form = $this->createCreateForm($entity, 'expendation');
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($form->isValid()) {
            $entity->setType('Gasto');
            $concept = "";
            $i = 0;

            $entity->setConcepto($_POST['concept']);
            $entity->setImporte(0 - $entity->getImporte());
            $user = $this->get('security.context')->getToken()->getUser();

            $entity->setUser($this->get('security.context')->getToken()->getUser());
            $entity->setCreationDate(new \DateTime());
            $entity->setActivo(true);

            $em->persist($entity);
            $em->flush();


            $idSisger = str_pad($entity->getId(), 4, "0", STR_PAD_LEFT);
            //$em = $this->getDoctrine()->getManager();
            $entity->setSisgerCode('R-' . $idSisger);
            $em->flush();

            if (array_key_exists('receptor', $_POST)) {
                $banking = $em->getRepository('BackendBundle:Banking')->find($_POST['receptor']);

                $workspace = $banking->getWorkspace();
                $workspace->addReceipe($entity);
                $entity->setWorkspace($workspace);

                $banking->setBalance($banking->getBalance() + $entity->getImporte());
                $entry = new BankingEntry();
                $entry->setDate($entity->getCreationDate());
                $entry->setCredit(0);
                $entry->setDebit($entity->getImporte());
                $entry->setBalance($banking->getBalance());
                $entry->setDetails($entity->getConcepto());
                $entry->setReceipe($entity);
                $em->persist($entry);
                $em->flush();
                $entity->setEntry($entry);
                $banking->addEntry($entry);
                $em->flush();
            }

            $this->generateReceipePDFAction($request, $entity->getId());

        } else {
            $flash = $this->get('session')->getFlashBag();
            $flash->set('danger', 'Ha ocurrido un error en el procesamiento de los datos enviados. Revise que ha completado correctamente todos los campos.');
            return $this->redirect($this->generateUrl('receipe_expendations'));
        }
    }

    public function createCostAction(Request $request)
    {
        $entity = new Receipe();
        $form = $this->createCreateForm($entity, 'cost');
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $reply = null;
        if (array_key_exists('idReply', $_POST)) {
            $reply = $em->getRepository('BackendBundle:Reply')->findOneBySisgerCode($_POST['idReply']);
            $entity->setReply($reply);
        }
        if ($form->isValid()) {
            $entity->setType('Costo');
            $concept = "";
            $i = 0;
            foreach ($_POST['concept'] as $service) {
                if ($i > 0) {
                    $concept = $concept . '. ' . $service;
                } else {
                    $concept = $service;
                }
                $i++;
            }
            $entity->setConcepto($concept);
            $entity->setImporte(0 - $entity->getImporte());

            $entity->setUser($this->get('security.context')->getToken()->getUser());
            $user = $this->get('security.context')->getToken()->getUser();
            $session = $request->getSession();
            $workspace = $user->getWorkspace();
            if ($session->has('workspace')) {
                $workspace = $em->getRepository('BackendBundle:Workspace')->find($session->get('workspace')->getId());

            }
            if ($reply) {
                $entity->setReply($reply);
                $reply->setWorkspace($workspace);
            }
            $entity->setWorkspace($workspace);
            $entity->setActivo(true);

            $em->persist($entity);
            $em->flush();

            $workspace->addReceipe($entity);
            $idSisger = str_pad($entity->getId(), 4, "0", STR_PAD_LEFT);
            //$em = $this->getDoctrine()->getManager();
            $entity->setSisgerCode('R-' . $idSisger);
            $em->flush();

            if (array_key_exists('receptor', $_POST)) {
                $banking = $em->getRepository('BackendBundle:Banking')->find($_POST['receptor']);
                $banking->setBalance($banking->getBalance() + $entity->getImporte());
                $entry = new BankingEntry();
                $entry->setDate($entity->getCreationDate());
                $entry->setCredit(0);
                $entry->setDebit($entity->getImporte());
                $entry->setBalance($banking->getBalance());
                $entry->setDetails($entity->getConcepto());
                $entry->setReceipe($entity);
                $em->persist($entry);
                $em->flush();
                $entity->setEntry($entry);
                $banking->addEntry($entry);
                $em->flush();
            }

            $this->generateReceipePDFAction($request, $entity->getId());

        } else {
            $flash = $this->get('session')->getFlashBag();
            $flash->set('danger', 'Ha ocurrido un error en el procesamiento de los datos enviados. Revise que ha completado correctamente todos los campos.');
            return $this->redirect($this->generateUrl('reply_show', array('id' => $reply->getId())));
        }
    }

    public function getReceipesAction()
    {

        $receipes = $this->getDoctrine()
            ->getRepository('BackendBundle:Receipe')
            ->createQueryBuilder('a')
            ->select('a')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);


        return new JsonResponse($receipes);
    }

    public function generateReceipePDFAction(Request $request, $receipeId)
    {
        $em = $this->getDoctrine()->getManager();
        $receipe = $em->getRepository('BackendBundle:Receipe')->find($receipeId);
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
            $html2pdf->Output($receipe->getSisgerCode() . '.pdf');
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
        $receipesReport = $em->getRepository('BackendBundle:Receipe')->findReceipesByRange($from, $to);
        $totalImporte = $em->getRepository('BackendBundle:Receipe')->findTotalImporteReceipesByRange($from, $to);

        return $this->render('BackendBundle:Receipe:reporte.html.twig', array(
            'recibos' => $receipesReport,
            'total' => $totalImporte[0][1],
            'from' => $fromString,
            'to' => $toString,
            'type' => 'Pago/Costo',
        ));

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
        $receipesReport = $em->getRepository('BackendBundle:Receipe')->findExpendationsByRange($from, $to);
        $totalImporte = $em->getRepository('BackendBundle:Receipe')->findTotalImporteExpendationsByRange($from, $to);

        return $this->render('BackendBundle:Receipe:reporte.html.twig', array(
            'recibos' => $receipesReport,
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
        $receipesReport = $em->getRepository('BackendBundle:Receipe')->findReceipesByRangeAndWs($from, $to, $ws);
        $totalImporte = $em->getRepository('BackendBundle:Receipe')->findTotalImporteReceipesByRangeAndWs($from, $to, $ws);

        return $this->render('BackendBundle:Receipe:reporte.html.twig', array(
            'recibos' => $receipesReport,
            'total' => $totalImporte[0][1],
            'from' => $fromString,
            'to' => $toString,

        ));

    }


    public function generateReceipePDF1Action($receipeId)
    {
        $em = $this->getDoctrine()->getManager();
        $receipe = $em->getRepository('BackendBundle:Receipe')->find($receipeId);
        $image = $this->container->getParameter('belraysa.route.logos') . "logo-5678c0e74c6f5-foto1.jpg";
        $data = PDFController::ExportReceipeAsPDFAction($receipe, $image);
        return new Response($data, 200, array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="recipe_' . $receipe->getId() . '.pdf"'));
    }

    /**
     * Creates a form to create a Receipe entity.
     *
     * @param Receipe $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Receipe $entity, $type)
    {
        if ($type == 'income') {
            $form = $this->createForm(new IncomeType(), $entity, array(
                'action' => $this->generateUrl('receipe_create_income'),
                'method' => 'POST',
            ));
        } elseif ($type == 'expendation') {
            $form = $this->createForm(new ExpendationType(), $entity, array(
                'action' => $this->generateUrl('receipe_create_expendation'),
                'method' => 'POST',
            ));
        } else {
            $form = $this->createForm(new CostType(), $entity, array(
                'action' => $this->generateUrl('receipe_create_cost'),
                'method' => 'POST',
            ));
        }

        //    $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Receipe entity.
     *
     */
    public function newIncomeAction(Request $request, $idReply)
    {
        $entity = new Receipe();

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


        return $this->render('BackendBundle:Receipe:newIncome.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'services' => $em->getRepository('BackendBundle:Service')->findAll(),
            'workspace' => $workspace
        ));
    }

    public function newExpendationAction(Request $request)
    {
        $entity = new Receipe();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createCreateForm($entity, 'expendation');

        return $this->render('BackendBundle:Receipe:newExpendation.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'services' => $em->getRepository('BackendBundle:Service')->findAll(),
            'bankings' => $em->getRepository('BackendBundle:Banking')->findAll()
        ));
    }

    public function newCostAction(Request $request, $idReply)
    {
        $entity = new Receipe();

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

        return $this->render('BackendBundle:Receipe:newCost.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'services' => $em->getRepository('BackendBundle:Service')->findAll(),
            'workspace' => $workspace
        ));
    }

    /**
     * Finds and displays a Receipe entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Receipe')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Receipe entity.');
        }

        return $this->render('BackendBundle:Receipe:show.html.twig', array(
            'entity' => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing Receipe entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Receipe')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Receipe entity.');
        }

        $editForm = $this->createEditForm($entity);


        return $this->render('BackendBundle:Receipe:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),

        ));
    }

    /**
     * Creates a form to edit a Receipe entity.
     *
     * @param Receipe $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Receipe $entity)
    {
        $form = $this->createForm(new ReceipeType(), $entity, array(
            'action' => $this->generateUrl('receipe_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Receipe entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Receipe')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Receipe entity.');
        }


        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('receipe_edit', array('id' => $id)));
        }

        return $this->render('BackendBundle:Receipe:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),

        ));
    }

    /**
     * Deletes a Receipe entity.
     *
     */
    public function cancelAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Receipe')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Receipe entity.');
        }

        $entity->setActivo(false);
        $em->flush();

        if ($entity->getType() == 'Gasto') {
            return $this->redirect($this->generateUrl('receipe_expendations'));
        } else {
            return $this->redirect($this->generateUrl('receipe', array('workspace' => $entity->getWorkspace()->getId())));
        }
    }

    public function activateAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Receipe')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Receipe entity.');
        }

        $entity->setActivo(true);
        $em->flush();


        if ($entity->getType() == 'Gasto') {
            return $this->redirect($this->generateUrl('receipe_expendations'));
        } else {
            return $this->redirect($this->generateUrl('receipe', array('workspace' => $entity->getWorkspace()->getId())));
        }
    }


}
