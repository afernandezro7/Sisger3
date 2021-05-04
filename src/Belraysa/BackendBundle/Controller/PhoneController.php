<?php

namespace Belraysa\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Belraysa\BackendBundle\Entity\Phone;
use Belraysa\BackendBundle\Form\PhoneType;

/**
 * Phone controller.
 *
 */
class PhoneController extends Controller
{

    /**
     * Lists all Phone entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:Phone')->findAll();

        return $this->render('BackendBundle:Phone:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Phone entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Phone();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('phone_show', array('id' => $entity->getId())));
        }

        return $this->render('BackendBundle:Phone:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Phone entity.
     *
     * @param Phone $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Phone $entity)
    {
        $form = $this->createForm(new PhoneType(), $entity, array(
            'action' => $this->generateUrl('phone_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Phone entity.
     *
     */
    public function newAction()
    {
        $entity = new Phone();
        $form   = $this->createCreateForm($entity);

        return $this->render('BackendBundle:Phone:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Phone entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Phone')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Phone entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Phone:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Phone entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Phone')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Phone entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Phone:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Phone entity.
    *
    * @param Phone $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Phone $entity)
    {
        $form = $this->createForm(new PhoneType(), $entity, array(
            'action' => $this->generateUrl('phone_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Phone entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Phone')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Phone entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('phone_edit', array('id' => $id)));
        }

        return $this->render('BackendBundle:Phone:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Phone entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackendBundle:Phone')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Phone entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('phone'));
    }

    /**
     * Creates a form to delete a Phone entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('phone_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
