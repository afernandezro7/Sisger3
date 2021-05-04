<?php

namespace Belraysa\BackendBundle\Controller;

use Belraysa\BackendBundle\Entity\Anexo;
use Belraysa\BackendBundle\Entity\PrecioVenta;
use Belraysa\BackendBundle\Entity\Receipe;
use Belraysa\BackendBundle\Entity\Voucher;
use Belraysa\BackendBundle\Form\ReceipeType;
use Belraysa\BackendBundle\Form\VoucherType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Belraysa\BackendBundle\Controller\FPDF\PDFController;
use Belraysa\BackendBundle\Entity\Reply;
use Belraysa\BackendBundle\Form\ReplyType;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\HttpFoundation\JsonResponse;
use HTML2PDF;

/**
 * Reply controller.
 *
 */
class ReplyController extends Controller
{

    /**
     * Lists all Reply entities.
     *
     */

    public function indexAllAction()
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();

        $userEntities = $em->getRepository('BackendBundle:Reply')->findRepliesByUser($user);
        $notUserEntities = $em->getRepository('BackendBundle:Reply')->findRepliesWithNoUser($user);

        $ordenadas = $em->getRepository('BackendBundle:Reply')->findRepliesOrderAsc();
        $anno = $ordenadas[0]->getBeginDate()->format('Y');
        $now = new \DateTime();

        $annoActual = $now->format('Y');
        $annos = array();
        for ($i = $anno; $i <= $annoActual; $i++) {
            $annos[] = $i;
        }


        $entities = array_merge($userEntities, $notUserEntities);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);

        //convertir todos los lideres y clientes de los expedientes en precios de venta
        /*  foreach ($entities as $expediente) {

              $lider = $expediente->getLeader();
              $precioVentaLider = new PrecioVenta();
              $precioVentaLider->setCliente($lider);
              $precioVentaLider->setPrecio(0);
              $em->persist($precioVentaLider);
              $em->flush();
              $expediente->addPrecioVentaLider($precioVentaLider);
              foreach ($expediente->getCustomers() as $cliente) {
                  if (!$expediente->hasCliente($cliente->getId()) && !$expediente->hasLider($cliente->getId())) {
                      $precioVenta = new PrecioVenta();
                      $precioVenta->setCliente($cliente);
                      $precioVenta->setPrecio(0);
                      $em->persist($precioVenta);
                      $em->flush();
                      $expediente->addPrecioVenta($precioVenta);
                  }
              }

              //poner precio a los expedientes que no lo tienen
              if (!$expediente->getPrice()) {
                  $expediente->setPrice(0);
              }
              $em->flush();

          }*/

        return $this->render('BackendBundle:Reply:indexAll.html.twig', array(
            'entities' => $pagination,
            'checked' => true,
            'annos' => $annos,
        ));
    }


    public function indexAction($ws)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();

        $ws = $em->getRepository('BackendBundle:Workspace')->findOneByName($ws);
        $userEntities = $em->getRepository('BackendBundle:Reply')->findRepliesByUserWS($user, $ws);

        $notUserEntities = $em->getRepository('BackendBundle:Reply')->findRepliesWithNoUserWS($user, $ws);


        $ordenadas = $em->getRepository('BackendBundle:Reply')->findRepliesOrderAsc();
        $anno = $ordenadas[0]->getBeginDate()->format('Y');
        $now = new \DateTime();

        $annoActual = $now->format('Y');

        for ($i = $anno; $i <= $annoActual; $i++) {
            $annos[] = $i;
        }


        $entities = array_merge($userEntities, $notUserEntities);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);


        return $this->render('BackendBundle:Reply:index.html.twig', array(
            'entities' => $pagination,
            'checked' => true,
            'annos' => $annos,
            'ws' => $ws
        ));
    }


    public function indexByUserAction($id, $ws)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('BackendBundle:User')->findOneById($id);
        $entities = $em->getRepository('BackendBundle:Reply')->findByUser($user);
        $anno = $entities[0]->getBeginDate()->format('Y');
        $now = new \DateTime();

        $annoActual = $now->format('Y');

        for ($i = $anno; $i <= $annoActual; $i++) {
            $annos[] = $i;
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);

        return $this->render('BackendBundle:Reply:index.html.twig', array(
            'entities' => $pagination,
            'checked' => false,
            'annos' => $annos,
            'ws' => $ws
        ));
    }

    public function indexByClientAction($id, $ws)
    {
        $em = $this->getDoctrine()->getManager();

        $client = $em->getRepository('BackendBundle:Client')->findOneById($id);

        $user = $this->get('security.context')->getToken()->getUser();
        $replies = array();
        foreach ($user->getWorkspaces() as $ws) {
            $replies = array_merge($replies, $em->getRepository('BackendBundle:Reply')->findRepliesByWorkspaceOrderAsc($ws));
        }

        $entities = array();
        foreach ($replies as $reply) {
            if ($reply->hasClient($client) or $reply->getLeader() == $client) {
                $entities[] = $reply;
            }
        }
        $anno = $entities[0]->getBeginDate()->format('Y');
        $now = new \DateTime();

        $annoActual = $now->format('Y');

        for ($i = $anno; $i <= $annoActual; $i++) {
            $annos[] = $i;
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);

        return $this->render('BackendBundle:Reply:index.html.twig', array(
            'entities' => $pagination,
            'checked' => false,
            'annos' => $annos,
            'ws' => $ws,
        ));
    }

    /**
     * Creates a new Reply entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Reply();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {


            //Getting current user's reservations from database
            $em = $this->getDoctrine()->getManager();

            $workspace = $em->getRepository('BackendBundle:Workspace')->find($_POST['workspace']);
            if ($workspace->getName() == 'G-BRS') {
                $workspace = $em->getRepository('BackendBundle:Workspace')->findOneByName('AAVV');
            }

            //Getting current user
            $user = $this->get('security.context')->getToken()->getUser();
            $entity->setUser($user);

            $entity->setPax(sizeof($entity->getPreciosVenta()) + 1);

            $begin = $entity->getBeginDate();
            $entity->setMonth($begin->format('m'));
            $entity->setYear($begin->format('Y'));

            $em->persist($entity);
            $em->flush();

            //calcular precio expediente como suma de todos los precios de venta
            $precio = 0;
            foreach ($entity->getPreciosVenta() as $precioVenta) {
                $precio = $precio + $precioVenta->getPrecio();
                $precioVenta->setExpediente($entity);
            }
            $precio = $precio + $entity->getPrecioVentaLider()[0]->getPrecio();
            $entity->setPrice($precio);

            $idSisger = str_pad($entity->getId(), 3, "0", STR_PAD_LEFT);
            //$em = $this->getDoctrine()->getManager();
            $entity->setSisgerCode('E-' . $idSisger);

            $em->flush();

            $workspace->addReply($entity);
            $em->flush();


            return $this->redirect($this->generateUrl('reply_show', array('id' => $entity->getId())));
        }

        return $this->render('BackendBundle:Reply:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Reply entity.
     *
     * @param Reply $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private
    function createCreateForm(Reply $entity)
    {
        $form = $this->createForm(new ReplyType(), $entity, array(
            'action' => $this->generateUrl('reply_create'),
            'method' => 'POST',
        ));

        //  $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Reply entity.
     *
     */
    public
    function newAction()
    {
        $entity = new Reply();
        $form = $this->createCreateForm($entity);
        $em = $this->getDoctrine()->getManager();
        return $this->render('BackendBundle:Reply:invoice.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),

        ));
    }

    /**
     * Finds and displays a Reply entity.
     *
     */
    public
    function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Reply')->find($id);
        $services = $em->getRepository('BackendBundle:Service')->findAll();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Reply entity.');
        }

        /*   $receipe = new Receipe();

           $receipe->setReply($entity);

           $form_receipe = $this->createForm(new ReceipeType(), $receipe, array(
               'action' => $this->generateUrl('receipe_create'),
               'method' => 'POST',
           ));

           $voucher = new Voucher();

           $voucher->setReply($entity);

           $form_voucher = $this->createForm(new VoucherType(), $voucher, array(
               'action' => $this->generateUrl('voucher_create'),
               'method' => 'POST',
           ));
   */
        return $this->render('BackendBundle:Reply:show.html.twig', array(
            'reply' => $entity,
            'services' => $services,
            'noches' => $entity->getBeginDate()->diff($entity->getFinishDate())->days
            /*  'form_receipe' => $form_receipe->createView(),
              'form_voucher' => $form_voucher->createView()*/
        ));
    }

    public
    function printAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Reply')->find($id);
        $services = $em->getRepository('BackendBundle:Service')->findAll();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Reply entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Reply:invoice-print.html.twig', array(
            'reply' => $entity,
            'services' => $services,
            'noches' => $entity->getBeginDate()->diff($entity->getFinishDate())->days,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public
    function voucherAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $reply = $em->getRepository('BackendBundle:Reply')->find($id);
        $image = $this->container->getParameter('belraysa.route.logos') . "logo-5678c0e74c6f5-foto1.jpg";
        $data = PDFController::ExportVoucherAsPDFAction($reply, $image);
        return new Response($data, 200, array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="voucher_' . $reply->getId() . '.pdf"'));

    }

    public function getLengthAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:Reply')->findAll();
        $length = sizeof($entities);

        return new JsonResponse("{$length}");
    }

    public
    function ReceipeAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $reply = $em->getRepository('BackendBundle:Reply')->find($id);
        $image = $this->container->getParameter('belraysa.route.logos') . "logo-5678c0e74c6f5-foto1.jpg";
        $data = PDFController::ExportReceipeAsPDFAction($reply, $image);
        return new Response($data, 200, array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="receipe_' . $reply->getId() . '.pdf"'));

    }

    /**
     * Displays a form to edit an existing Reply entity.
     *
     */
    public
    function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Reply')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Reply entity.');
        }

        $beginDate = $entity->getBeginDate();
        $beginDate = date_format($beginDate, 'd/m/Y');
        $finishDate = $entity->getFinishDate();
        $finishDate = date_format($finishDate, 'd/m/Y');
        //$phones = $entity->getClient()->getPhones();
        //$email = $entity->getClient()->getEmail();
        //print_r($beginDate);
        //die;

        $editForm = $this->createEditForm($entity);

        return $this->render('BackendBundle:Reply:edit.html.twig', array(
            'entity' => $entity,
            'beginDate' => $beginDate,
            'finishDate' => $finishDate,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Reply entity.
     *
     * @param Reply $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private
    function createEditForm(Reply $entity)
    {
        $form = $this->createForm(new ReplyType(), $entity, array(
            'action' => $this->generateUrl('reply_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Reply entity.
     *
     */
    public
    function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Reply')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Reply entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            //Getting current user's reservations from database
            $em = $this->getDoctrine()->getManager();
            //Getting current user
            //$user = $this->get('security.context')->getToken()->getUser();
            //$entity->setUser($user);
            $begin = $entity->getBeginDate();
            $entity->setMonth($begin->format('m'));
            $entity->setYear($begin->format('Y'));

            $em->flush();
            $entity->setPax(sizeof($entity->getPreciosVenta()) + 1);

            //calcular precio expediente como suma de todos los precios de venta
            $precio = 0;
            foreach ($entity->getPreciosVenta() as $precioVenta) {
                $precio = $precio + $precioVenta->getPrecio();
                $precioVenta->setExpediente($entity);
            }
            $preciosDeVentaDelExpediente = $em->getRepository('BackendBundle:PrecioVenta')->findByExpediente($entity->getId());
            foreach ($preciosDeVentaDelExpediente as $precioVentaOriginal) {
                $flag = false;
                foreach ($entity->getPreciosVenta() as $precioVenta) {
                   if($precioVentaOriginal->getId() == $precioVenta->getId()){
                       $flag = true;
                       break;
                   }
                }
                if(!$flag){
                    $em->remove($precioVentaOriginal);
                    $em->flush();
                }
            }

            $precio = $precio + $entity->getPrecioVentaLider()[0]->getPrecio();
            $entity->setPrice($precio);

            $em->flush();

            return $this->redirect($this->generateUrl('reply_show', array('id' => $entity->getId())));
        } else {
            print_r($_POST);
            die;
        }

        return $this->render('BackendBundle:Reply:edit.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a Reply entity.
     *
     */
    public
    function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Reply')->find($id);
        $ws = $entity->getWorkspace()->getName();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Reply entity.');
        }
        $workspace = $entity->getWorkspace();
        $session = $request->getSession();
        $session->set('workspace', $workspace);
        $em->remove($entity);
        $em->flush();

        $flash = $this->get('session')->getFlashBag();
        $flash->set('success', 'El expediente ha sido eliminado satisfactoriamente.');

        return $this->redirect($this->generateUrl('reply', array('ws' => $ws)));
    }

    /**
     * Creates a form to delete a Reply entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private
    function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('reply_delete', array('id' => $id)))
            ->setMethod('DELETE')
            // ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    public function batchDeleteAction(Request $request, $ws)
    {
        $em = $this->getDoctrine()->getManager();
        $ws = $em->getRepository('BackendBundle:Workspace')->find($ws)->getName();

        $session = $request->getSession();
        $session->set('workspace', $ws);

        $ids = $request->get('batch_action_checkbox');
        if ($ids) {

            foreach ($ids as $id) {
                $entity = $em->getRepository('BackendBundle:Reply')->find($id);
                if ($entity) {
                    $em->remove($entity);
                }
            }
            $em->flush();
            $flash = $this->get('session')->getFlashBag();
            $flash->set('success', 'Los expedientes han sido eliminados satisfactoriamente.');
        }
        return $this->redirect($this->generateUrl('reply', array('ws' => $ws)));
    }

    public function batchDeleteAllAction(Request $request)
    {

        $ids = $request->get('batch_action_checkbox');
        if ($ids) {
            $em = $this->getDoctrine()->getManager();
            foreach ($ids as $id) {
                $entity = $em->getRepository('BackendBundle:Reply')->find($id);
                if ($entity) {
                    $em->remove($entity);
                }
            }
            $em->flush();
            $flash = $this->get('session')->getFlashBag();
            $flash->set('success', 'Los expedientes han sido eliminados satisfactoriamente.');
        }
        return $this->redirect($this->generateUrl('reply'));
    }

    public function getRepliesAction()
    {

        $replies = $this->getDoctrine()
            ->getRepository('BackendBundle:Reply')
            ->createQueryBuilder('a')
            ->select('a')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);


        return new JsonResponse($replies);
    }

    public function getLengthByOperatorAction()
    {
        if (array_key_exists('range', $_POST)) {
            $range = $_POST['range'];

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
        $operators = $em->getRepository('BackendBundle:User')->findAll();

        $repliesByOperatorArray = array();

        foreach ($operators as $operator) {
            $repliesByOperatorArray[] = sizeof($em->getRepository('BackendBundle:Reply')->findRepliesByUserAndMonth($operator, $date1, $date2));
        }

        foreach ($repliesByOperatorArray as $val) {
            $json[] = sprintf('%s', (is_string($val) ? $val : json_encode($val)));
        }
        return new JsonResponse($json);
    }

    public function getLengthByOperatorWSAction()
    {
        if (array_key_exists('range', $_POST)) {
            $range = $_POST['range'];

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
        $operators = $em->getRepository('BackendBundle:User')->findByWorkspace($_POST['ws']);

        $repliesByOperatorArray = array();

        foreach ($operators as $operator) {
            $repliesByOperatorArray[] = sizeof($em->getRepository('BackendBundle:Reply')->findRepliesByUserAndMonth($operator, $date1, $date2));
        }

        $json = array();

        foreach ($repliesByOperatorArray as $val) {
            $json[] = sprintf('%s', (is_string($val) ? $val : json_encode($val)));
        }
        return new JsonResponse($json);
    }

    public function getRepliesByMonthYearAction($month, $year)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();
        if ($user->getRole()->getName() == "ROLE_SUPER_ADMIN") {
            $replies = $em->getRepository('BackendBundle:Reply')->findRepliesByMonthOfYear($month, $year);
        } else {
            $replies = $em->getRepository('BackendBundle:Reply')->findRepliesByMonthOfYearWithUser($month, $year, $user);
        }

        $ordenadas = $em->getRepository('BackendBundle:Reply')->findRepliesOrderAsc();
        $anno = $ordenadas[0]->getBeginDate()->format('Y');
        $now = new \DateTime();

        $annoActual = $now->format('Y');

        for ($i = $anno; $i <= $annoActual; $i++) {
            $annos[] = $i;
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $replies,
            $this->get('request')->query->get('page', 1),
            10);

        return $this->render('BackendBundle:Reply:filteredAll.html.twig', array(
            'entities' => $pagination,
            'checked' => true,
            'annos' => $annos

        ));
    }

    public function getRepliesByMonthYearAndWSAction($month, $year, $ws)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();
        if ($user->getRole()->getName() == "ROLE_SUPER_ADMIN") {
            $replies = $em->getRepository('BackendBundle:Reply')->findRepliesByMonthOfYearAndWS($month, $year, $ws);
        } else {
            $replies = $em->getRepository('BackendBundle:Reply')->findRepliesByMonthOfYearWithUserAndWS($month, $year, $user, $ws);
        }

        $ordenadas = $em->getRepository('BackendBundle:Reply')->findRepliesOrderAsc();
        $anno = $ordenadas[0]->getBeginDate()->format('Y');
        $now = new \DateTime();

        $annoActual = $now->format('Y');

        for ($i = $anno; $i <= $annoActual; $i++) {
            $annos[] = $i;
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $replies,
            $this->get('request')->query->get('page', 1),
            10);

        return $this->render('BackendBundle:Reply:filtered.html.twig', array(
            'entities' => $pagination,
            'checked' => true,
            'ws' => $em->getRepository('BackendBundle:Workspace')->find($ws),
            'annos' => $annos

        ));
    }

    public function getRepliesByYearAction($year)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();
        if ($user->getRole()->getName() == "ROLE_SUPER_ADMIN") {
            $replies = $em->getRepository('BackendBundle:Reply')->findRepliesByYear($year);
        } else {
            $replies = $em->getRepository('BackendBundle:Reply')->findRepliesByYearWithoutUser($year, $user);
        }

        $ordenadas = $em->getRepository('BackendBundle:Reply')->findRepliesOrderAsc();
        $anno = $ordenadas[0]->getBeginDate()->format('Y');
        $now = new \DateTime();

        $annoActual = $now->format('Y');

        for ($i = $anno; $i <= $annoActual; $i++) {
            $annos[] = $i;
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $replies,
            $this->get('request')->query->get('page', 1),
            10);

        return $this->render('BackendBundle:Reply:filteredAll.html.twig', array(
            'entities' => $pagination,
            'checked' => true,
            'annos' => $annos

        ));
    }

    public function getRepliesByYearAndWSAction($year, $ws)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();
        if ($user->getRole()->getName() == "ROLE_SUPER_ADMIN") {
            $replies = $em->getRepository('BackendBundle:Reply')->findRepliesByYearAndWS($year, $ws);
        } else {
            $replies = $em->getRepository('BackendBundle:Reply')->findRepliesByYearWithoutUserAndWS($year, $user, $ws);
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $replies,
            $this->get('request')->query->get('page', 1),
            10);

        $ordenadas = $em->getRepository('BackendBundle:Reply')->findRepliesOrderAsc();
        $anno = $ordenadas[0]->getBeginDate()->format('Y');
        $now = new \DateTime();

        $annoActual = $now->format('Y');

        for ($i = $anno; $i <= $annoActual; $i++) {
            $annos[] = $i;
        }

        return $this->render('BackendBundle:Reply:filtered.html.twig', array(
            'entities' => $pagination,
            'checked' => true,
            'ws' => $em->getRepository('BackendBundle:Workspace')->find($ws),
            'annos' => $annos
        ));
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
        $repliesReport = $em->getRepository('BackendBundle:Reply')->findRepliesByRange($from, $to);
        // $expenditures = $em->getRepository('BackendBundle:Receipe')->findExpendituresByRange($from, $to);

        // print_r($totalImporte);
        // die;
        //    print_r($totalPax);
        //    die;
        /*
        ob_start();
        include(dirname(__FILE__) . '/Reportes/ExpedientesReport.php');

        $content = ob_get_clean();
        //print_r($content);
        //die;
        // convert in PDF
        require_once(dirname(__FILE__) . '/html2pdf/vendor/autoload.php');
        try {
            $html2pdf = new HTML2PDF('L', 'A4', 'fr', true, 'UTF-8', array(5, 5, 5, 8));
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
            $html2pdf->Output($fromString . ' - ' . $toString . ' - ' . 'Expedientes' . '.pdf');
        } catch (HTML2PDF_exception $e) {
            echo $e;
            exit;
        }
        */
        return $this->render('BackendBundle:Reply:reporte.html.twig', array(
            'expedientes' => $repliesReport,
            'from' => $fromString,
            'to' => $toString,
            //'expenditures' => $expenditures,

        ));
    }

    public function xlsAction()
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
        $repliesReport = $em->getRepository('BackendBundle:Reply')->findRepliesByRange($from, $to);

        // get the XLS
//aqui se ejecuta el codigo del reader
        $uploaddir = $this->container->getParameter('belraysa.route.templates');
        $uploadfile = $uploaddir . basename('Expedientes.xlsx');

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
            ->setCellValue('E3', date_format(new \DateTime(), 'd/m/Y'));

        $i = 6;
        $indice = 1;
        $style = $objPHPExcel->getActiveSheet()->getStyle('A6');
        foreach ($repliesReport as $expediente) {
            if ($indice > 6) {
                $objPHPExcel->getActiveSheet()->duplicateStyle($style,
                    'A' . $indice . ':J' . $indice
                );
            }
            $lider = '';
            if (array_key_exists('0', $expediente->getPrecioVentaLider())) {
                $lider = $expediente->getPrecioVentaLider()[0]->getCliente()->getFirstName() . ' ' . $expediente->getPrecioVentaLider()[0]->getCliente()->getLastName
                    ();
            }


            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indice, $i)
                ->setCellValue('B' . $indice, $expediente->getSisgerCode())
                ->setCellValue('C' . $indice, $expediente->getUser()->getLetra())
                ->setCellValue('D' . $indice, $lider)
                ->setCellValue('E' . $indice, date_format($expediente->getBeginDate(), 'd/m/Y') . ' - ' . date_format($expediente->getFinishDate(), 'd/m/Y'));
            $suma = 0;
            foreach ($expediente->getRecibos() as $recibo) {
                if ($recibo->isActivo()) {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('F' . $indice, $recibo->getSisgerCode())
                        ->setCellValue('G' . $recibo->getImporte() . ' USD');
                    $suma = $suma + $recibo->getImporte();
                    $indice += 1;
                }
            }
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('F' . $indice, 'TOTAL')
                ->setCellValue('G' . $indice, $suma . ' USD');
            $indice += 1;
            $servicios = '';
            $s = 1;
            foreach ($expediente->getServices() as $service) {
                if ($s != 1) {
                    $servicios = $servicios . ', ' . $service->getName();
                } else {
                    $servicios = $service->getName();
                }
            }
            $indice += 1;
            $i += 1;
        }


// Create new PHPExcel object


// Set document properties
        $objPHPExcel->getProperties()->setCreator("BELRAYSA TOURS & TRAVEL GROUP S.A")
            ->setLastModifiedBy("BELRAYSA TOURS & TRAVEL GROUP S.A")
            ->setTitle("REPORTE_DE_EXPEDIENTES_" . date_format(new \DateTime(), 'd/m/Y') . '_BELRAYSA TOURS & TRAVEL GROUP S.A')
            ->setSubject("REPORTE_DE_EXPEDIENTES_" . date_format(new \DateTime(), 'd/m/Y') . '_BELRAYSA TOURS & TRAVEL GROUP S.A')
            ->setDescription("REPORTE_DE_EXPEDIENTES_" . date_format(new \DateTime(), 'd/m/Y') . '_BELRAYSA TOURS & TRAVEL GROUP S.A')
            ->setKeywords("EXPEDIENTES ")
            ->setCategory("REPORTE_DE_EXPEDIENTES_" . date_format(new \DateTime(), 'd/m/Y') . '_BELRAYSA TOURS & TRAVEL GROUP S.A');


// Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="EXPEDIENTES_BELRAYSA TOURS & TRAVEL GROUP S.A.xlsx"');
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

    public function generateReportByWSAction($ws)
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
        $repliesReport = $em->getRepository('BackendBundle:Reply')->findRepliesByRangeAndWS($from, $to, $ws);

        // print_r($totalImporte);
        // die;
        //    print_r($totalPax);
        //    die;
        /*
        ob_start();
        include(dirname(__FILE__) . '/Reportes/ExpedientesReport.php');

        $content = ob_get_clean();
        //print_r($content);
        //die;
        // convert in PDF
        require_once(dirname(__FILE__) . '/html2pdf/vendor/autoload.php');
        try {
            $html2pdf = new HTML2PDF('L', 'A4', 'fr', true, 'UTF-8', array(5, 5, 5, 8));
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
            $html2pdf->Output($fromString . ' - ' . $toString . ' - ' . 'Expedientes' . '.pdf');
        } catch (HTML2PDF_exception $e) {
            echo $e;
            exit;
        }
        */
        return $this->render('BackendBundle:Reply:reporte.html.twig', array(
            'expedientes' => $repliesReport,
            'from' => $fromString,
            'to' => $toString,

        ));
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


        $lider = null;
        $user = null;


        $ws = null;
        if (array_key_exists('reply_entorno', $_GET)) {
            if ($_GET['reply_entorno'] != '') {
                $ws = $_GET['reply_entorno'];
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

        if (array_key_exists('reply_sisgerCode', $_GET)) {
            if ($_GET['reply_sisgerCode'] != '') {
                $sisgerCode = $_GET['reply_sisgerCode'];
            }
        }

        if (array_key_exists('reply_from', $_GET)) {
            if ($_GET['reply_from'] != '') {
                $date1 = date_create_from_format('d/m/Y', $_GET['reply_from']);
                $from = new \DateTime($date1->format("Y-m-d") . " 00:00:00");
            }
        }

        if (array_key_exists('reply_to', $_GET)) {
            if ($_GET['reply_to'] != '') {
                $date2 = date_create_from_format('d/m/Y', $_GET['reply_to']);
                $to = new \DateTime($date2->format("Y-m-d") . " 23:59:59");
            }
        }

        if (array_key_exists('reply_lider', $_GET)) {
            if ($_GET['reply_lider'] != '') {
                $lider = $_GET['reply_lider'];
            }
        }
        if (array_key_exists('reply_user', $_GET)) {
            if ($_GET['reply_user'] != '') {
                $user = $_GET['reply_user'];
            }
        }

        $expedientes = $em->getRepository('BackendBundle:Reply')->findBusquedaAvanzada(true, $aavv, $c507, $lbrs, $sisgerCode, $from, $to, $lider, $user);
        if (array_key_exists('buscar', $_GET)) {
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $expedientes,
                $this->get('request')->query->get('page', 1),
                10);

            if ($from) {
                $from = date_format($from, 'd/m/Y');
            }
            if ($to) {
                $to = date_format($to, 'd/m/Y');
            }

            return $this->render('BackendBundle:Reply:filteredAll.html.twig', array(
                'entities' => $pagination,
                'aavv' => $aavv,
                'c507' => $c507,
                'lbrs' => $lbrs,
                'sisgerCode' => $sisgerCode,
                'from' => $from,
                'to' => $to,
                'lider' => $lider,
                'user' => $user,
                'entorno' => $ws
            ));
        } elseif (array_key_exists('pdf', $_GET)) {
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
            return $this->render('BackendBundle:Reply:reporte.html.twig', array(
                'expedientes' => $expedientes,
                'range' => $range

            ));
        } else {
            // get the XLS
//aqui se ejecuta el codigo del reader
            $uploaddir = $this->container->getParameter('belraysa.route.templates');
            $uploadfile = $uploaddir . basename('Expedientes.xlsx');

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
                ->setCellValue('E3', date_format(new \DateTime(), 'd/m/Y'));

            $i = 1;
            $indice = 6;
            $style = $objPHPExcel->getActiveSheet()->getStyle('A6');
            foreach ($expedientes as $expediente) {
                if ($indice > 6) {
                    $objPHPExcel->getActiveSheet()->duplicateStyle($style,
                        'A' . $indice . ':J' . $indice
                    );
                }
                $lider = '';
                if ($expediente->getPrecioVentaLider()[0]) {
                    $lider = $expediente->getPrecioVentaLider()[0]->getCliente()->getFirstName() . ' ' . $expediente->getPrecioVentaLider()[0]->getCliente()->getLastName();
                }


                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $indice, $i)
                    ->setCellValue('B' . $indice, $expediente->getSisgerCode())
                    ->setCellValue('C' . $indice, $expediente->getUser()->getLetra())
                    ->setCellValue('D' . $indice, $lider)
                    ->setCellValue('E' . $indice, date_format($expediente->getBeginDate(), 'd/m/Y') . ' - ' . date_format($expediente->getFinishDate(), 'd/m/Y'));
                $servicios = '';
                $s = 1;
                foreach ($expediente->getServices() as $service) {
                    if ($s != 1) {
                        $servicios = $servicios . ', ' . $service->getName();
                    } else {
                        $servicios = $servicios . $service->getName();
                    }
                    $s += 1;
                }
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('G' . $indice, $servicios);
                $suma = 0;
                foreach ($expediente->getRecibos() as $recibo) {
                    if ($recibo->isActivo()) {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('F' . $indice, $recibo->getSisgerCode() . ' | ' . $recibo->getImporte() . ' USD');
                        $suma = $suma + $recibo->getImporte();
                        $indice += 1;

                    }
                }
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('F' . $indice, 'TOTAL | ' . $suma . ' USD');

                $indice += 1;


                $i += 1;
            }


// Create new PHPExcel object


// Set document properties
            $objPHPExcel->getProperties()->setCreator("BELRAYSA TOURS & TRAVEL GROUP S.A")
                ->setLastModifiedBy("BELRAYSA TOURS & TRAVEL GROUP S.A")
                ->setTitle("REPORTE_DE_EXPEDIENTES_" . date_format(new \DateTime(), 'd/m/Y') . '_BELRAYSA TOURS & TRAVEL GROUP S.A')
                ->setSubject("REPORTE_DE_EXPEDIENTES_" . date_format(new \DateTime(), 'd/m/Y') . '_BELRAYSA TOURS & TRAVEL GROUP S.A')
                ->setDescription("REPORTE_DE_EXPEDIENTES_" . date_format(new \DateTime(), 'd/m/Y') . '_BELRAYSA TOURS & TRAVEL GROUP S.A')
                ->setKeywords("EXPEDIENTES ")
                ->setCategory("REPORTE_DE_EXPEDIENTES_" . date_format(new \DateTime(), 'd/m/Y') . '_BELRAYSA TOURS & TRAVEL GROUP S.A');


// Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="EXPEDIENTES_BELRAYSA TOURS & TRAVEL GROUP S.A.xlsx"');
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

    public function filtrarPorWSAction($ws)
    {
        $em = $this->getDoctrine()->getManager();

        $aavv = null;
        $c507 = null;
        $lbrs = null;

        $from = null;
        $to = null;

        $sisgerCode = null;

        $mes = null;
        $anno = null;
        $lider = null;
        $user = null;


        if ($ws == 'AAVV') {
            $aavv = $em->getRepository('BackendBundle:Workspace')->findOneByName($ws);
        }
        if ($ws == 'C507') {
            $c507 = $em->getRepository('BackendBundle:Workspace')->findOneByName($ws);
        }
        if ($ws == 'L-BRS') {
            $lbrs = $em->getRepository('BackendBundle:Workspace')->findOneByName($ws);
        }

        if (array_key_exists('reply_sisgerCode', $_GET)) {
            if ($_GET['reply_sisgerCode'] != '') {
                $sisgerCode = $_GET['reply_sisgerCode'];
            }
        }

        if (array_key_exists('reply_from', $_GET)) {
            if ($_GET['reply_from'] != '') {
                $date1 = date_create_from_format('d/m/Y', $_GET['reply_from']);
                $from = new \DateTime($date1->format("Y-m-d") . " 00:00:00");
            }
        }

        if (array_key_exists('reply_to', $_GET)) {
            if ($_GET['reply_to'] != '') {
                $date2 = date_create_from_format('d/m/Y', $_GET['reply_to']);
                $to = new \DateTime($date2->format("Y-m-d") . " 23:59:59");
            }
        }

        if (array_key_exists('reply_lider', $_GET)) {
            if ($_GET['reply_lider'] != '') {
                $lider = $_GET['reply_lider'];
            }
        }
        if (array_key_exists('reply_user', $_GET)) {
            if ($_GET['reply_user'] != '') {
                $user = $_GET['reply_user'];
            }
        }

        $expedientes = $em->getRepository('BackendBundle:Reply')->findBusquedaAvanzada(true, $aavv, $c507, $lbrs, $sisgerCode, $from, $to, $lider, $user);
        if (array_key_exists('buscar', $_GET)) {
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $expedientes,
                $this->get('request')->query->get('page', 1),
                10);

            if ($from) {
                $from = date_format($from, 'd/m/Y');
            }
            if ($to) {
                $to = date_format($to, 'd/m/Y');
            }

            return $this->render('BackendBundle:Reply:filtered.html.twig', array(
                'entities' => $pagination,
                'aavv' => $aavv,
                'c507' => $c507,
                'lbrs' => $lbrs,
                'sisgerCode' => $sisgerCode,
                'from' => $from,
                'to' => $to,
                'lider' => $lider,
                'user' => $user,
                'entorno' => $ws
            ));
        } elseif (array_key_exists('pdf', $_GET)) {
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
            return $this->render('BackendBundle:Reply:reporte.html.twig', array(
                'expedientes' => $expedientes,
                'range' => $range

            ));
        } else {
            // get the XLS
//aqui se ejecuta el codigo del reader
            $uploaddir = $this->container->getParameter('belraysa.route.templates');
            $uploadfile = $uploaddir . basename('Expedientes.xlsx');

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
                ->setCellValue('E3', date_format(new \DateTime(), 'd/m/Y'));

            $i = 1;
            $indice = 6;
            $style = $objPHPExcel->getActiveSheet()->getStyle('A6');
            foreach ($expedientes as $expediente) {
                if ($indice > 6) {
                    $objPHPExcel->getActiveSheet()->duplicateStyle($style,
                        'A' . $indice . ':J' . $indice
                    );
                }
                $lider = '';
                if ($expediente->getPrecioVentaLider()[0]) {
                    $lider = $expediente->getPrecioVentaLider()[0]->getCliente()->getFirstName() . ' ' . $expediente->getPrecioVentaLider()[0]->getCliente()->getLastName();
                }


                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $indice, $i)
                    ->setCellValue('B' . $indice, $expediente->getSisgerCode())
                    ->setCellValue('C' . $indice, $expediente->getUser()->getLetra())
                    ->setCellValue('D' . $indice, $lider)
                    ->setCellValue('E' . $indice, date_format($expediente->getBeginDate(), 'd/m/Y') . ' - ' . date_format($expediente->getFinishDate(), 'd/m/Y'));
                $servicios = '';
                $s = 1;
                foreach ($expediente->getServices() as $service) {
                    if ($s != 1) {
                        $servicios = $servicios . ', ' . $service->getName();
                    } else {
                        $servicios = $servicios . $service->getName();
                    }
                    $s += 1;
                }
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('G' . $indice, $servicios);
                $suma = 0;
                foreach ($expediente->getRecibos() as $recibo) {
                    if ($recibo->isActivo()) {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('F' . $indice, $recibo->getSisgerCode() . ' | ' . $recibo->getImporte() . ' USD');
                        $suma = $suma + $recibo->getImporte();
                        $indice += 1;

                    }
                }
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('F' . $indice, 'TOTAL | ' . $suma . ' USD');

                $indice += 1;


                $i += 1;
            }


// Create new PHPExcel object


// Set document properties
            $objPHPExcel->getProperties()->setCreator("BELRAYSA TOURS & TRAVEL GROUP S.A")
                ->setLastModifiedBy("BELRAYSA TOURS & TRAVEL GROUP S.A")
                ->setTitle("REPORTE_DE_EXPEDIENTES_" . date_format(new \DateTime(), 'd/m/Y') . '_BELRAYSA TOURS & TRAVEL GROUP S.A')
                ->setSubject("REPORTE_DE_EXPEDIENTES_" . date_format(new \DateTime(), 'd/m/Y') . '_BELRAYSA TOURS & TRAVEL GROUP S.A')
                ->setDescription("REPORTE_DE_EXPEDIENTES_" . date_format(new \DateTime(), 'd/m/Y') . '_BELRAYSA TOURS & TRAVEL GROUP S.A')
                ->setKeywords("EXPEDIENTES ")
                ->setCategory("REPORTE_DE_EXPEDIENTES_" . date_format(new \DateTime(), 'd/m/Y') . '_BELRAYSA TOURS & TRAVEL GROUP S.A');


// Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="EXPEDIENTES_BELRAYSA TOURS & TRAVEL GROUP S.A.xlsx"');
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

    public function filtrarTreePorWSAction($month, $year, $usuario, $ws)
    {
        $em = $this->getDoctrine()->getManager();

        $aavv = null;
        $c507 = null;
        $lbrs = null;

        $from = null;
        $to = null;

        $sisgerCode = null;

        $mes = null;
        $anno = null;
        $lider = null;
        $user = null;


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

        $expedientes = $em->getRepository('BackendBundle:Reply')->findBusquedaAvanzada(true, $aavv, $c507, $lbrs, $sisgerCode, $from, $to, $lider, $user);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $expedientes,
            $this->get('request')->query->get('page', 1),
            10);

        if ($from) {
            $from = date_format($from, 'd/m/Y');
        }
        if ($to) {
            $to = date_format($to, 'd/m/Y');
        }
        return $this->render('BackendBundle:Reply:filtered.html.twig', array(
            'entities' => $pagination,
            'aavv' => $aavv,
            'c507' => $c507,
            'lbrs' => $lbrs,
            'sisgerCode' => $sisgerCode,
            'from' => $from,
            'to' => $to,
            'lider' => $lider,
            'user' => $user
        ));
    }
}
