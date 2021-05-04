<?php

namespace Belraysa\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Belraysa\BackendBundle\Entity\Anno;
use Belraysa\BackendBundle\Form\AnnoType;

/**
 * Anno controller.
 *
 */
class AnnoController extends Controller
{

    /**
     * Lists all Anno entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:Anno')->findAll();

        return $this->render('BackendBundle:Anno:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a new Anno entity.
     *
     */
    public function createAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        if (!$em->getRepository('BackendBundle:Anno')->findByNombre($_POST['anno'])) {
            $entity = new Anno();
            $entity->setNombre($_POST['anno']);
            $em->persist($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('anno'));

    }

    /**
     * Creates a form to create a Anno entity.
     *
     * @param Anno $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Anno $entity)
    {
        $form = $this->createForm(new AnnoType(), $entity, array(
            'action' => $this->generateUrl('anno_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Anno entity.
     *
     */
    public function newAction()
    {
        $entity = new Anno();
        $form = $this->createCreateForm($entity);

        return $this->render('BackendBundle:Anno:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Anno entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Anno')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Anno entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Anno:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Anno entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Anno')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Anno entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Anno:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Anno entity.
     *
     * @param Anno $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Anno $entity)
    {
        $form = $this->createForm(new AnnoType(), $entity, array(
            'action' => $this->generateUrl('anno_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Anno entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Anno')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Anno entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('anno_edit', array('id' => $id)));
        }

        return $this->render('BackendBundle:Anno:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Anno entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Anno')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Anno entity.');
        }

        $em->remove($entity);
        $em->flush();
        $flash = $this->get('session')->getFlashBag();
        $flash->set('success', 'Los cambios han sido guardados satisfactoriamente.');

        return $this->redirect($this->generateUrl('anno'));
    }

    /**
     * Creates a form to delete a Anno entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('anno_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
}
