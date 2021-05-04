<?php

namespace Belraysa\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Belraysa\BackendBundle\Entity\Mercancia;
use Belraysa\BackendBundle\Form\MercanciaType;
use Belraysa\BackendBundle\Entity\Bulto;

/**
 * Mercancia controller.
 *
 */
class MercanciaController extends Controller
{
    /**
     * Lists all Mercancia entities.
     *
     */
    public function indexAction($bulto)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:Mercancia')->findByBulto($bulto);
        $bulto = $em->getRepository('BackendBundle:Bulto')->find($bulto);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);
        $entity = new Mercancia();
        $entity->setBulto($bulto);
        $form = $this->createCreateForm($entity);

        return $this->render('BackendBundle:Mercancia:index.html.twig', array(
            'entities' => $pagination,
            'bulto' => $bulto,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new Mercancia entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $bultoService = $this->get('bulto_service');

        if($_POST['bulto'] == -1){
            $bulto = $bultoService->createBultoHelper($_POST['concepto_id'], $em);
        }
        else
            $bulto = $em->getRepository('BackendBundle:Bulto')->find($_POST['bulto']);

        $entity = new Mercancia();
        $entity->setBulto($bulto);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $valorAduanal = $bulto->getConcepto()->getValorAduanal();
            if (array_key_exists('miRelacionada', $_POST)) {
                if ($_POST['miRelacionada'] != 0) {
                    $relacionada = $em->getRepository('BackendBundle:Mercancia')->find($_POST['miRelacionada']);
                    $entity->setArancel($relacionada->getArancel());
                    $entity->setMiRelacionada($relacionada);
                } else {
                    $valorAduanal += $entity->getArancel()->getArancel() * $entity->getCantidad();
                }

            } else {
                $valorAduanal += $entity->getArancel()->getArancel() * $entity->getCantidad();
            }
            $limite = 200;
            if ($bulto->getConcepto()->getTipo() == 'ENA') {
                $limite = 1000;
            } elseif ($bulto->getConcepto()->getTipo() == 'Menaje') {
                $limite = 9999999;
            }
            if ($valorAduanal <= $limite) {

                $valorPeso = $bulto->getConcepto()->getPesoKg();
                $valorPeso += $entity->getPesoKg() * $entity->getCantidad();

                $valorVolumen = $bulto->getConcepto()->getVolumenM3();
                $valorVolumen += $entity->getVolumenM3() * $entity->getCantidad();

                $limitePeso = $bulto->getConcepto()->getContenedor()->getMaxPesoKg();
                $limiteVolumen = $bulto->getConcepto()->getContenedor()->getVolumenM3();

                if (($valorPeso <= $limitePeso) && ($valorVolumen <= $limiteVolumen)) {

                    $cantidad = $entity->getCantidad();

                    $entity->setCantidad(1);

                    $volumen = $_POST['volumen'];
                    if($volumen){
                        $entity->setAlturaCm(0);
                        $entity->setAnchoCm(0);
                        $entity->setProfundidadCm(0);
                        $entity->setVolumenM3($volumen);
                    }

                    else{
                        $altura = $_POST['altura'];
                        $umaltura = $_POST['umaltura'];
                        if ($umaltura == 'cm') {
                            $entity->setAlturaCm($altura);
                        }

                        $ancho = $_POST['ancho'];
                        $umancho = $_POST['umancho'];
                        if ($umancho == 'cm') {
                            $entity->setAnchoCm($ancho);
                        }

                        $profundidad = $_POST['profundidad'];
                        $umprofundidad = $_POST['umprofundidad'];
                        if ($umprofundidad == 'cm') {
                            $entity->setProfundidadCm($profundidad);
                        }

                        if ($altura && $ancho && $profundidad) {
                            $volumen = $altura * $ancho * $profundidad;

                            $volumenM3 = number_format($volumen / 1000000, 2);
                            if ($volumenM3 == 0) {
                                $volumenM3 = 0.01;
                            }
                            $entity->setVolumenM3($volumenM3);
                        }
                    }

                    $peso = $_POST['peso'];
                    $umpeso = $_POST['umpeso'];
                    if ($umpeso == 'kgs') {
                        $entity->setPesoKg($peso);
                        $entity->setPesoLb(number_format($peso * 2.2, 2));
                    } else {
                        $entity->setPesoLb($peso);
                        $entity->setPesoKg(number_format($peso / 2.2, 2));
                    }

                    $entity->setRelacion(number_format($volumen / 5000, 2));

                    $entity->setFechaCreacion(new \DateTime());
                    $em->persist($entity);
                    $em->flush();
                    if ($cantidad > 1) {
                        for ($i = 0; $i < $cantidad - 1; $i++) {
                            $new = clone $entity;

                            $em->persist($new);
                            $em->flush();

                        }
                    }
                } else {
                    $flash = $this->get('session')->getFlashBag();
                    $flash->set('danger', 'La mercancía que está intentando crear excede el valor en peso y/o volumen del contenedor al que pertence.');
                }
            } else {
                $flash = $this->get('session')->getFlashBag();
                $flash->set('danger', 'La mercancía que está intentando crear excede el valor aduanal permitido.');

            }

        }

        return $this->redirect($this->generateUrl('bulto', array('concepto' => $entity->getBulto()->getConcepto()->getId())));
    }

    /**
     * Creates a form to create a Mercancia entity.
     *
     * @param Mercancia $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Mercancia $entity)
    {
        $form = $this->createForm(new MercanciaType(), $entity, array(
            'action' => $this->generateUrl('mercancia_create'),
            'method' => 'POST',
        ));


        return $form;
    }

    /**
     * Displays a form to create a new Mercancia entity.
     *
     */
    public function newAction()
    {
        $entity = new Mercancia();
        $form = $this->createCreateForm($entity);

        return $this->render('BackendBundle:Mercancia:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Mercancia entity.
     *
     */
    public function showAction()
    {
        $em = $this->getDoctrine()->getManager();
        $id = $_GET['id'];
        $entity = $em->getRepository('BackendBundle:Mercancia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Mercancia entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('BackendBundle:Mercancia:show.html.twig', array(
            'mercancia' => $entity,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Mercancia entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Mercancia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Mercancia entity.');
        }

        $editForm = $this->createEditForm($entity);

        if ($entity->getBulto()->getConcepto()->getTipo() == 'ENA') {
            return $this->render('BackendBundle:Mercancia:edit.html.twig', array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
            ));
        } elseif ($entity->getBulto()->getConcepto()->getTipo() == 'Envio') {
            if ($entity->getMiRelacionada()) {
                return $this->render('BackendBundle:Mercancia:edit3.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                ));
            } else {
                return $this->render('BackendBundle:Mercancia:edit2.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                ));
            }
        } else {
            return $this->render('BackendBundle:Mercancia:edit.html.twig', array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
            ));
        }
    }

    /**
     * Creates a form to edit a Mercancia entity.
     *
     * @param Mercancia $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Mercancia $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new MercanciaType(), $entity, array(
            'action' => $this->generateUrl('mercancia_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        $form->add('tarifaAlternativa');
        $form->remove('cantidad');

        return $form;
    }

    /**
     * Edits an existing Mercancia entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Mercancia')->find($id);
        $currentVA = $entity->getArancel()->getArancel();
        $currentWeight = $entity->getPesoKg();
        $currentVolume = $entity->getVolumenM3();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Mercancia entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $relacionadaOriginal = $entity->getMiRelacionada();
        if ($editForm->isValid()) {
            $valorAduanal = $entity->getBulto()->getConcepto()->getValorAduanal();

            if (array_key_exists('miRelacionada', $_POST) && $_POST['miRelacionada'] != 0 && $relacionadaOriginal != $_POST['miRelacionada']) {
                $relacionada = $em->getRepository('BackendBundle:Mercancia')->find($_POST['miRelacionada']);
                $entity->setArancel($relacionada->getMiRelacionada()->getArancel());
                $entity->setMiRelacionada($relacionada);
            }

            $limite = 200;
            if ($entity->getBulto()->getConcepto()->getTipo() == 'ENA') {
                $limite = 1000;
            } elseif ($entity->getBulto()->getConcepto()->getTipo() == 'Menaje') {
                $limite = 9999999;
            }
            if ($valorAduanal <= $limite) {
                $bulto = $entity->getBulto();
                $valorPeso = $bulto->getConcepto()->getPesoKg();
                $valorPeso += $entity->getPesoKg() * $entity->getCantidad() - $currentWeight;

                $valorVolumen = $bulto->getConcepto()->getVolumenM3();
                $valorVolumen += $entity->getVolumenM3() * $entity->getCantidad() - $currentVolume;

                $limitePeso = $bulto->getConcepto()->getContenedor()->getMaxPesoKg();
                $limiteVolumen = $bulto->getConcepto()->getContenedor()->getVolumenM3();

                if (($valorPeso <= $limitePeso) && ($valorVolumen <= $limiteVolumen)) {

                    $volumen = $_POST['volumen'];
                    if($volumen){
                        $entity->setAlturaCm(0);
                        $entity->setAnchoCm(0);
                        $entity->setProfundidadCm(0);
                        $entity->setVolumenM3($volumen);
                    }

                    else{
                        $altura = $_POST['altura'];
                        $umaltura = $_POST['umaltura'];
                        if ($umaltura == 'cm') {
                            $entity->setAlturaCm($altura);
                        }

                        $ancho = $_POST['ancho'];
                        $umancho = $_POST['umancho'];
                        if ($umancho == 'cm') {
                            $entity->setAnchoCm($ancho);
                        }

                        $profundidad = $_POST['profundidad'];
                        $umprofundidad = $_POST['umprofundidad'];
                        if ($umprofundidad == 'cm') {
                            $entity->setProfundidadCm($profundidad);
                        }

                        if ($altura && $ancho && $profundidad) {
                            $volumen = $altura * $ancho * $profundidad;

                            $volumenM3 = number_format($volumen / 1000000, 2);
                            if ($volumenM3 == 0) {
                                $volumenM3 = 0.01;
                            }
                            $entity->setVolumenM3($volumenM3);
                        }
                    }

                    $peso = $_POST['peso'];
                    $umpeso = $_POST['umpeso'];
                    if ($umpeso == 'kgs') {
                        $entity->setPesoKg($peso);
                        $entity->setPesoLb(number_format($peso * 2.2, 2));
                    } else {
                        $entity->setPesoLb($peso);
                        $entity->setPesoKg(number_format($peso / 2.2, 2));
                    }

                    $entity->setRelacion(number_format($volumen / 5000, 2));


                    $em->flush();
                } else {
                    $flash = $this->get('session')->getFlashBag();
                    $flash->set('danger', 'La mercancía que está intentando crear excede el valor en peso y/o volumen del contenedor al que pertence.');
                }
            } else {
                $flash = $this->get('session')->getFlashBag();
                $flash->set('danger', 'La mercancía que está intentando modificar excede el valor aduanal permitido.');

            }
        }

        return $this->redirect($this->generateUrl('bulto', array('concepto' => $entity->getBulto()->getConcepto()->getId())));
    }

    public function moveAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Mercancia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Mercancia entity.');
        }

        $bulto_original = $entity->getBulto();
        $target = $_POST['target'];
        $target = $em->getRepository('BackendBundle:Bulto')->find($target);


        $bulto_original->removeMercancia($entity);
        $target->addMercancia($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('bulto', array('concepto' => $entity->getBulto()->getConcepto()->getId())));
    }

    /**
     * Deletes a Mercancia entity.
     *
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $_GET['id'];
        $entity = $em->getRepository('BackendBundle:Mercancia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Mercancia entity.');
        }

        $bulto = $entity->getBulto();

        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('bulto', array('concepto' => $bulto->getConcepto()->getId())));
    }

    /**
     * Creates a Mercancia entity, if there is no bulto creates a new one and assign the mercancy to this, it is also used
     * for creating another empty bulto.
     *
     */
    public function masBultoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $bultoService = $this->get('bulto_service');

        if($_POST['bulto'] == -1){
            $bulto = $bultoService->createBultoHelper($_POST['concepto_id'], $em);
        }
        else
            $bulto = $em->getRepository('BackendBundle:Bulto')->find($_POST['bulto']);

        $entity = new Mercancia();
        $entity->setBulto($bulto);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $valorAduanal = $bulto->getConcepto()->getValorAduanal();
            if (array_key_exists('miRelacionada', $_POST)) {
                if ($_POST['miRelacionada'] != 0) {
                    $relacionada = $em->getRepository('BackendBundle:Mercancia')->find($_POST['miRelacionada']);
                    $entity->setArancel($relacionada->getArancel());
                    $entity->setMiRelacionada($relacionada);
                } else {
                    $valorAduanal += $entity->getArancel()->getArancel() * $entity->getCantidad();
                }

            } else {
                $valorAduanal += $entity->getArancel()->getArancel() * $entity->getCantidad();
            }
            $limite = 200;
            if ($bulto->getConcepto()->getTipo() == 'ENA') {
                $limite = 950;
            } elseif ($bulto->getConcepto()->getTipo() == 'Menaje') {
                $limite = 9999999;
            }
            if ($valorAduanal <= $limite) {

                $valorPeso = $bulto->getConcepto()->getPesoKg();
                $valorPeso += $entity->getPesoKg() * $entity->getCantidad();

                $valorVolumen = $bulto->getConcepto()->getVolumenM3();
                $valorVolumen += $entity->getVolumenM3() * $entity->getCantidad();

                $limitePeso = $bulto->getConcepto()->getContenedor()->getMaxPesoKg();
                $limiteVolumen = $bulto->getConcepto()->getContenedor()->getVolumenM3();

                if (($valorPeso <= $limitePeso) && ($valorVolumen <= $limiteVolumen)) {

                    $cantidad = $entity->getCantidad();

                    $entity->setCantidad(1);

                    $volumen = $_POST['volumen'];
                    if($volumen){
                        $entity->setAlturaCm(0);
                        $entity->setAnchoCm(0);
                        $entity->setProfundidadCm(0);
                        $entity->setVolumenM3($volumen);
                    }

                    else{
                        $altura = $_POST['altura'];
                        $umaltura = $_POST['umaltura'];
                        if ($umaltura == 'cm') {
                            $entity->setAlturaCm($altura);
                        }

                        $ancho = $_POST['ancho'];
                        $umancho = $_POST['umancho'];
                        if ($umancho == 'cm') {
                            $entity->setAnchoCm($ancho);
                        }

                        $profundidad = $_POST['profundidad'];
                        $umprofundidad = $_POST['umprofundidad'];
                        if ($umprofundidad == 'cm') {
                            $entity->setProfundidadCm($profundidad);
                        }

                        if ($altura && $ancho && $profundidad) {
                            $volumen = $altura * $ancho * $profundidad;

                            $volumenM3 = number_format($volumen / 1000000, 2);
                            if ($volumenM3 == 0) {
                                $volumenM3 = 0.01;
                            }
                            $entity->setVolumenM3($volumenM3);
                        }
                    }

                    $peso = $_POST['peso'];
                    $umpeso = $_POST['umpeso'];
                    if ($umpeso == 'kgs') {
                        $entity->setPesoKg($peso);
                        $entity->setPesoLb(number_format($peso * 2.2, 2));
                    } else {
                        $entity->setPesoLb($peso);
                        $entity->setPesoKg(number_format($peso / 2.2, 2));
                    }

                    $entity->setRelacion(number_format($volumen / 5000, 2));

                    $entity->setFechaCreacion(new \DateTime());
                    $em->persist($entity);
                    $em->flush();
                    if ($cantidad > 1) {
                        for ($i = 0; $i < $cantidad - 1; $i++) {
                            $new = clone $entity;

                            $em->persist($new);
                            $em->flush();

                        }
                    }
                } else {
                    $flash = $this->get('session')->getFlashBag();
                    $flash->set('danger', 'La mercancía que está intentando crear excede el valor en peso y/o volumen del contenedor al que pertence.');
                }
            } else {
                $flash = $this->get('session')->getFlashBag();
                $flash->set('danger', 'La mercancía que está intentando crear excede el valor aduanal permitido.');

            }

        }

        if($_POST['mas_bulto'] == "true"){
            $bulto = $bultoService->createBultoHelper($_POST['concepto_id'], $em);
        }
        return new JsonResponse(array('indice' => $bulto->getIndice(), 'id' => $bulto->getId(), 'mercancia_id' => $entity->getId(), 'mercancia_desc' => $entity->getDescripcion(), 'mercancia_indice' => $entity->getBulto()->getIndice()));
    }

    /**
     * Creates a form to delete a Mercancia entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mercancia_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }


}

