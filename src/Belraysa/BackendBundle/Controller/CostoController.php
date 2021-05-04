<?php

namespace Belraysa\BackendBundle\Controller;

use Belraysa\BackendBundle\Entity\BankingEntry;
use Belraysa\BackendBundle\Entity\IdEgreso;
use Belraysa\BackendBundle\Repository\ConceptoCostoRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Belraysa\BackendBundle\Entity\Costo;
use Belraysa\BackendBundle\Form\CostoType;

/**
 * Costo controller.
 *
 */
class CostoController extends Controller
{

    /**
     * Lists all Costo entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:Costo')->findAll();

        return $this->render('BackendBundle:Costo:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Costo entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Costo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $reply = null;

        if ($form->isValid()) {
            $entity->setTipo('Costo');
            if(!$entity->getFecha()) {
                $entity->setFecha(new \DateTime());
            }
            $user = $this->get('security.context')->getToken()->getUser();
            $workspace = $this->get('belraysa.workspace')->getCurrentWorkspace();
            $entity->setWorkspace($workspace);
            $entity->setUsuario($user);
            $entity->setActivo(true);
            $entity->setImporte(0 - $entity->getImporte());
            if (!$entity->getRefExpediente() && $reply != null) {
                $entity->setRefExpediente($reply->getSisgerCode());
            }
            $em->persist($entity);
            $em->flush();

            $workspace->addRecibo($entity);

if(array_key_exists('firmar', $_POST)){
    $firma = true;
    $entity->setFirmado(true);
}
            $idEgreso = new IdEgreso();
            $em->persist($idEgreso);
            $em->flush();
            $entity->setIdEgreso($idEgreso);
            $entity->setSisgerCode('E-' . $idEgreso->getId());
            $em->flush();

            if (array_key_exists('receptor', $_POST)) {
                $banking = $em->getRepository('BackendBundle:Banking')->find($_POST['receptor']);
                $banking->setBalance($banking->getBalance() + $entity->getImporte());
                $entry = new BankingEntry();
                $entry->setDate($entity->getFecha());
                $entry->setCredit(0);
                $entry->setDebit($entity->getImporte());
                $entry->setBalance($banking->getBalance());
                $entry->setDetails($entity->getDetalles());
                $entry->setRecibo($entity);
                $entry->setActivo(true);
                $em->persist($entry);
                $em->flush();
                $entity->setEntrada($entry);
                $banking->addEntry($entry);
                $em->flush();
            }

            return $this->redirect($this->generateUrl('recibo_generate_pdf', array('reciboId' => $entity->getId())));

        } else {
            $flash = $this->get('session')->getFlashBag();
            $flash->set('danger', 'Ha ocurrido un error en el procesamiento de los datos enviados. Revise que ha completado correctamente todos los campos.');
            return $this->redirect($this->generateUrl('reply_show', array('id' => $reply->getId())));
        }
    }

    /**
     * Creates a form to create a Costo entity.
     *
     * @param Costo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Costo $entity)
    {
        $form = $this->createForm(new CostoType(), $entity, array(
            'action' => $this->generateUrl('costo_create'),
            'method' => 'POST',
        ));
        $form->add('conceptosCosto', null, array(
            'required' => true,
            'choices' => function (ConceptoCostoRepository $repository) {
                $qb = $repository->createQueryBuilder('conceptoCosto')->where('conceptoCosto.workspace = :ws')->setParameter('ws', $this->get('belraysa.workspace')->getCurrentWorkspace()->getId());
                return $qb;
            }));

        return $form;
    }

    /**
     * Displays a form to create a new Costo entity.
     *
     */
    public function newAction(Request $request, $idReply)
    {
        $entity = new Costo();

        $em = $this->getDoctrine()->getManager();
        if ($idReply == 0000) {
            $entity->setExpediente(null);
            $workspace = $this->get('belraysa.workspace')->getCurrentWorkspace();
            $entity->setWorkspace($workspace);
        }else{
            $reply = $em->getRepository('BackendBundle:Reply')->find($idReply);

            $entity->setExpediente($reply);
            $entity->setWorkspace($reply->getWorkspace());
        }
        $form = $this->createCreateForm($entity);
        $user = $this->get('security.context')->getToken()->getUser();
        $session = $request->getSession();
        $workspace = $user->getWorkspace();
        if ($session->has('workspace')) {
            $workspace = $em->getRepository('BackendBundle:Workspace')->find($session->get('workspace')->getId());

        }


        return $this->render('BackendBundle:Costo:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'services' => $em->getRepository('BackendBundle:ConceptoCosto')->findByWorkspace($entity->getWorkspace()),
            'workspace' => $workspace
        ));
    }

    /**
     * Finds and displays a Costo entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Costo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Costo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Costo:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Costo entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Costo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Costo entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Costo:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Costo entity.
    *
    * @param Costo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Costo $entity)
    {
        $form = $this->createForm(new CostoType(), $entity, array(
            'action' => $this->generateUrl('costo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Costo entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Costo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Costo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('costo_edit', array('id' => $id)));
        }

        return $this->render('BackendBundle:Costo:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Costo entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackendBundle:Costo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Costo entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('costo'));
    }

    /**
     * Creates a form to delete a Costo entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('costo_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
