<?php

namespace Belraysa\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Belraysa\BackendBundle\Entity\Document;
use Belraysa\BackendBundle\Form\DocumentType;

/**
 * Document controller.
 *
 */
class DocumentController extends Controller
{

    /**
     * Lists all Document entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:Document')->findAll();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);
        return $this->render('BackendBundle:Document:index.html.twig', array(
            'entities' => $pagination,
        ));
    }
    public function indexByClientAction($idClient)
    {
        $em = $this->getDoctrine()->getManager();
        $client = $em->getRepository('BackendBundle:Client')->find($idClient);
        $entities = $em->getRepository('BackendBundle:Document')->findByClient($client);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);
        return $this->render('BackendBundle:Document:index.html.twig', array(
            'entities' => $pagination,
        ));
    }

    /**
     * Creates a new Document entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Document();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            if (null != $entity->getUrl()) {
                $entity->uploadPhoto($this->container->getParameter('belraysa.route.client_documents'));
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('document'));
        } else {

            print_r($form->getErrors());
            die;
        }

        return $this->render('BackendBundle:Document:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Document entity.
     *
     * @param Document $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Document $entity)
    {
        $form = $this->createForm(new DocumentType(), $entity, array(
            'action' => $this->generateUrl('document_create'),
            'method' => 'POST',
        ));

        //  $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Document entity.
     *
     */
    public function newAction()
    {
        $entity = new Document();
        $form = $this->createCreateForm($entity);

        return $this->render('BackendBundle:Document:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Document entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Document')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Document:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Document entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Document')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Document:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Document entity.
     *
     * @param Document $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Document $entity)
    {
        $form = $this->createForm(new DocumentType(), $entity, array(
            'action' => $this->generateUrl('document_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //    $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Document entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Document')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        $photo = $entity->getUrl();

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if (null == $entity->getUrl()) {
                $entity->setUrl($photo);
            } else {
                $entity->uploadPhoto($this->container->getParameter('belraysa.route.client_documents'));
            }
            $em->flush();

            return $this->redirect($this->generateUrl('document', array('id' => $id)));
        }

        return $this->render('BackendBundle:Document:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Document entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Document')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        $em->remove($entity);
        $em->flush();

        $flash = $this->get('session')->getFlashBag();
        $flash->set('success', 'El documento ha sido eliminado satisfactoriamente.');

        return $this->redirect($this->generateUrl('document'));
    }

    /**
     * Creates a form to delete a Document entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('document_delete', array('id' => $id)))
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
                $entity = $em->getRepository('BackendBundle:Document')->find($id);
                if ($entity) {
                    $em->remove($entity);
                }
            }
            $em->flush();
            $flash = $this->get('session')->getFlashBag();
            $flash->set('success', 'Los documentos han sido eliminados satisfactoriamente.');
        }
        return $this->redirect($this->generateUrl('document'));


    }
}
