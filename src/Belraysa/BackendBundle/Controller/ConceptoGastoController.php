<?php

namespace Belraysa\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Belraysa\BackendBundle\Entity\ConceptoGasto;
use Belraysa\BackendBundle\Form\ConceptoGastoType;

/**
 * ConceptoGasto controller.
 *
 */
class ConceptoGastoController extends Controller
{

    /**
     * Lists all ConceptoGasto entities.
     *
     */
    public function indexAction($ws)
    {
        $em = $this->getDoctrine()->getManager();
        $ws = $em->getRepository('BackendBundle:Workspace')->findOneByName($ws);
        $entities = $em->getRepository('BackendBundle:ConceptoGasto')->findByWorkspace($ws);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);

        return $this->render('BackendBundle:ConceptoGasto:index.html.twig', array(
            'entities' => $pagination,
            'ws' => $ws
        ));
    }
    /**
     * Creates a new ConceptoGasto entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ConceptoGasto();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $ws = $this->get('belraysa.workspace')->getCurrentWorkspace();
            if (!$entity->getWorkspace()) {
                $entity->setWorkspace($ws);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('conceptogasto', array('ws' => $ws->getName())));
        }

        return $this->render('BackendBundle:ConceptoGasto:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ConceptoGasto entity.
     *
     * @param ConceptoGasto $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ConceptoGasto $entity)
    {
        $form = $this->createForm(new ConceptoGastoType(), $entity, array(
            'action' => $this->generateUrl('conceptogasto_create'),
            'method' => 'POST',
        ));


        return $form;
    }

    /**
     * Displays a form to create a new ConceptoGasto entity.
     *
     */
    public function newAction()
    {
        $entity = new ConceptoGasto();
        $form   = $this->createCreateForm($entity);

        return $this->render('BackendBundle:ConceptoGasto:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing ConceptoGasto entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:ConceptoGasto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ConceptoGasto entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('BackendBundle:ConceptoGasto:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a ConceptoGasto entity.
    *
    * @param ConceptoGasto $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ConceptoGasto $entity)
    {
        $form = $this->createForm(new ConceptoGastoType(), $entity, array(
            'action' => $this->generateUrl('conceptogasto_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    /**
     * Edits an existing ConceptoGasto entity.
     *
     */
    public function updateAction(Request $request)
    {
        $id = $_POST['id'];
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:ConceptoGasto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ConceptoGasto entity.');
        }

        $entity->setNombre($_POST['belraysa_backendbundle_conceptogasto']['nombre']);

        $em->flush();

        return $this->redirect($this->generateUrl('conceptogasto', array('ws' => $entity->getWorkspace()->getName())));
    }
    /**
     * Deletes a ConceptoGasto entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:ConceptoGasto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ConceptoGasto entity.');
        }
        $ws = $entity->getWorkspace();
        $em->remove($entity);
        $em->flush();

        $flash = $this->get('session')->getFlashBag();
        $flash->set('success', 'El elemento ha sido eliminado satisfactoriamente.');

        return $this->redirect($this->generateUrl('conceptogasto', array('ws' => $ws->getName())));
    }

    public function batchDeleteAction(Request $request, $ws)
    {
        $em = $this->getDoctrine()->getManager();
        $ws = $em->getRepository('BackendBundle:Workspace')->find($ws)->getName();
        $ids = $request->get('batch_action_checkbox');
        if ($ids) {
            $em = $this->getDoctrine()->getManager();
            foreach ($ids as $id) {
                $entity = $em->getRepository('BackendBundle:ConceptoGasto')->find($id);
                if ($entity) {
                    $em->remove($entity);
                }
            }
            $em->flush();
            $flash = $this->get('session')->getFlashBag();
            $flash->set('success', 'Los elementos han sido eliminados satisfactoriamente.');
        }
        return $this->redirect($this->generateUrl('conceptogasto', array('ws' => $ws)));


    }
    public function filterAction()
    {
        $em = $this->getDoctrine()->getManager();
        $ws = $this->get('belraysa.workspace')->getCurrentWorkspace();
        $servicios = $em->getRepository('BackendBundle:ConceptoGasto')->findBusquedaSimple($_GET['query'], $ws);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $servicios,
            $this->get('request')->query->get('page', 1),
            10);

        return $this->render('BackendBundle:ConceptoGasto:filtered.html.twig', array(
            'entities' => $pagination,
            'conceptos' => $servicios,
            'query' => $_GET['query']
        ));
    }
}
