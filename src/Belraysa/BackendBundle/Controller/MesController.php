<?php

namespace Belraysa\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Belraysa\BackendBundle\Entity\Mes;
use Belraysa\BackendBundle\Form\MesType;

/**
 * Mes controller.
 *
 */
class MesController extends Controller
{

    /**
     * Lists all Mes entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:Mes')->findAll();

        return $this->render('BackendBundle:Mes:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a new Mes entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$em->getRepository('BackendBundle:Mes')->findOneBy(array('anno' => $_POST['annoid'], 'numero' => $_POST['mes']))) {
            $entity = new Mes();
            $entity->setNumero($_POST['mes']);
            $mes_numero = $_POST['mes'];
            $mesNombre = 'ENERO';
            switch ($mes_numero) {
                case 1:
                    $mesNombre = 'ENERO';
                    break;
                case 2:
                    $mesNombre = 'FEBRERO';
                    break;
                case 3:
                    $mesNombre = 'MARZO';
                    break;
                case 4:
                    $mesNombre = 'ABRIL';
                    break;
                case 5:
                    $mesNombre = 'MAYO';
                    break;
                case 6:
                    $mesNombre = 'JUNIO';
                    break;
                case 7:
                    $mesNombre = 'JULIO';
                    break;
                case 8:
                    $mesNombre = 'AGOSTO';
                    break;
                case 9:
                    $mesNombre = 'SEPTIEMBRE';
                    break;
                case 10:
                    $mesNombre = 'OCTUBRE';
                    break;
                case 11:
                    $mesNombre = 'NOVIEMBRE';
                    break;
                case 12:
                    $mesNombre = 'DICIEMBRE';
            }
            $entity->setNombre($mesNombre);
            $em->persist($entity);
            $em->flush();
            $anno = $em->getRepository('BackendBundle:Anno')->find($_POST['annoid']);
            $anno->addMes($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('anno'));

    }

    /**
     * Creates a form to create a Mes entity.
     *
     * @param Mes $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Mes $entity)
    {
        $form = $this->createForm(new MesType(), $entity, array(
            'action' => $this->generateUrl('mes_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Mes entity.
     *
     */
    public function newAction()
    {
        $entity = new Mes();
        $form = $this->createCreateForm($entity);

        return $this->render('BackendBundle:Mes:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Mes entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Mes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Mes entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Mes:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Mes entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Mes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Mes entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Mes:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Mes entity.
     *
     * @param Mes $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Mes $entity)
    {
        $form = $this->createForm(new MesType(), $entity, array(
            'action' => $this->generateUrl('mes_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Mes entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Mes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Mes entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('mes_edit', array('id' => $id)));
        }

        return $this->render('BackendBundle:Mes:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Mes entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Mes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Mes entity.');
        }

        $em->remove($entity);
        $em->flush();
        $flash = $this->get('session')->getFlashBag();
        $flash->set('success', 'Los cambios han sido guardados satisfactoriamente.');

        return $this->redirect($this->generateUrl('mes'));
    }

    /**
     * Creates a form to delete a Mes entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mes_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
}
