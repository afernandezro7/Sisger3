<?php

namespace Belraysa\BackendBundle\Controller;

use Belraysa\BackendBundle\Entity\BankingEntry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Belraysa\BackendBundle\Entity\Traspaso;
use Belraysa\BackendBundle\Form\TraspasoType;

/**
 * Traspaso controller.
 *
 */
class TraspasoController extends Controller
{

    /**
     * Lists all Traspaso entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:Traspaso')->findTraspasosOnDescendantOrder();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);
        return $this->render('BackendBundle:Traspaso:index.html.twig', array(
            'entities' => $pagination,
        ));
    }

    /**
     * Creates a new Traspaso entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Traspaso();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isValid()) {

            if ($entity->getImporte() <= $entity->getOrigen()->getBalance()) {
                if ($entity->getOrigen()->getId() != $entity->getDestino()->getId()) {

                    $concept = "";
                    $i = 0;
                    $entity->setConcepto($_POST['concept']);
                    $user = $this->get('security.context')->getToken()->getUser();
                    $entity->setUser($this->get('security.context')->getToken()->getUser());
                    $entity->setCreationDate(new \DateTime());
                    $em->persist($entity);
                    $em->flush();

                    $idSisger = str_pad($entity->getId(), 4, "0", STR_PAD_LEFT);
                    $idOrigen = str_pad($entity->getOrigen()->getId(), 4, "0", STR_PAD_LEFT);
                    $idDestino = str_pad($entity->getDestino()->getId(), 4, "0", STR_PAD_LEFT);
                    //$em = $this->getDoctrine()->getManager();
                    $entity->setSisgerCode('T-O:' . $idOrigen . 'D:' . $idDestino . '-' . $idSisger);
                    $em->flush();

                    $banking = $entity->getOrigen();
                    $banking->setBalance($banking->getBalance() - $entity->getImporte());
                    $entry = new BankingEntry();
                    $entry->setDate($entity->getCreationDate());
                    $entry->setCredit(0);
                    $entry->setDebit(0 - $entity->getImporte());
                    $entry->setBalance($banking->getBalance());
                    $entry->setDetails($entity->getConcepto());
                    $entry->setOrigenTraspaso($entity);
                    $entry->setActivo(true);
                    $em->persist($entry);
                    $em->flush();
                    $entity->setEntryOrigen($entry);
                    $banking->addEntry($entry);
                    $em->flush();

                    $banking = $entity->getDestino();
                    $banking->setBalance($banking->getBalance() + $entity->getImporte());
                    $entry = new BankingEntry();
                    $entry->setDate($entity->getCreationDate());
                    $entry->setCredit($entity->getImporte());
                    $entry->setDebit(0);
                    $entry->setBalance($banking->getBalance());
                    $entry->setDetails($entity->getConcepto());
                    $entry->setDestinoTraspaso($entity);
                    $entry->setActivo(true);
                    $em->persist($entry);
                    $em->flush();
                    $entity->setEntryDestino($entry);
                    $banking->addEntry($entry);
                    $em->flush();
                } else {
                    $flash = $this->get('session')->getFlashBag();
                    $flash->set('danger', 'El origen y destino especificados coinciden.');
                }
            } else {
                $flash = $this->get('session')->getFlashBag();
                $flash->set('danger', 'No tiene saldo suficiente para traspasar el monto especificado.');
            }
        } else {
            $flash = $this->get('session')->getFlashBag();
            $flash->set('danger', 'Ha ocurrido un error en el procesamiento de los datos enviados. Revise que ha completado correctamente todos los campos.');

        }

        return $this->redirect($this->generateUrl('traspaso'));
    }

    /**
     * Creates a form to create a Traspaso entity.
     *
     * @param Traspaso $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Traspaso $entity)
    {
        $form = $this->createForm(new TraspasoType(), $entity, array(
            'action' => $this->generateUrl('traspaso_create'),
            'method' => 'POST',
        ));


        return $form;
    }

    /**
     * Displays a form to create a new Traspaso entity.
     *
     */
    public function newAction()
    {
        $entity = new Traspaso();
        $form = $this->createCreateForm($entity);

        return $this->render('BackendBundle:Traspaso:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Traspaso entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Traspaso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Traspaso entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Traspaso:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Traspaso entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Traspaso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Traspaso entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Traspaso:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Traspaso entity.
     *
     * @param Traspaso $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Traspaso $entity)
    {
        $form = $this->createForm(new TraspasoType(), $entity, array(
            'action' => $this->generateUrl('traspaso_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Traspaso entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Traspaso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Traspaso entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('traspaso_edit', array('id' => $id)));
        }

        return $this->render('BackendBundle:Traspaso:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Traspaso entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Traspaso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Traspaso entity.');
        }
        if ($entity->getImporte() <= $entity->getOrigen()->getBalance()) {


            $new = new Traspaso();
            $new->setConcepto('Cancelacion del traspaso ' . $entity->getSisgerCode());
            $new->setUser($this->get('security.context')->getToken()->getUser());
            $new->setCreationDate(new \DateTime());
            $new->setImporte($entity->getImporte());
            $new->setSuma($entity->getSuma());
            $new->setOrigen($entity->getDestino());
            $new->setDestino($entity->getOrigen());
            $em->persist($new);
            $em->flush();

            $idSisger = str_pad($new->getId(), 4, "0", STR_PAD_LEFT);
            $idOrigen = str_pad($entity->getDestino()->getId(), 4, "0", STR_PAD_LEFT);
            $idDestino = str_pad($entity->getOrigen()->getId(), 4, "0", STR_PAD_LEFT);
            //$em = $this->getDoctrine()->getManager();
            $new->setSisgerCode('T-O:' . $idOrigen . 'D:' . $idDestino . '-' . $idSisger);
            $em->flush();

            $banking = $new->getOrigen();
            $banking->setBalance($banking->getBalance() - $new->getImporte());
            $entry = new BankingEntry();
            $entry->setDate($entity->getCreationDate());
            $entry->setCredit(0);
            $entry->setDebit(0 - $entity->getImporte());
            $entry->setBalance($banking->getBalance());
            $entry->setOrigenTraspaso($new);
            $entry->setDetails("Movimiento de saldo por cancelacion de traspaso ". $entity->getSisgerCode());

            $entry->setActivo(true);
            $em->persist($entry);
            $em->flush();
            $new->setEntryOrigen($entry);
            $banking->addEntry($entry);
            $em->flush();

            $banking = $new->getDestino();
            $banking->setBalance($banking->getBalance() + $entity->getImporte());
            $entry = new BankingEntry();
            $entry->setDate($entity->getCreationDate());
            $entry->setCredit($entity->getImporte());
            $entry->setDebit(0);
            $entry->setBalance($banking->getBalance());
            $entry->setDestinoTraspaso($new);
            $entry->setDetails("Movimiento de saldo por cancelacion de traspaso ". $entity->getSisgerCode());

            $entry->setActivo(true);
            $em->persist($entry);
            $em->flush();
            $new->setEntryDestino($entry);
            $banking->addEntry($entry);
            $em->flush();

        } else {
            $flash = $this->get('session')->getFlashBag();
            $flash->set('danger', 'No tiene saldo suficiente para traspasar el monto especificado.');
        }

        return $this->redirect($this->generateUrl('traspaso'));
    }

    /**
     * Creates a form to delete a Traspaso entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('traspaso_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
}
