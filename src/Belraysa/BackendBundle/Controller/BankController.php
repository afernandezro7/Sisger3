<?php

namespace Belraysa\BackendBundle\Controller;

use Belraysa\BackendBundle\Form\BankEditType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Belraysa\BackendBundle\Entity\Bank;
use Belraysa\BackendBundle\Form\BankType;

/**
 * Bank controller.
 *
 */
class BankController extends Controller
{

    /**
     * Lists all Bank entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:Bank')->findAll();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);
        return $this->render('BackendBundle:Bank:index.html.twig', array(
            'entities' => $pagination
        ));
    }
    /**
     * Creates a new Bank entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Bank();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();


        if ($form->isValid()) {
            $entity->setCreatedAt(new \DateTime());
            $entity->setType('Banco');
            $entity->setInitBalance($entity->getBalance());
            $em->persist($entity);
            $em->flush();

          //  $ws = $entity->getWorkspace();
          //  $ws->addBanking($entity);
          //  $em->flush();
        }

        return $this->redirect($this->generateUrl('banking_all'));
    }

    /**
     * Creates a form to create a Bank entity.
     *
     * @param Bank $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Bank $entity)
    {
        $form = $this->createForm(new BankType(), $entity, array(
            'action' => $this->generateUrl('bank_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Bank entity.
     *
     */
    public function newAction()
    {
        $bank = new Bank();
        $form_bank = $this->createForm(new BankType(), $bank, array(
            'action' => $this->generateUrl('bank_create'),
            'method' => 'POST',
        ));

        return $this->render('BackendBundle:Bank:new.html.twig', array(
            'entity' => $bank,
            'form_bank'   => $form_bank->createView(),
        ));
    }

    /**
     * Finds and displays a Bank entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Bank')->find($id);
        $entradas = $em->getRepository('BackendBundle:BankingEntry')->findBy(array('banking' => $entity), array('id' => 'DESC'));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Bank entity.');
        }
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entradas,
            $this->get('request')->query->get('page', 1),
            10);
        return $this->render('BackendBundle:Bank:index.html.twig', array(
            'entity' => $entity,
            'entradas' => $pagination,
            'ws' => $this->get('belraysa.workspace')->getCurrentWorkspace()

        ));
    }

    public function timelineAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Bank')->find($id);
        $entradas = $em->getRepository('BackendBundle:BankingEntry')->findBy(array('banking' => $entity), array('id' => 'DESC'));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Bank entity.');
        }

        $finalDates = array();
        $j = 0;
        for ($i = sizeof($entradas) - 1; $i >= 0; $i--) {
            $entrada = $entradas[$j];
            $date = $entrada->getDate()->format('d M. Y');
            $finalDates[$date][] = $entrada;
            $j++;
        }

        return $this->render('BackendBundle:BankingEntry:timeline.html.twig', array(
            'entity' => $entity,
            'entradas' => $finalDates,
            'ws' => $this->get('belraysa.workspace')->getCurrentWorkspace()

        ));
    }

    /**
     * Displays a form to edit an existing Bank entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Bank')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Bank entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Bank:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Bank entity.
    *
    * @param Bank $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Bank $entity)
    {
        $form = $this->createForm(new BankEditType(), $entity, array(
            'action' => $this->generateUrl('bank_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->remove('workspace');
        $form->remove('balance');


        return $form;
    }
    /**
     * Edits an existing Bank entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Bank')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Bank entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();


        }

        return $this->redirect($this->generateUrl('banking_all'));
    }
    /**
     * Deletes a Bank entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackendBundle:Bank')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Bank entity.');
            }

            foreach ($entity->getWorkspaces() as $workspace) {
                $workspace->removeBanking($entity);
                $em->flush();
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('banking_all'));
    }

    /**
     * Creates a form to delete a Bank entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bank_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    public function generarReportePDFAction($banking)
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


        $entradas = $em->getRepository('BackendBundle:BankingEntry')->findEntradasPorFechas($from, $to, $banking);
        $banking = $em->getRepository('BackendBundle:Banking')->find($banking);

        if (array_key_exists('pdf', $_POST)) {
            return $this->render('BackendBundle:BankingEntry:reporte.html.twig', array(
                'entradas' => $entradas,
                'from' => $fromString,
                'to' => $toString,
                'banking' => $banking
            ));

        } else {
            $flash = $this->get('session')->getFlashBag();
            $flash->set('success', 'Filtro aplicado. Los movimientos que se muestran están comprendidos en el rango de fechas especificado.');

            return $this->render('BackendBundle:Bank:index.html.twig', array(
                'entity' => $banking,
                'entradas' => $entradas,
                'ws' => $this->get('belraysa.workspace')->getCurrentWorkspace()
            ));
        }

    }
}
