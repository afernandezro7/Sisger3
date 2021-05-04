<?php

namespace Belraysa\BackendBundle\Controller;

use Belraysa\BackendBundle\Entity\BankingEntry;
use Belraysa\BackendBundle\Entity\IdIngreso;
use Belraysa\BackendBundle\Entity\Ingreso;
use Belraysa\BackendBundle\Form\IngresoType;
use Belraysa\BackendBundle\Repository\ServiceRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Ingreso controller.
 *
 */
class IngresoController extends Controller
{
    /**
     * Creates a new Ingreso entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Ingreso();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $reply = null;
        if (array_key_exists('idReply', $_POST)) {
            $reply = $em->getRepository('BackendBundle:Reply')->findOneBySisgerCode($_POST['idReply']);
            $entity->setExpediente($reply);
        }
        if ($form->isValid()) {
            $entity->setTipo('Ingreso');
            $entity->setCreacion(new \DateTime());
            $entity->setRecibiDe($entity->getCliente()->getFirstName() . ' ' . $entity->getCliente()->getLastName());
            if (!$entity->getFecha()) {
                $entity->setFecha(new \DateTime());
            }
            $user = $this->get('security.context')->getToken()->getUser();
            $workspace = $reply->getWorkspace();
            $entity->setWorkspace($workspace);
            $entity->setUsuario($user);
            $entity->setActivo(true);
            $entity->setSaldoPendiente($entity->getSaldoAnterior() - $entity->getImporte());
            if($entity->getImporte() == 0){
                $entity->setCuentaXCobrar(true);                
            }
            else $entity->setCuentaXCobrar(false);

            if($entity->getSaldoPendiente() == 0)
                $entity->setFechaLimite(null);

            if (!$entity->getRefExpediente() && $reply != null) {
                $entity->setRefExpediente($reply->getSisgerCode());
            }
            
            if(array_key_exists('firmar', $_POST)){
                $firma = true;
                $entity->setFirmado(true);
            }

            $entity2 = clone $entity;
            if($entity->getSaldoPendiente() > 0 && $entity->getImporte() != 0){
                $entity2->setCuentaXCobrar(true);
                $entity2->setImporte(0);
                $entity2->setSaldoAnterior($entity->getSaldoPendiente());
                $entity2->setSaldoPendiente($entity->getSaldoPendiente());
                $entity2->setSuma('');
                $entity2->setFechaLimite($entity->getFechaLimite());
                $entity->setFechaLimite(null);

                $em->persist($entity2);
            }

            $em->persist($entity);     
            $em->flush();       

            $workspace->addRecibo($entity);

            $idIngreso = new IdIngreso();
            $em->persist($idIngreso);
            $em->flush();
            $entity->setIdIngreso($idIngreso);
            $entity->setSisgerCode('I-' . $idIngreso->getId());

            if($entity->getSaldoPendiente() > 0 && $entity->getImporte() != 0){
                $idIngreso2 = new IdIngreso();
                $em->persist($idIngreso2);
                $em->flush();
                $entity2->setIdIngreso($idIngreso2);
                $entity2->setSisgerCode('I-' . $idIngreso2->getId());                
            }

            $em->flush();

            if (array_key_exists('receptor', $_POST)) {
                $banking = $em->getRepository('BackendBundle:Banking')->find($_POST['receptor']);
                $banking->setBalance($banking->getBalance() + $entity->getImporte());
                $entry = new BankingEntry();
                $entry->setDate($entity->getFecha());
                $entry->setCredit($entity->getImporte());
                $entry->setDebit(0);
                $entry->setBalance($banking->getBalance());
                $detalles = "Servicios: ";
                $i = 0;
                foreach ($entity->getServicios() as $servicio) {
                    if ($i > 0) {
                        $detalles = $detalles . ', ';
                    }
                    $detalles = $detalles . $servicio->getName();
                    $i++;
                }
                if ($entity->getDetalles()) {
                    $detalles = $detalles . '. ' . $entity->getDetalles();
                }
                $entry->setDetails($detalles);
                $entry->setRecibo($entity);
                $entry->setActivo(true);

                if($entity->getSaldoPendiente() > 0 && $entity->getImporte() != 0){
                    $entry2 = clone $entry;
                    $entry2->setRecibo($entity2);
                    $entry2->setCredit($entity2->getImporte());
                    $em->persist($entry2);

                    $entity2->setEntrada($entry2);
                    $banking->addEntry($entry2);
                }
                
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
     * Creates a form to create a Ingreso entity.
     *
     * @param Ingreso $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Ingreso $entity)
    {
        $form = $this->createForm(new IngresoType(), $entity, array(
            'action' => $this->generateUrl('ingreso_create'),
            'method' => 'POST',
        ));
        $form->add('servicios', null, array(
            'required' => true,
            'choices' => function (ServiceRepository $repository) {
                $qb = $repository->createQueryBuilder('servicio')->where('servicio.workspace = :ws')->setParameter('ws', $this->get('belraysa.workspace')->getCurrentWorkspace()->getId());
                return $qb;
            }));

        return $form;
    }

    /**
     * Displays a form to create a new Ingreso entity.
     *
     */
    public function newAction(Request $request, $idReply)
    {
        $entity = new Ingreso();

        $em = $this->getDoctrine()->getManager();
        $reply = $em->getRepository('BackendBundle:Reply')->find($idReply);

        $entity->setExpediente($reply);
        $entity->setWorkspace($reply->getWorkspace());

        $form = $this->createCreateForm($entity);
        $user = $this->get('security.context')->getToken()->getUser();
        $session = $request->getSession();
        $workspace = $user->getWorkspace();
        if ($session->has('workspace')) {
            $workspace = $em->getRepository('BackendBundle:Workspace')->find($session->get('workspace')->getId());

        }

        return $this->render('BackendBundle:Ingreso:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'services' => $em->getRepository('BackendBundle:Service')->findByWorkspace($reply->getWorkspace()),
            'workspace' => $workspace
        ));
    }
}
