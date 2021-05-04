<?php

namespace Belraysa\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Belraysa\BackendBundle\Entity\Inventario;
use Belraysa\BackendBundle\Form\InventarioType;

/**
 * Inventario controller.
 *
 */
class InventarioController extends Controller
{

    /**
     * Lists all Inventario entities.
     *
     */
    public function indexAction($ws)
    {
        $em = $this->getDoctrine()->getManager();
        $ws = $em->getRepository('BackendBundle:Workspace')->findOneByName($ws);
        $entities = $em->getRepository('BackendBundle:Inventario')->findByWorkspace($ws);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);

        return $this->render('BackendBundle:Inventario:index.html.twig', array(
            'entities' => $pagination,
            'ws' => $ws
        ));
    }
    /**
     * Creates a new Inventario entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Inventario();
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
            return $this->redirect($this->generateUrl('inventario', array('ws' => $ws->getName())));
        }

        return $this->render('BackendBundle:Inventario:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Inventario entity.
     *
     * @param Inventario $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Inventario $entity)
    {
        $form = $this->createForm(new InventarioType(), $entity, array(
            'action' => $this->generateUrl('inventario_create'),
            'method' => 'POST',
        ));


        return $form;
    }

    /**
     * Displays a form to create a new Inventario entity.
     *
     */
    public function newAction()
    {
        $entity = new Inventario();
        $form   = $this->createCreateForm($entity);

        return $this->render('BackendBundle:Inventario:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing Inventario entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Inventario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Inventario entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('BackendBundle:Inventario:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Inventario entity.
     *
     * @param Inventario $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Inventario $entity)
    {
        $form = $this->createForm(new InventarioType(), $entity, array(
            'action' => $this->generateUrl('inventario_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    /**
     * Edits an existing Inventario entity.
     *
     */
    public function updateAction(Request $request)
    {
        $id = $_POST['id'];
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Inventario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Inventario entity.');
        }

        $entity->setNombre($_POST['belraysa_backendbundle_inventario']['nombre']);

        $em->flush();

        return $this->redirect($this->generateUrl('inventario', array('ws' => $entity->getWorkspace()->getName())));
    }
    /**
     * Deletes a Inventario entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Inventario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Inventario entity.');
        }
        $ws = $entity->getWorkspace();
        $em->remove($entity);
        $em->flush();

        $flash = $this->get('session')->getFlashBag();
        $flash->set('success', 'El elemento ha sido eliminado satisfactoriamente.');

        return $this->redirect($this->generateUrl('inventario', array('ws' => $ws->getName())));
    }

    public function batchDeleteAction(Request $request, $ws)
    {
        $em = $this->getDoctrine()->getManager();
        $ws = $em->getRepository('BackendBundle:Workspace')->find($ws)->getName();
        $ids = $request->get('batch_action_checkbox');
        if ($ids) {
            $em = $this->getDoctrine()->getManager();
            foreach ($ids as $id) {
                $entity = $em->getRepository('BackendBundle:Inventario')->find($id);
                if ($entity) {
                    $em->remove($entity);
                }
            }
            $em->flush();
            $flash = $this->get('session')->getFlashBag();
            $flash->set('success', 'Los elementos han sido eliminados satisfactoriamente.');
        }
        return $this->redirect($this->generateUrl('inventario', array('ws' => $ws)));


    }
    public function filterAction()
    {
        $em = $this->getDoctrine()->getManager();
        $ws = $this->get('belraysa.workspace')->getCurrentWorkspace();
        $servicios = $em->getRepository('BackendBundle:Inventario')->findBusquedaSimple($_GET['query'], $ws);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $servicios,
            $this->get('request')->query->get('page', 1),
            10);

        return $this->render('BackendBundle:Inventario:filtered.html.twig', array(
            'entities' => $pagination,
            'conceptos' => $servicios,
            'query' => $_GET['query']
        ));
    }
}
