<?php

namespace Belraysa\BackendBundle\Controller;

use Belraysa\BackendBundle\Entity\Bank;
use Belraysa\BackendBundle\Entity\Card;
use Belraysa\BackendBundle\Entity\Cash;
use Belraysa\BackendBundle\Form\BankType;
use Belraysa\BackendBundle\Form\CardType;
use Belraysa\BackendBundle\Form\CashType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Belraysa\BackendBundle\Entity\Banking;
use Belraysa\BackendBundle\Form\BankingType;

/**
 * Banking controller.
 *
 */
class BankingController extends Controller
{

    /**
     * Lists all Banking entities.
     *
     */
    public function indexAllAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:Banking')->findAll();

        $cash = new Cash();
        $cash->setEnabled(true);
        $form_cash = $this->createForm(new CashType(), $cash, array(
            'action' => $this->generateUrl('cash_create'),
            'method' => 'POST',
        ));

        $card = new Card();
        $card->setEnabled(true);
        $form_card = $this->createForm(new CardType(), $card, array(
            'action' => $this->generateUrl('card_create'),
            'method' => 'POST',
        ));

        $bank = new Bank();
        $bank->setEnabled(true);
        $form_bank = $this->createForm(new BankType(), $bank, array(
            'action' => $this->generateUrl('bank_create'),
            'method' => 'POST',
        ));

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);

        return $this->render('BackendBundle:Banking:indexAll.html.twig', array(
            'entities' => $pagination,
            'form_cash' => $form_cash->createView(),
            'form_card' => $form_card->createView(),
            'form_bank' => $form_bank->createView(),
            'cash' => $cash,
            'card' => $card,
            'bank' => $bank,
        ));
    }

    public function indexAction($ws)
    {
        $em = $this->getDoctrine()->getManager();
        $ws = $em->getRepository('BackendBundle:Workspace')->findOneByName($ws);
        $entities = $ws->getBankings();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);
        return $this->render('BackendBundle:Banking:index.html.twig', array(
            'entities' => $pagination,
            'ws' => $ws
        ));
    }

    /**
     * Creates a new Banking entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Banking();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $ws = $em->getRepository('BackendBundle:Banking')->find($_POST['ws']);
        if ($form->isValid()) {

            $em->persist($entity);
            $em->flush();


        }

        return $this->redirect($this->generateUrl('banking', array('workspace' => $ws->getId())));
    }

    /**
     * Creates a form to create a Banking entity.
     *
     * @param Banking $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Banking $entity)
    {
        $form = $this->createForm(new BankingType(), $entity, array(
            'action' => $this->generateUrl('banking_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Banking entity.
     *
     */
    public function newAction()
    {
        $entity = new Banking();
        $form = $this->createCreateForm($entity);

        return $this->render('BackendBundle:Banking:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Banking entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Banking')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Banking entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Banking:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Banking entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Banking')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Banking entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Banking:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Banking entity.
     *
     * @param Banking $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Banking $entity)
    {
        $form = $this->createForm(new BankingType(), $entity, array(
            'action' => $this->generateUrl('banking_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Banking entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Banking')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Banking entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();


        }

        return $this->redirect($this->generateUrl('banking', array('workspace' => $entity->getWorkspace()->getId())));
    }

    /**
     * Deletes a Banking entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Banking')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Banking entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('banking_all'));
    }

    /**
     * Creates a form to delete a Banking entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('banking_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    public function batchDeleteAction(Request $request, $workspace)
    {
        $em = $this->getDoctrine()->getManager();
        $ids = $request->get('batch_action_checkbox');
        if ($ids) {
            foreach ($ids as $id) {
                $entity = $em->getRepository('BackendBundle:Banking')->find($id);
                if ($entity) {
                    $em->remove($entity);
                }
            }
            $em->flush();
            $flash = $this->get('session')->getFlashBag();
            $flash->set('success', 'Los elementos han sido eliminados satisfactoriamente.');
        }
        return $this->redirect($this->generateUrl('banking', array('workspace' => $em->getRepository('BackendBundle:Workspace')->find($workspace))));


    }

    public function batchDeleteAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $ids = $request->get('batch_action_checkbox');
        if ($ids) {
            foreach ($ids as $id) {
                $entity = $em->getRepository('BackendBundle:Banking')->find($id);
                if ($entity) {
                    $em->remove($entity);
                }
            }
            $em->flush();
            $flash = $this->get('session')->getFlashBag();
            $flash->set('success', 'Los elementos han sido eliminados satisfactoriamente.');
        }
        return $this->redirect($this->generateUrl('banking_all'));


    }

    /*  public function getBankingsByPaymentMethodAction()
      {

          $em = $this->getDoctrine()->getManager();
          $paymentMethod = $em->getRepository('BackendBundle:PaymentMethod')->findOneByName($_POST['pm']);

          $bankings = array();

          if ($paymentMethod) {
              $bankingsDelPM = $paymentMethod->getBankings();
              $bankingsDelWS = $this->get('belraysa.workspace')->getCurrentWorkspace()->getBankings();
              foreach ($bankingsDelWS as $bws) {
                  $flag = false;
                  foreach ($bankingsDelPM as $bpm) {
                      if ($bpm->getId() == $bws->getId()) {
                          $bankings[] = $bpm;
                          $flag = true;
                      }
                  }
                  if (!$flag && (sizeof($bws->getMetodosPago()) == 0)) {
                      $bankings[] = $bws;
                  }
              }
          }

          $json = array();

          for ($i = 0; $i < sizeof($bankings); $i++) {
              $json[$i]['id'] = $bankings[$i]->getId();
              $json[$i]['name'] = $bankings[$i]->getName();
          }


          return new JsonResponse($json);


      }*/

    public function getBankingsByPaymentMethodAction()
    {

        $em = $this->getDoctrine()->getManager();
        $paymentMethod = $em->getRepository('BackendBundle:PaymentMethod')->findOneByName($_POST['pm']);

        $bankings = array();

        if ($paymentMethod) {
            $bankingsDelPM = $paymentMethod->getBankings();
            if ($this->get('belraysa.workspace')->getCurrentWorkspace()->getName() != 'G-BRS') {
                $bankingsDelWS = $this->get('belraysa.workspace')->getCurrentWorkspace()->getBankings();
                foreach ($bankingsDelPM as $bpm) {
                    foreach ($bankingsDelWS as $bws) {
                        if ($bpm->getId() == $bws->getId()) {
                            $bankings[] = $bpm;
                        }
                    }
                }
            } else {
                foreach ($bankingsDelPM as $bpm) {
                    $bankings[] = $bpm;
                }
            }
        }

        $json = array();

        for ($i = 0; $i < sizeof($bankings); $i++) {
            $json[$i]['id'] = $bankings[$i]->getId();
            $json[$i]['name'] = $bankings[$i]->getName();
        }

        //print_r($json);
        //die;
        return new JsonResponse($json);


    }
}
