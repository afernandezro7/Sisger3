<?php

namespace Belraysa\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Belraysa\BackendBundle\Entity\Service;
use Belraysa\BackendBundle\Form\ServiceType;

/**
 * Service controller.
 *
 */
class ServiceController extends Controller
{
    public function getLengthAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:Service')->findAll();
        $length = sizeof($entities);

        return new JsonResponse("{$length}");
    }

    /**
     * Lists all Service entities.
     *
     */
    public function indexAction($ws)
    {
        $em = $this->getDoctrine()->getManager();
        $ws = $em->getRepository('BackendBundle:Workspace')->findOneByName($ws);
        $entities = $em->getRepository('BackendBundle:Service')->findByWorkspace($ws);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);

        return $this->render('BackendBundle:Service:index.html.twig', array(
            'entities' => $pagination,
            'ws' => $ws
        ));
    }

    public function getServicesAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:Service')->findAll();

        return $this->render('BackendBundle:Service:list.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a new Service entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Service();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $ws = $this->get('belraysa.workspace')->getCurrentWorkspace();
            if (!$entity->getWorkspace()) {
                $entity->setWorkspace($ws);
            }
            $em->persist($entity);
            $em->flush();

            $services = $this->getDoctrine()
                ->getRepository('BackendBundle:Service')
                ->createQueryBuilder('s')
                ->select('s')
                ->getQuery()
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            return new JsonResponse($services);
        }

        return $this->render('BackendBundle:Service:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    public function create1Action(Request $request)
    {
        $entity = new Service();
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
            return $this->redirect($this->generateUrl('service', array('ws' => $ws->getName())));
        }

        return $this->render('BackendBundle:Client:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Service entity.
     *
     * @param Service $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Service $entity)
    {
        $form = $this->createForm(new ServiceType(), $entity, array(
            'action' => $this->generateUrl('service_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Service entity.
     *
     */
    public function newAction()
    {
        $entity = new Service();
        $form = $this->createCreateForm($entity);

        return $this->render('BackendBundle:Service:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing Service entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Service')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Service entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('BackendBundle:Service:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Service entity.
     *
     * @param Service $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Service $entity)
    {
        $form = $this->createForm(new ServiceType(), $entity, array(
            'action' => $this->generateUrl('service_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Service entity.
     *
     */
    public function updateAction(Request $request)
    {
        $id = $_POST['id'];
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Service')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Service entity.');
        }

        $entity->setName($_POST['belraysa_backendbundle_service']['name']);

        $em->flush();

        return $this->redirect($this->generateUrl('service', array('ws' => $entity->getWorkspace()->getName())));
    }

    /**
     * Deletes a Service entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Service')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Service entity.');
        }
        $ws = $entity->getWorkspace();
        $em->remove($entity);
        $em->flush();

        $flash = $this->get('session')->getFlashBag();
        $flash->set('success', 'El servicio ha sido eliminado satisfactoriamente.');

        return $this->redirect($this->generateUrl('service', array('ws' => $ws->getName())));
    }

    /**
     * Creates a form to delete a Service entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('service_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
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
            $em = $this->getDoctrine()->getManager();
            foreach ($ids as $id) {
                $entity = $em->getRepository('BackendBundle:Service')->find($id);
                if ($entity) {
                    $em->remove($entity);
                }
            }
            $em->flush();
            $flash = $this->get('session')->getFlashBag();
            $flash->set('success', 'Los servicios han sido eliminados satisfactoriamente.');
        }
        return $this->redirect($this->generateUrl('service', array('ws' => $ws)));


    }

    public function filterAction()
    {
        $em = $this->getDoctrine()->getManager();
        $ws = $this->get('belraysa.workspace')->getCurrentWorkspace();
        $servicios = $em->getRepository('BackendBundle:Service')->findBusquedaSimple($_GET['query'], $ws);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $servicios,
            $this->get('request')->query->get('page', 1),
            10);

        return $this->render('BackendBundle:Service:filtered.html.twig', array(
            'entities' => $pagination,
            'services' => $servicios,
            'query' => $_GET['query']
        ));
    }
}
