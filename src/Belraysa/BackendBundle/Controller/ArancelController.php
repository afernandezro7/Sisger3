<?php

namespace Belraysa\BackendBundle\Controller;

use Belraysa\BackendBundle\Entity\Capitulo;
use Belraysa\BackendBundle\Entity\UM;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Belraysa\BackendBundle\Entity\Arancel;
use Belraysa\BackendBundle\Form\ArancelType;

/**
 * Arancel controller.
 *
 */
class ArancelController extends Controller
{

    /**
     * Lists all Arancel entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:Arancel')->findAll();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            103);
        $entity = new Arancel();
        $form = $this->createCreateForm($entity);

        return $this->render('BackendBundle:Arancel:index.html.twig', array(
            'entities' => $pagination,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new Arancel entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Arancel();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('arancel_show', array('id' => $entity->getId())));
        }

        return $this->render('BackendBundle:Arancel:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Arancel entity.
     *
     * @param Arancel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Arancel $entity)
    {
        $form = $this->createForm(new ArancelType(), $entity, array(
            'action' => $this->generateUrl('arancel_create'),
            'method' => 'POST',
        ));


        return $form;
    }

    /**
     * Displays a form to create a new Arancel entity.
     *
     */
    public function newAction()
    {
        $entity = new Arancel();
        $form = $this->createCreateForm($entity);

        return $this->render('BackendBundle:Arancel:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Arancel entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Arancel')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Arancel entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Arancel:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Arancel entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Arancel')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Arancel entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:Arancel:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Arancel entity.
     *
     * @param Arancel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Arancel $entity)
    {
        $form = $this->createForm(new ArancelType(), $entity, array(
            'action' => $this->generateUrl('arancel_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Arancel entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Arancel')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Arancel entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('arancel_edit', array('id' => $id)));
        }

        return $this->render('BackendBundle:Arancel:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Arancel entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Arancel')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Arancel entity.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('arancel'));
    }

    public
    function batchDeleteAction(Request $request)
    {

        $ids = $request->get('batch_action_checkbox');
        if ($ids) {
            $em = $this->getDoctrine()->getManager();
            foreach ($ids as $id) {
                $entity = $em->getRepository('BackendBundle:Arancel')->find($id);
                if ($entity) {
                    $em->remove($entity);
                }
            }
            $em->flush();
            $flash = $this->get('session')->getFlashBag();
            $flash->set('success', 'Los valores han sido eliminados satisfactoriamente.');
        }
        return $this->redirect($this->generateUrl('arancel'));


    }

    /**
     * Creates a form to delete a Arancel entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('arancel_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    public function uploadAction(Request $request)
    {
        //aqui se ejecuta el codigo del reader

        $uploaddir = $this->container->getParameter('belraysa.route.aranceles');
        $uploadfile = $uploaddir . basename($_FILES['path']['name']);
        if (move_uploaded_file($_FILES['path']['tmp_name'], $uploadfile)) {

            $objPHPExcel = \PHPExcel_IOFactory::load($uploadfile);

            $sheets = $objPHPExcel->getAllSheets();
            $cont = 1;
            $em = $this->getDoctrine()->getManager();
            foreach ($sheets as $sheet) {
                $sheetData = $sheet->toArray(null, true, true, true);
                if (trim($sheetData[1]["A"]) != null) {
                    $capitulo = $em->getRepository('BackendBundle:Capitulo')->findOneByNombre(trim($sheetData[1]["A"]));
                    if (!$capitulo) {
                        $capitulo = new Capitulo();
                        $capitulo->setNombre(trim($sheetData[1]["A"]));
                        $capitulo->setNumero($cont);
                        $em->persist($capitulo);
                        $em->flush();
                    }
                    for ($i = 3; $i <= sizeof($sheetData); $i++) {
                        $arancel = $sheetData[$i];
                        if (trim($arancel["B"]) != null && $em->getRepository('BackendBundle:Arancel')->findOneByArticulos(trim($arancel["C"]))) {
                            $entity = new Arancel();
                            $entity->setCapitulo($capitulo);
                            $entity->setArticulos(trim($arancel["B"]));

                            $um = $em->getRepository('BackendBundle:UM')->findOneByNombre(trim($arancel["C"]));
                            if (!$um) {
                                $um = new UM();
                                $um->setNombre(trim($arancel["C"]));
                                $em->persist($um);
                                $em->flush();
                            }

                            $entity->setUm($um);
                            $entity->setCantidad(trim($arancel["D"]));
                            $entity->setArancel(trim($arancel["E"]));
                            $entity->setObservaciones(trim($arancel["F"]));
                            $em->persist($entity);
                            $em->flush();
                        }
                    }
                }
                $cont++;
            }
        }
        return $this->redirect($this->generateUrl('arancel'));
    }

    public function filtrarAction()
    {
        $em = $this->getDoctrine()->getManager();

        $capitulo = null;
        $articulo = null;


        if (array_key_exists('arancel_capitulo', $_GET)) {
            if ($_GET['arancel_capitulo'] != '') {
                $capitulo = $_GET['arancel_capitulo'];
            }
        }

        if (array_key_exists('arancel_articulo', $_GET)) {
            if ($_GET['arancel_articulo'] != '') {
                $articulo = $_GET['arancel_articulo'];
            }
        }


        $aranceles = $em->getRepository('BackendBundle:Arancel')->findBusquedaAvanzada($capitulo, $articulo);


        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $aranceles,
            $this->get('request')->query->get('page', 1),
            103);
        $entity = new Arancel();
        $form = $this->createCreateForm($entity);

        return $this->render('BackendBundle:Arancel:filtered.html.twig', array(
            'entities' => $pagination,
            'capitulo' => $capitulo,
            'articulo' => $articulo,
            'form' => $form->createView()
        ));


    }
}
