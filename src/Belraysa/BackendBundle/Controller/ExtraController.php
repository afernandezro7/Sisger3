<?php

namespace Belraysa\BackendBundle\Controller;

use Belraysa\BackendBundle\Entity\BankingEntry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Belraysa\BackendBundle\Entity\Extra;
use Belraysa\BackendBundle\Form\ExtraType;

/**
 * Extra controller.
 *
 */
class ExtraController extends Controller
{

    /**
     * Lists all Extra entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:Extra')->findExtrasOnDescendantOrder();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);
        return $this->render('BackendBundle:Extra:index.html.twig', array(
            'entities' => $pagination,
        ));
    }

    /**
     * Creates a new Extra entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Extra();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isValid()) {

          

                if ($_POST['tipo'] == 'Egreso') {
                  if ($entity->getImporte() <= $entity->getBanking()->getBalance()) {
                    $entity->setImporte(0 - $entity->getImporte());
                    } else {
                $flash = $this->get('session')->getFlashBag();
                $flash->set('danger', 'No tiene saldo suficiente para registrar el egreso.');
                    return $this->redirect($this->generateUrl('extra'));
            }
                }

                $entity->setUsuario($this->get('security.context')->getToken()->getUser());

                $entity->setFecha(new \DateTime());
                $em->persist($entity);
                $em->flush();

                $idSisger = str_pad($entity->getId(), 4, "0", STR_PAD_LEFT);
                $idBanking = str_pad($entity->getBanking()->getId(), 4, "0", STR_PAD_LEFT);
                if ($_POST['tipo'] == 'Egreso') {
                    $entity->setSisgerCode('E-' . $idBanking . '-' . $idSisger);
                } else {
                    $entity->setSisgerCode('I-' . $idBanking . '-' . $idSisger);
                }
                $em->flush();

                $banking = $entity->getBanking();
                $banking->setBalance($banking->getBalance() + $entity->getImporte());
                $entry = new BankingEntry();
                $entry->setDate($entity->getFecha());
                if ($_POST['tipo'] == 'Egreso') {
                    $entry->setCredit(0);
                    $entry->setDebit($entity->getImporte());
                } else {
                    $entry->setCredit($entity->getImporte());
                    $entry->setDebit(0);
                }
                $entry->setBalance($banking->getBalance());
                $entry->setDetails($entity->getConcepto());
                $entry->setExtra($entity);
                $entry->setActivo(true);
                $em->persist($entry);
                $em->flush();
                $entity->setEntrada($entry);
                $banking->addEntry($entry);
                $em->flush();

            
        } else {
            $flash = $this->get('session')->getFlashBag();
            $flash->set('danger', 'Ha ocurrido un error en el procesamiento de los datos enviados. Revise que ha completado correctamente todos los campos.');

        }

        return $this->redirect($this->generateUrl('extra'));
    }

    /**
     * Creates a form to create a Extra entity.
     *
     * @param Extra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Extra $entity)
    {
        $form = $this->createForm(new ExtraType(), $entity, array(
            'action' => $this->generateUrl('extra_create'),
            'method' => 'POST',
        ));


        return $form;
    }

    /**
     * Displays a form to create a new Extra entity.
     *
     */
    public function newAction()
    {
        $entity = new Extra();
        $form = $this->createCreateForm($entity);

        return $this->render('BackendBundle:Extra:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Extra entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Extra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Extra entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Extra:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Extra entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Extra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Extra entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Extra:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Extra entity.
     *
     * @param Extra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Extra $entity)
    {
        $form = $this->createForm(new ExtraType(), $entity, array(
            'action' => $this->generateUrl('extra_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Extra entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Extra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Extra entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('extra_edit', array('id' => $id)));
        }

        return $this->render('BackendBundle:Extra:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Extra entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackendBundle:Extra')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Extra entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('extra'));
    }

    /**
     * Creates a form to delete a Extra entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('extra_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    public function filtrarAction()
    {
        $em = $this->getDoctrine()->getManager();

        $aavv = null;
        $c507 = null;
        $lbrs = null;

        $from = null;
        $to = null;

        $sisgerCode = null;
        $user = null;

        $tipo = null;
        $importe_desde = null;
        $importe_hasta = null;
        $cuenta = null;


        $mes = null;
        $anno = null;

        $ws = null;

        if (array_key_exists('extra_sisgerCode', $_GET)) {
            if ($_GET['extra_sisgerCode'] != '') {
                $sisgerCode = $_GET['extra_sisgerCode'];
            }
        }

        if (array_key_exists('extra_from', $_GET)) {
            if ($_GET['extra_from'] != '') {
                $date1 = date_create_from_format('d/m/Y', $_GET['extra_from']);
                $from = new \DateTime($date1->format("Y-m-d") . " 00:00:00");
            }
        }

        if (array_key_exists('extra_to', $_GET)) {
            if ($_GET['extra_to'] != '') {
                $date2 = date_create_from_format('d/m/Y', $_GET['extra_to']);
                $to = new \DateTime($date2->format("Y-m-d") . " 00:00:00");
            }
        }

        if (array_key_exists('extra_user', $_GET)) {
            if ($_GET['extra_user'] != '') {
                $user = $_GET['extra_user'];
            }
        }

        if (array_key_exists('extra_tipo', $_GET)) {
            if ($_GET['extra_tipo'] != '') {
                $tipo = $_GET['extra_tipo'];

            }
        }

        if (array_key_exists('extra_importe_desde', $_GET)) {
            if ($_GET['extra_importe_desde'] != '') {
                $importe_desde = $_GET['extra_importe_desde'];
            }
        }

        if (array_key_exists('extra_importe_hasta', $_GET)) {
            if ($_GET['extra_importe_hasta'] != '') {
                $importe_hasta = $_GET['extra_importe_hasta'];
            }
        }

        if (array_key_exists('extra_cuenta', $_GET)) {
            if ($_GET['extra_cuenta'] != '') {
                $cuenta = $_GET['extra_cuenta'];
            }
        }


        $recibos = $em->getRepository('BackendBundle:Extra')->findBusquedaAvanzada(true, $aavv, $c507, $lbrs, $sisgerCode, $from, $to, $user, $tipo, $importe_desde, $importe_hasta, $cuenta);
        $total = $em->getRepository('BackendBundle:Extra')->findImporteBusquedaAvanzada(true, $aavv, $c507, $lbrs, $sisgerCode, $from, $to, $user, $tipo, $importe_desde, $importe_hasta, $cuenta);

        if (array_key_exists('buscar', $_GET)) {
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $recibos,
                $this->get('request')->query->get('page', 1),
                10);

            if ($from) {
                $from = date_format($from, 'd/m/Y');
            }
            if ($to) {
                $to = date_format($to, 'd/m/Y');
            }


            return $this->render('BackendBundle:Extra:filtered.html.twig', array(
                'entities' => $pagination,
                'aavv' => $aavv,
                'c507' => $c507,
                'lbrs' => $lbrs,
                'sisgerCode' => $sisgerCode,
                'from' => $from,
                'to' => $to,
                'user' => $user,
                'tipo' => $tipo,
                'importe_desde' => $importe_desde,
                'importe_hasta' => $importe_hasta,
                'cuenta' => $cuenta,
                'entorno' => $ws
            ));
        } else {
            if ($from) {
                $from = date_format($from, 'd/m/Y');
            }
            if ($to) {
                $to = date_format($to, 'd/m/Y');
            }
            $range = '';
            if ($from && $to) {
                $range = $range . $from . ' - ' . $to;
            } elseif ($from) {
                $range = 'Desde ' . $from;
            } elseif ($to) {
                $range = 'Hasta ' . $to;
            }

            return $this->render('BackendBundle:Extra:reporte.html.twig', array(
                'recibos' => $recibos,
                'total' => $total[0][1],
                'range' => $range,


            ));
        }
    }
}
