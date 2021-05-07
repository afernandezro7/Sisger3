<?php

namespace Belraysa\BackendBundle\Controller;

use Belraysa\BackendBundle\Form\ENAEditType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Belraysa\BackendBundle\Entity\ENA;
use Belraysa\BackendBundle\Form\ENAType;

/**
 * ENA controller.
 *
 */
class ENAController extends Controller
{

    /**
     * Lists all ENA entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $activas = array();
        $entities = $em->getRepository('BackendBundle:ENA')->findOrdenadas();
        foreach ($entities as $ena) {
            if ($ena->getContenedor()) {
                if ($ena->getContenedor()->getEstado() != 'CERRADO') {
                    $activas[] = $ena;
                }
            }
        }

        $entity = new ENA();
        $form = $this->createCreateForm($entity);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $activas,
            $this->get('request')->query->get('page', 1),
            15);

        $contenedor = $em->getRepository('BackendBundle:Contenedor')->findContenedorEnUso();
        $flag_hbl = 'no';
        if ($contenedor) {
            $flag_hbl = 'si';
        }

        return $this->render('BackendBundle:ENA:index.html.twig', array(
            'entities' => $pagination,
            'form' => $form->createView(),
            'query' => '',
            'flag_hbl' => $flag_hbl,
            'exp_id' => 0
        ));
    }

    public function filtrarAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $_GET['query'];

        $activas = array();
        $entities = $em->getRepository('BackendBundle:ENA')->advanceSearch($query);
        foreach ($entities as $ena) {
            if ($ena->getContenedor()) {
                if ($ena->getContenedor()->getEstado() != 'CERRADO') {
                    $activas[] = $ena;
                }
            }
        }

        $entity = new ENA();
        $form = $this->createCreateForm($entity);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $activas,
            $this->get('request')->query->get('page', 1),
            15);

        $contenedor = $em->getRepository('BackendBundle:Contenedor')->findContenedorEnUso();
        $flag_hbl = 'no';
        if ($contenedor) {
            $flag_hbl = 'si';
        }

        return $this->render('BackendBundle:ENA:index.html.twig', array(
            'entities' => $pagination,
            'form' => $form->createView(),
            'query' => $query,
            'flag_hbl' => $flag_hbl,
            'exp_id' => 0
        ));
    }

    /**
     * Creates a new ENA entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ENA();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $now = new \DateTime();

            $em = $this->getDoctrine()->getManager();

            $expediente = $em->getRepository('BackendBundle:Reply')->findOneBy(array('id' => $_POST['expediente']));
            if(!$expediente){
                $flash = $this->get('session')->getFlashBag();
                $flash->set('danger', 'No existe el expediente E-' . $_POST['expediente']);
                return $this->redirect($this->generateUrl('ena'));
            }
            $entity->setTipo('ENA');
            $entity->setExpediente($expediente);

            $entity->setConsignado($entity->getRemitente());
            $entity->setFechaCreacion(new \DateTime());

            $entity->setEstado($_POST['estado']);
            $em->persist($entity);
            $em->flush();

            if ($entity->getEstado() == 'RVA') {
                $sisgerCode = "RVA" . date_format($now, 'y') . "000" . str_pad($entity->getId(), 6, 0, STR_PAD_LEFT);
            } else {
                $contenedor = $em->getRepository('BackendBundle:Contenedor')->findContenedorEnUso()[0];

                /* if ($_POST['sisgerCode'] != "") {
                     $fecha = $_POST['sisgerCode'];
                     $fecha = date_create($fecha);
                */
                $sisgerCode = "BRS". date_format($now, 'y') . "000" . str_pad($entity->getId(), 6, 0, STR_PAD_LEFT);
                //  $entity->setFechaHBL($fecha);
                /* } else {
                     $sisgerCode = "LBRS" . date_format($now, 'y')[1] . date_format($now, 'm') . date_format($now, 'd') . str_pad($entity->getId(), 6, 0, STR_PAD_LEFT);
                     $entity->setFechaHBL(new \DateTime());
                 }
                */
                $entity->setContenedor($contenedor);
            }
            $entity->setSisgerCode($sisgerCode);
            if($entity->getContenedor()){
                if($entity->getContenedor()->getFechaSalida()){
                    $entity->setFechaHBL($entity->getContenedor()->getFechaSalida());
                }
            }
            $em->flush();
            $flash = $this->get('session')->getFlashBag();
            $flash->set('success', 'Los cambios han sido guardados satisfactoriamente.');
        } else {
            print_r($form->getErrors());
            die;
            $flash = $this->get('session')->getFlashBag();
            $flash->set('danger', 'No se ha podido completar la operación.');
        }

        return $this->redirect($this->generateUrl('ena'));
    }

    /**
     * Creates a form to create a ENA entity.
     *
     * @param ENA $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ENA $entity)
    {
        $form = $this->createForm(new ENAType(), $entity, array(
            'action' => $this->generateUrl('ena_create'),
            'method' => 'POST',
        ));


        return $form;
    }

    /**
     * Displays a form to create a new ENA entity.
     *
     */
    public function newAction()
    {
        $entity = new ENA();
        $form = $this->createCreateForm($entity);

        return $this->render('BackendBundle:ENA:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ENA entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:ENA')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ENA entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:ENA:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing ENA entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:ENA')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ENA entity.');
        }

        $editForm = $this->createEditForm($entity);

        $contenedor = $em->getRepository('BackendBundle:Contenedor')->findContenedorEnUso();
        $flag_hbl = 'no';
        if ($contenedor) {
            $flag_hbl = 'si';
        }

        return $this->render('BackendBundle:ENA:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'flag_hbl' => $flag_hbl
        ));
    }

    /**
     * Creates a form to edit a ENA entity.
     *
     * @param ENA $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ENA $entity)
    {
        $form = $this->createForm(new ENAEditType(), $entity, array(
            'action' => $this->generateUrl('ena_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));


        return $form;
    }

    /**
     * Edits an existing ENA entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:ENA')->find($id);
        $estado_original = $entity->getEstado();
        $contenedor_original = $entity->getContenedor();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ENA entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setConsignado($entity->getRemitente());

            $expediente = $em->getRepository('BackendBundle:Reply')->findOneBy(array('id' => $_POST['expediente']));
            if(!$expediente){
                $flash = $this->get('session')->getFlashBag();
                $flash->set('danger', 'No existe el expediente E-' . $_POST['expediente']);
                return $this->redirect($this->generateUrl('ena'));
            }
            $entity->setExpediente($expediente);

            if (array_key_exists('estado', $_POST)) {
                $entity->setEstado('HBL');
                // $entity->setFechaHBL(new \DateTime());
            }
            $now = new \DateTime();
            if ($estado_original != $entity->getEstado()) {
                $contenedor = $em->getRepository('BackendBundle:Contenedor')->findContenedorEnUso()[0];
                if ($contenedor->getId() != $contenedor_original->getId()) {
                    $limitePeso = $contenedor->getMaxPesoKg();
                    $limiteVolumen = $contenedor->getVolumenM3();
                    $valorPeso = $contenedor->getPesoKg() + $entity->getPesoKg();
                    $valorVolumen = $contenedor->getVolumenConceptosM3() + $entity->getVolumenM3();
                    if (($valorPeso <= $limitePeso) && ($valorVolumen <= $limiteVolumen)) {
                        /* if ($_POST['sisgerCode'] != "") {
                             $fecha = $_POST['sisgerCode'];
                             $fecha = date_create($fecha);*/
                        $sisgerCode = "BRS". date_format($now, 'y') . "000" . str_pad($entity->getId(), 6, 0, STR_PAD_LEFT);
                        //    $entity->setFechaHBL($fecha);
                        /*   } else {
                               $now = new \DateTime();
                               $sisgerCode = "LBRS" . date_format($now, 'y')[1] . date_format($now, 'm') . date_format($now, 'd') . str_pad($entity->getId(), 6, 0, STR_PAD_LEFT);
                               $entity->setFechaHBL($now);
                           }*/
                        $entity->setContenedor($contenedor);
                        $entity->setSisgerCode($sisgerCode);
                        if($entity->getContenedor()){
                            if($entity->getContenedor()->getFechaSalida()){
                                $entity->setFechaHBL($entity->getContenedor()->getFechaSalida());
                            }
                        }
                        $em->flush();
                        $flash = $this->get('session')->getFlashBag();
                        $flash->set('success', 'Los cambios han sido guardados satisfactoriamente.');
                    } else {
                        $flash = $this->get('session')->getFlashBag();
                        $flash->set('danger', 'No se ha podido completar la operación pues actualmente el contenedor en uso no tiene capacidad en peso y/o volumen para asumir este HBL.');
                    }
                } else {
                    /* if ($_POST['sisgerCode'] != "") {
                         $fecha = $_POST['sisgerCode'];
                         $fecha = date_create($fecha);*/
                    $sisgerCode = "BRS". date_format($now, 'y') . "000" . str_pad($entity->getId(), 6, 0, STR_PAD_LEFT);
                    // $entity->setFechaHBL($fecha);
                    /*   } else {
                           $now = new \DateTime();
                           $sisgerCode = "LBRS" . date_format($now, 'y')[1] . date_format($now, 'm') . date_format($now, 'd') . str_pad($entity->getId(), 6, 0, STR_PAD_LEFT);
                           $entity->setFechaHBL($now);
                       }*/
                    $entity->setSisgerCode($sisgerCode);
                    $em->flush();
                    $flash = $this->get('session')->getFlashBag();
                    $flash->set('success', 'Los cambios han sido guardados satisfactoriamente.');
                }
            } else {
                $contenedor = $entity->getContenedor();
                if ($contenedor == null) {
                    $entity->setContenedor($contenedor_original);
                    if($entity->getContenedor()){
                        if($entity->getContenedor()->getFechaSalida()){
                            $entity->setFechaHBL($entity->getContenedor()->getFechaSalida());
                        }
                    }
                    $em->flush();
                    $flash = $this->get('session')->getFlashBag();
                    $flash->set('success', 'Los cambios han sido guardados satisfactoriamente.');
                } else {
                    if ($contenedor_original == null) {
                        $limitePeso = $contenedor->getMaxPesoKg();
                        $limiteVolumen = $contenedor->getVolumenM3();

                        $valorPeso = $contenedor->getPesoKg() + $entity->getPesoKg();
                        $valorVolumen = $contenedor->getVolumenConceptosM3() + $entity->getVolumenM3();
                        if (($valorPeso <= $limitePeso) && ($valorVolumen <= $limiteVolumen)) {
                            $entity->setContenedor($contenedor);
                            if($entity->getContenedor()){
                                if($entity->getContenedor()->getFechaSalida()){
                                    $entity->setFechaHBL($entity->getContenedor()->getFechaSalida());
                                }
                            }
                            $em->flush();
                            $flash = $this->get('session')->getFlashBag();
                            $flash->set('success', 'Los cambios han sido guardados satisfactoriamente.');
                        } else {
                            $flash = $this->get('session')->getFlashBag();
                            $flash->set('danger', 'No se ha podido completar la operación pues actualmente el contenedor en uso no tiene capacidad en peso y/o volumen para asumir este HBL.');
                        }
                    } else if ($contenedor->getId() != $contenedor_original->getId()) {
                        $limitePeso = $contenedor->getMaxPesoKg();
                        $limiteVolumen = $contenedor->getVolumenM3();

                        $valorPeso = $contenedor->getPesoKg() + $entity->getPesoKg();
                        $valorVolumen = $contenedor->getVolumenConceptosM3() + $entity->getVolumenM3();
                        if (($valorPeso <= $limitePeso) && ($valorVolumen <= $limiteVolumen)) {
                            $entity->setContenedor($contenedor);
                            if($entity->getContenedor()){
                                if($entity->getContenedor()->getFechaSalida()){
                                    $entity->setFechaHBL($entity->getContenedor()->getFechaSalida());
                                }
                            }
                            $em->flush();
                            $flash = $this->get('session')->getFlashBag();
                            $flash->set('success', 'Los cambios han sido guardados satisfactoriamente.');
                        } else {
                            $flash = $this->get('session')->getFlashBag();
                            $flash->set('danger', 'No se ha podido completar la operación pues actualmente el contenedor en uso no tiene capacidad en peso y/o volumen para asumir este HBL.');
                        }
                    }
                }
            }
            $em->flush();
        } else {
            $flash = $this->get('session')->getFlashBag();
            $flash->set('danger', 'No se ha podido completar la operación');

        }
        return $this->redirect($this->generateUrl('ena'));
    }

    /**
     * Deletes a ENA entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Concepto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ENA entity.');
        }

        $em->remove($entity);
        $em->flush();
        $flash = $this->get('session')->getFlashBag();
        $flash->set('success', 'El elemento ha sido eliminado satisfactoriamente.');
        return $this->redirect($this->generateUrl('ena'));
    }

    /**
     * Creates a form to delete a ENA entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ena_delete', array('id' => $id)))
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
                $entity = $em->getRepository('BackendBundle:Concepto')->find($id);
                if ($entity) {
                    $em->remove($entity);
                }
            }
            $em->flush();
            $flash = $this->get('session')->getFlashBag();
            $flash->set('success', 'Los elementos eliminados satisfactoriamente.');
        }
        return $this->redirect($this->generateUrl('ena'));


    }

    public function indexExpAction($exp_id)
    {
        $em = $this->getDoctrine()->getManager();

        $activas = array();
        $cerradas = array();
        $entities = $em->getRepository('BackendBundle:ENA')->findOrdenadas();
        foreach ($entities as $ena) {
            if ($ena->getContenedor()) {
                if ($ena->getContenedor()->getEstado() == 'CERRADO') {
                    $cerradas[] = $ena;
                    break;
                }
            }
            $activas[] = $ena;
        }

        $entity = new ENA();
        $form = $this->createCreateForm($entity);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            array_merge($activas, $cerradas),
            $this->get('request')->query->get('page', 1),
            15);

        $contenedor = $em->getRepository('BackendBundle:Contenedor')->findContenedorEnUso();
        $flag_hbl = 'no';
        if ($contenedor) {
            $flag_hbl = 'si';
        }

        return $this->render('BackendBundle:ENA:index.html.twig', array(
            'entities' => $pagination,
            'form' => $form->createView(),
            'flag_hbl' => $flag_hbl,
            'exp_id' => $exp_id
        ));
    }
}
