<?php

namespace Belraysa\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Belraysa\BackendBundle\Entity\TipoContenedor;
use Belraysa\BackendBundle\Form\TipoContenedorType;

/**
 * TipoContenedor controller.
 *
 */
class TipoContenedorController extends Controller
{
    public function getLengthAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:TipoContenedor')->findAll();
        $length = sizeof($entities);

        return new JsonResponse("{$length}");
    }

    /**
     * Lists all TipoContenedor entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('BackendBundle:TipoContenedor')->findAll();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);

        return $this->render('BackendBundle:TipoContenedor:index.html.twig', array(
            'entities' => $pagination
        ));
    }

    public function getTipoContenedorsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:TipoContenedor')->findAll();

        return $this->render('BackendBundle:TipoContenedor:list.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a new TipoContenedor entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new TipoContenedor();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($entity);
            $em->flush();

            $tipocontenedores = $this->getDoctrine()
                ->getRepository('BackendBundle:TipoContenedor')
                ->createQueryBuilder('s')
                ->select('s')
                ->getQuery()
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            return $this->redirect($this->generateUrl('tipocontenedor'));
        }

        return $this->render('BackendBundle:TipoContenedor:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    public function create1Action(Request $request)
    {
        $entity = new TipoContenedor();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('tipocontenedor'));
        }

        return $this->render('BackendBundle:Client:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a TipoContenedor entity.
     *
     * @param TipoContenedor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TipoContenedor $entity)
    {
        $form = $this->createForm(new TipoContenedorType(), $entity, array(
            'action' => $this->generateUrl('tipocontenedor_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new TipoContenedor entity.
     *
     */
    public function newAction()
    {
        $entity = new TipoContenedor();
        $form = $this->createCreateForm($entity);

        return $this->render('BackendBundle:TipoContenedor:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing TipoContenedor entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:TipoContenedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoContenedor entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('BackendBundle:TipoContenedor:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a TipoContenedor entity.
     *
     * @param TipoContenedor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(TipoContenedor $entity)
    {
        $form = $this->createForm(new TipoContenedorType(), $entity, array(
            'action' => $this->generateUrl('tipocontenedor_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing TipoContenedor entity.
     *
     */
    public function updateAction(Request $request)
    {
        $id = $_POST['id'];
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:TipoContenedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoContenedor entity.');
        }

        $entity->setNombre($_POST['belraysa_backendbundle_tipocontenedor']['nombre']);

        $em->flush();

        return $this->redirect($this->generateUrl('tipocontenedor'));
    }

    /**
     * Deletes a TipoContenedor entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:TipoContenedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoContenedor entity.');
        }

        $em->remove($entity);
        $em->flush();

        $flash = $this->get('session')->getFlashBag();
        $flash->set('success', 'El tipo de contebedor ha sido eliminado satisfactoriamente.');

        return $this->redirect($this->generateUrl('tipocontenedor'));
    }

    /**
     * Creates a form to delete a TipoContenedor entity by id.
     *
     * @param mixed $id The entity idz
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tipocontenedor_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    public function batchDeleteAction(Request $request)
    {
        $ids = $request->get('batch_action_checkbox');
        if ($ids) {
            $em = $this->getDoctrine()->getManager();
            foreach ($ids as $id) {
                $entity = $em->getRepository('BackendBundle:TipoContenedor')->find($id);
                if ($entity) {
                    $em->remove($entity);
                }
            }
            $em->flush();
            $flash = $this->get('session')->getFlashBag();
            $flash->set('success', 'Los tipos de contenedores han sido eliminados satisfactoriamente.');
        }
        return $this->redirect($this->generateUrl('tipocontenedor'));


    }

    public function filterAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tiposcontenedor = $em->getRepository('BackendBundle:TipoContenedor')->findBusquedaSimple($_GET['query']);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $tiposcontenedor,
            $this->get('request')->query->get('page', 1),
            10);

        return $this->render('BackendBundle:TipoContenedor:filtered.html.twig', array(
            'entities' => $pagination,
            'tipocontenedores' => $tiposcontenedor,
            'query' => $_GET['query']
        ));
    }
}
