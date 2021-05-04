<?php

namespace Belraysa\BackendBundle\Controller;

use Belraysa\BackendBundle\BackendBundle;
use Belraysa\BackendBundle\Entity\Document;
use Belraysa\BackendBundle\Entity\User;
use Belraysa\BackendBundle\Form\ClientEditType;
use Belraysa\BackendBundle\Form\DocumentType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Belraysa\BackendBundle\Entity\Client;
use Belraysa\BackendBundle\Form\ClientType;
use Symfony\Component\HttpFoundation\Response;
use Belraysa\BackendBundle\Form\UserType;


/**
 * Client controller.
 *
 */
class ClientController extends Controller
{
    /**
     * Lists all Client entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();


        $user = $this->get('security.context')->getToken()->getUser();
        $userEntities = $em->getRepository('BackendBundle:Client')->findByUserOrderedDesc($user);
        $notUserEntities = $em->getRepository('BackendBundle:Client')->findClientsWithNoUserOrderedDesc($user);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            array_merge($userEntities, $notUserEntities),
            $this->get('request')->query->get('page', 1),
            30);

        return $this->render('BackendBundle:Client:index.html.twig', array(
            'entities' => $pagination,
            'clientes' => $em->getRepository('BackendBundle:Client')->findAll()
        ));
    }

    public function pdfAction()
    {
        $em = $this->getDoctrine()->getManager();
        $clientes = $em->getRepository('BackendBundle:Client')->findAll();

        return $this->render('BackendBundle:Client:reporte.html.twig', array(
            'clientes' => $clientes,
        ));
    }

    public function xlsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $clientes = $em->getRepository('BackendBundle:Client')->findAll();

        // get the XLS
//aqui se ejecuta el codigo del reader
        $uploaddir = $this->container->getParameter('belraysa.route.templates');
        $uploadfile = $uploaddir . basename('Clientes.xlsx');

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

        $indice = 6;
        $style = $objPHPExcel->getActiveSheet()->getStyle('A6');
        foreach ($clientes as $cliente) {
            if ($indice > 6) {
                $objPHPExcel->getActiveSheet()->duplicateStyle($style,
                    'A' . $indice . ':J' . $indice
                );
            }
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $indice, $cliente->getFirstName() . ' ' . $cliente->getLastName())
                ->setCellValue('B' . $indice, $cliente->getDni())
                ->setCellValue('C' . $indice, $cliente->getPassport())
                ->setCellValue('D' . $indice, $cliente->getAddress())
                ->setCellValue('E' . $indice, $cliente->getMunicipality())
                ->setCellValue('F' . $indice, $cliente->getProvince())
                ->setCellValue('G' . $indice, $cliente->getCountry())
                ->setCellValue('H' . $indice, $cliente->getPhones())
                ->setCellValue('I' . $indice, $cliente->getCell())
                ->setCellValue('J' . $indice, $cliente->getEmail());

            $indice += 1;
        }


// Create new PHPExcel object


// Set document properties
        $objPHPExcel->getProperties()->setCreator("BELRAYSA TOURS & TRAVEL GROUP S.A")
            ->setLastModifiedBy("BELRAYSA TOURS & TRAVEL GROUP S.A")
            ->setTitle("REPORTE_DE_CLIENTES_" . date_format(new \DateTime(), 'd/m/Y') . '_BELRAYSA TOURS & TRAVEL GROUP S.A')
            ->setSubject("REPORTE_DE_CLIENTES_" . date_format(new \DateTime(), 'd/m/Y') . '_BELRAYSA TOURS & TRAVEL GROUP S.A')
            ->setDescription("REPORTE_DE_CLIENTES_" . date_format(new \DateTime(), 'd/m/Y') . '_BELRAYSA TOURS & TRAVEL GROUP S.A')
            ->setKeywords("CLIENTES ")
            ->setCategory("REPORTE_DE_CLIENTES_" . date_format(new \DateTime(), 'd/m/Y') . '_BELRAYSA TOURS & TRAVEL GROUP S.A');


// Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="CLIENTES_BELRAYSA TOURS & TRAVEL GROUP S.A.xlsx"');
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

    /**
     * Creates a new Client entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Client();
        $form = $this->createCreateForm($request, $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $user = $this->get('security.context')->getToken()->getUser();
            if ($entity->getEmail() == null) {
                $entity->setEmail("");
            }
            if ($entity->getPhones() == null) {
                $entity->setPhones("");
            }

            $entity->setUser($user);
            $em = $this->getDoctrine()->getManager();
            if (array_key_exists('province', $_POST)) {
                $entity->setProvince($_POST['province']);
            }
            if (array_key_exists('country', $_POST)) {
                $entity->setCountry($_POST['country']);
            }
            $em->persist($entity);
            $em->flush();

            $clients = $this->getDoctrine()
                ->getRepository('BackendBundle:Client')
                ->createQueryBuilder('c')
                ->select('c')
                ->getQuery()
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            return new JsonResponse($clients);

        }

        return $this->render('BackendBundle:Client:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    public function create1Action(Request $request)
    {
        $entity = new Client();
        $form = $this->createCreateForm($request, $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $user = $this->get('security.context')->getToken()->getUser();
            if ($entity->getEmail() == null) {
                $entity->setEmail("");
            }
            if ($entity->getPhones() == null) {
                $entity->setPhones("");
            }
            $entity->setUser($user);
            $em = $this->getDoctrine()->getManager();

            if (array_key_exists('province', $_POST)) {
                $entity->setProvince($_POST['province']);
            }
            if (array_key_exists('country', $_POST)) {
                $entity->setCountry($_POST['country']);
            }

            $em->persist($entity);
            $em->flush();

            if (array_key_exists('expediente', $_POST)) {
                return $this->redirect($this->generateUrl('reply_new'));
            } elseif (array_key_exists('cliente', $_POST)) {
                return $this->redirect($this->generateUrl('client') . '#myModalHorizontal');
            } else {
                return $this->redirect($this->generateUrl('client'));
            }

        } else {
            print_r($form->getErrors());
            die;
        }

        return $this->render('BackendBundle:Client:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }


    /**
     * Creates a form to create a Client entity.
     *
     * @param Client $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Request $request, Client $entity)
    {
        $form = $this->createForm(new ClientType(), $entity, array(
            'action' => $this->generateUrl('client_create'),
            'method' => 'POST',
        ));

        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        $session = $request->getSession();
        if ($session->has('workspace')) {
            $workspace = $em->getRepository('BackendBundle:Workspace')->find($session->get('workspace')->getId());

        } else {
            $workspace = $user->getWorkspace();
        }

        if ($workspace->getName() == 'L-BRS') {
            $form->add('dni', null, array('required' => true))
                ->add('passport', null, array('required' => false))
                ->add('address', 'textarea', array('required' => true))
                ->add('municipality', null, array('required' => true))
                ->add('cell', null, array('required' => true))
                // ->add('province', 'choice', array('required' => true))
                // ->add('country', 'choice', array('required' => true))
            ;
        }

        return $form;
    }

    /**
     * Displays a form to create a new Client entity.
     *
     */
    public
    function newAction(Request $request)
    {
        $entity = new Client();

        $form = $this->createCreateForm($request, $entity);

        return $this->render('BackendBundle:Client:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),

        ));
    }

    /**
     * Finds and displays a Client entity.
     *
     */

    public
    function getDataAction(Request $request)
    {
        $id = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();

        $client = $em->getRepository('BackendBundle:Client')->createQueryBuilder('c')
            ->select('c')
            ->where('c.id = :clientId')
            ->setParameter('clientId', $id)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return new JsonResponse($client);
    }

    public
    function getClientsAction()
    {

        $clients = $this->getDoctrine()
            ->getRepository('BackendBundle:Client')
            ->createQueryBuilder('a')
            ->select('a')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);


        return new JsonResponse($clients);
    }


    public
    function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Client')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Client entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Client:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Client entity.
     *
     */
    public
    function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Client')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Client entity.');
        }

        $editForm = $this->createEditForm($request, $entity);

        return $this->render('BackendBundle:Client:edit.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView()
        ));
    }

    public
    function edit1Action(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Client')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Client entity.');
        }

        $editForm = $this->createEditForm($request, $entity);

        return $this->render('BackendBundle:Client:edit1.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView()
        ));
    }

    public
    function edit2Action(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Client')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Client entity.');
        }

        $editForm = $this->createEditForm($request, $entity);

        return $this->render('BackendBundle:Client:edit2.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView()
        ));
    }

    public
    function edit3Action(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Client')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Client entity.');
        }

        $editForm = $this->createEditForm($request, $entity);

        return $this->render('BackendBundle:Client:edit3.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView()
        ));
    }

    /**
     * Creates a form to edit a Client entity.
     *
     * @param Client $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private
    function createEditForm(Request $request, Client $entity)
    {
        $form = $this->createForm(new ClientEditType(), $entity, array(
            'action' => $this->generateUrl('client_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        $session = $request->getSession();
        if ($session->has('workspace')) {
            $workspace = $em->getRepository('BackendBundle:Workspace')->find($session->get('workspace')->getId());

        } else {
            $workspace = $user->getWorkspace();
        }

        if ($workspace->getName() == 'L-BRS') {
            $form->add('dni', null, array('required' => true))
                ->add('passport', null, array('required' => false))
                ->add('address', 'textarea', array('required' => true))
                ->add('municipality', null, array('required' => true))
                ->add('cell', null, array('required' => true))
                // ->add('province', null, array('required' => true))
                // ->add('country', null, array('required' => true))
            ;
        }

        return $form;
    }

    /**
     * Edits an existing Client entity.
     *
     */
    public
    function updateAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Client')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Bank entity.');
        }

        $editForm = $this->createEditForm($request, $entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if (array_key_exists('belraysa_backendbundle_client', $_POST)) {
                $entity->setProvince($_POST['belraysa_backendbundle_client']['province']);
                $entity->setCountry($_POST['belraysa_backendbundle_client']['country']);
            }
            $em->flush();
        }

        return $this->redirect($this->generateUrl('client'));

    }

    public
    function update1Action(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Client')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Bank entity.');
        }

        $editForm = $this->createEditForm($request, $entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if (array_key_exists('belraysa_backendbundle_client', $_POST)) {
                $entity->setProvince($_POST['belraysa_backendbundle_client']['province']);
                $entity->setCountry($_POST['belraysa_backendbundle_client']['country']);
            }
            $em->flush();
        }

        if (array_key_exists('ruta', $_POST)) {
            return $this->redirect($this->generateUrl($_POST['ruta']));
        } else {
            return $this->redirect($this->generateUrl('client'));
        }
    }

    /**
     * Deletes a Client entity.
     *
     */
    public
    function deleteAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Client')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Client entity.');
        }

        $em->remove($entity);
        $em->flush();

        $flash = $this->get('session')->getFlashBag();
        $flash->set('success', 'El cliente ha sido eliminado satisfactoriamente.');

        return $this->redirect($this->generateUrl('client'));
    }

    /**
     * Creates a form to delete a Client entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private
    function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('client_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }


    public
    function batchDeleteAction(Request $request)
    {

        $ids = $request->get('batch_action_checkbox');
        if ($ids) {
            $em = $this->getDoctrine()->getManager();
            foreach ($ids as $id) {
                $entity = $em->getRepository('BackendBundle:Client')->find($id);
                if ($entity) {
                    $em->remove($entity);
                }
            }
            $em->flush();
            $flash = $this->get('session')->getFlashBag();
            $flash->set('success', 'Los clientes han sido eliminados satisfactoriamente.');
        }
        return $this->redirect($this->generateUrl('client'));


    }

    public
    function getLengthByOperatorAction()
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

        $clientByOperatorArray = array();

        foreach ($operators as $operator) {
            $clientByOperatorArray[] = sizeof($em->getRepository('BackendBundle:Client')->findClientsByUserAndMonth($operator, $from, $to));
        }


        foreach ($clientByOperatorArray as $val) {
            $json[] = sprintf('%s', (is_string($val) ? $val : json_encode($val)));
        }
        return new JsonResponse($json);
    }

    public
    function getLengthByOperatorWSAction()
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

        $clientByOperatorArray = array();

        foreach ($operators as $operator) {
            $clientByOperatorArray[] = sizeof($em->getRepository('BackendBundle:Client')->findClientsByUserAndMonth($operator, $from, $to));
        }

        $json = array();

        foreach ($clientByOperatorArray as $val) {
            $json[] = sprintf('%s', (is_string($val) ? $val : json_encode($val)));
        }
        return new JsonResponse($json);
    }

    public
    function getDocumentTypesAction()
    {
        $em = $this->getDoctrine()->getManager();

        $types = $em->getRepository('BackendBundle:DocumentType')->createQueryBuilder('t')
            ->select('t')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return new JsonResponse($types);
    }

    public function filterAction()
    {
        $em = $this->getDoctrine()->getManager();
        $clientes = $em->getRepository('BackendBundle:Client')->findBusquedaSimple($_GET['query']);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $clientes,
            $this->get('request')->query->get('page', 1),
            10);

        return $this->render('BackendBundle:Client:filtered.html.twig', array(
            'entities' => $pagination,
            'clientes' => $clientes,
            'query' => $_GET['query']
        ));
    }

    public function verificarDniAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clientes = $em->getRepository('BackendBundle:Client')->findByDni($_POST['dni']);
        if (sizeof($clientes) > 1) {
            $flag = 'ERROR';
            $mensaje = 'El número de identidad especificado ya existe';
        } elseif (sizeof($clientes) == 1) {
            if(array_key_exists('client', $_POST)) {
                if ($clientes[0]->getId() == $_POST['client']){
                    $flag = 'OK';
                    $mensaje = 'OK';
                }else{
                    $flag = 'ERROR';
                    $mensaje = 'El número de identidad especificado ya existe';
                }
            }else{
                $flag = 'ERROR';
                $mensaje = 'El número de identidad especificado ya existe';
            }
        } else {
            $flag = 'OK';
            $mensaje = 'OK';
        }
        return new JsonResponse(array('status' => $flag, 'message' => $mensaje));
    }

    public function verificarPassportAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clientes = $em->getRepository('BackendBundle:Client')->findByPassport($_POST['passport']);
        if (sizeof($clientes) > 1) {
            $flag = 'ERROR';
            $mensaje = 'El pasaporte especificado ya existe';
        } elseif (sizeof($clientes) == 1) {
            if(array_key_exists('client', $_POST)) {
                if ($clientes[0]->getId() == $_POST['client']){
                    $flag = 'OK';
                    $mensaje = 'OK';
                }else{
                    $flag = 'ERROR';
                    $mensaje = 'El pasaporte especificado ya existe';
                }
            }else{
                $flag = 'ERROR';
                $mensaje = 'El pasaporte especificado ya existe';
            }
        } else {
            $flag = 'OK';
            $mensaje = 'OK';
        }
        return new JsonResponse(array('status' => $flag, 'message' => $mensaje));
    }
}
