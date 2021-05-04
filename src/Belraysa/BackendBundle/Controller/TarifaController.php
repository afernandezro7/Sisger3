<?php

namespace Belraysa\BackendBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Belraysa\BackendBundle\Entity\Tarifa;
use Belraysa\BackendBundle\Form\TarifaType;

/**
 * Tarifa controller.
 *
 */
class TarifaController extends Controller
{

    /**
     * Lists all Tarifa entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:Tarifa')->findOrdenadas();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);
        $entity = new Tarifa();
        $form = $this->createCreateForm($entity);

        return $this->render('BackendBundle:Tarifa:index.html.twig', array(
            'entities' => $pagination,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new Tarifa entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Tarifa();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tarifa_show', array('id' => $entity->getId())));
        }

        return $this->render('BackendBundle:Tarifa:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Tarifa entity.
     *
     * @param Tarifa $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Tarifa $entity)
    {
        $form = $this->createForm(new TarifaType(), $entity, array(
            'action' => $this->generateUrl('tarifa_create'),
            'method' => 'POST',
        ));


        return $form;
    }

    /**
     * Displays a form to create a new Tarifa entity.
     *
     */
    public function newAction()
    {
        $entity = new Tarifa();
        $form = $this->createCreateForm($entity);

        return $this->render('BackendBundle:Tarifa:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Tarifa entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Tarifa')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tarifa entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Tarifa:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Tarifa entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Tarifa')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tarifa entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Tarifa:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Tarifa entity.
     *
     * @param Tarifa $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Tarifa $entity)
    {
        $form = $this->createForm(new TarifaType(), $entity, array(
            'action' => $this->generateUrl('tarifa_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Tarifa entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Tarifa')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tarifa entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tarifa_edit', array('id' => $id)));
        }

        return $this->render('BackendBundle:Tarifa:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Tarifa entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Tarifa')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tarifa entity.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('tarifa'));
    }

    public
    function batchDeleteAction(Request $request)
    {

        $ids = $request->get('batch_action_checkbox');
        if ($ids) {
            $em = $this->getDoctrine()->getManager();
            foreach ($ids as $id) {
                $entity = $em->getRepository('BackendBundle:Tarifa')->find($id);
                if ($entity) {
                    $em->remove($entity);
                }
            }
            $em->flush();
            $flash = $this->get('session')->getFlashBag();
            $flash->set('success', 'Los valores han sido eliminados satisfactoriamente.');
        }
        return $this->redirect($this->generateUrl('tarifa'));


    }

    /**
     * Creates a form to delete a Tarifa entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tarifa_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    public function uploadAction(Request $request)
    {
        //aqui se ejecuta el codigo del reader

        $uploaddir = $this->container->getParameter('belraysa.route.tarifas');
        $uploadfile = $uploaddir . basename($_FILES['path']['name']);
        if (move_uploaded_file($_FILES['path']['tmp_name'], $uploadfile)) {

            $objPHPExcel = \PHPExcel_IOFactory::load($uploadfile);

            $sheets = $objPHPExcel->getAllSheets();
            $cont = 1;
            $em = $this->getDoctrine()->getManager();
            foreach ($sheets as $sheet) {
                $sheetData = $sheet->toArray(null, true, true, true);
                for ($i = 3; $i <= sizeof($sheetData); $i++) {
                    $tarifa = $sheetData[$i];
                    if (trim($tarifa["A"]) != null) {
                        if (!$em->getRepository('BackendBundle:Tarifa')->findByNumero($tarifa["A"])) {
                            $entity = new Tarifa();
                            $entity->setNumero($tarifa["A"]);
                            $entity->setArticulo(trim($tarifa["B"]));
                            $entity->setPrecio(trim($tarifa["C"]));
                            $em->persist($entity);
                            $em->flush();
                        }
                    }
                }

            }
        }
        return $this->redirect($this->generateUrl('tarifa'));
    }

    public function filtrarAction()
    {
        $em = $this->getDoctrine()->getManager();

        $precio_desde = null;
        $precio_hasta = null;
        $articulo = null;


        if (array_key_exists('tarifa_precio_desde', $_GET)) {
            if ($_GET['tarifa_precio_desde'] != '') {
                $precio_desde = $_GET['tarifa_precio_desde'];
            }
        }

        if (array_key_exists('tarifa_precio_hasta', $_GET)) {
            if ($_GET['tarifa_precio_hasta'] != '') {
                $precio_hasta = $_GET['tarifa_precio_hasta'];
            }
        }


        if (array_key_exists('tarifa_articulo', $_GET)) {
            if ($_GET['tarifa_articulo'] != '') {
                $articulo = $_GET['tarifa_articulo'];
            }
        }


        $tarifas = $em->getRepository('BackendBundle:Tarifa')->findBusquedaAvanzada($articulo, $precio_desde, $precio_hasta);


        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $tarifas,
            $this->get('request')->query->get('page', 1),
            10);
        $entity = new Tarifa();
        $form = $this->createCreateForm($entity);

        return $this->render('BackendBundle:Tarifa:filtered.html.twig', array(
            'entities' => $pagination,
            'articulo' => $articulo,
            'precio_desde' => $precio_desde,
            'precio_hasta' => $precio_hasta,
            'form' => $form->createView()
        ));


    }
}
