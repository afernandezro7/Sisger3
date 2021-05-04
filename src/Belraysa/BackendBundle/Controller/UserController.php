<?php

namespace Belraysa\BackendBundle\Controller;

use Belraysa\BackendBundle\Entity\Permiso;
use Belraysa\BackendBundle\Form\ProfileType;
use Belraysa\BackendBundle\Form\UsuarioAvatarType;
use Belraysa\BackendBundle\Form\UsuarioInfoGeneralType;
use Belraysa\BackendBundle\Form\UsuarioPasswordType;
use Belraysa\BackendBundle\Form\UsuarioPermisosType;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Belraysa\BackendBundle\Entity\User;
use Belraysa\BackendBundle\Form\UserType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * User controller.
 *
 */
class UserController extends Controller
{

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('BackendBundle:User')->findByActivo(true);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10);

        return $this->render('BackendBundle:User:index.html.twig', array(
            'entities' => $pagination,
        ));
    }

    public function createAction(Request $request)
    {
        $entity = new User();
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('user_create'),
            'method' => 'POST',
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->uploadPhoto($this->container->getParameter('belraysa.route.photos_users'));
            if($entity->getFirma()){
                 $entity->uploadFirma($this->container->getParameter('belraysa.route.firmas_users'));
            }
            $encoder = $this->get('security.encoder_factory')->getEncoder($entity);
            $entity->setSalt(md5(time()));
            $codificatedPassword = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
            $entity->setPassword($codificatedPassword);
            $entity->setActivo(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $flash = $this->get('session')->getFlashBag();
            $flash->set('success', 'Los cambios han sido guardados satisfactoriamente.');
            return $this->redirect($this->generateUrl('user'));
        }

        return $this->render('BackendBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    public function newAction()
    {
        $entity = new User();

        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('user_create'),
            'method' => 'POST',
        ));

        return $this->render('BackendBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    public function loadWorkspaceNomencladoresUserNewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = new User();
        $workspace_id = $_POST['belraysa_backendbundle_user']['workspace'];
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('user_create'),
            'method' => 'POST',
        ));
        $form->remove('nomencladoresLectura');
        $form->add('nomencladoresLectura', 'entity', array(
                'class' => 'BackendBundle:Nomenclador',
                'query_builder' => function (EntityRepository $er) use ($workspace_id) {
                    $query = $er->createQueryBuilder('n')
                        ->select(array('n'))
                        ->where('n.workspace = :workspace_id')
                        ->setParameter('workspace_id', $workspace_id)
                        ->orderBy('n.nombre', 'ASC');

                    return $query;
                }
            )
        );
        $form->remove('nomencladoresEscritura');
        $form->add('nomencladoresEscritura', 'entity', array(
                'class' => 'BackendBundle:Nomenclador',
                'query_builder' => function (EntityRepository $er) use ($workspace_id) {
                    $query = $er->createQueryBuilder('n')
                        ->select(array('n'))
                        ->where('n.workspace = :workspace_id')
                        ->setParameter('workspace_id', $workspace_id)
                        ->orderBy('n.nombre', 'ASC');

                    return $query;
                }
            )
        );


        return $this->render('BackendBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));

    }

    public function loadWorkspaceNomencladoresAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:User')->find($_POST['user_id']);
        $workspace_id = $_POST['belraysa_backendbundle_usuario_permisos']['workspace'];
        $form_permisos = $this->createForm(new UsuarioPermisosType(), $entity, array(
            'action' => $this->generateUrl('user_update_permisos', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        $form_permisos->remove('nomencladoresLectura');
        $form_permisos->add('nomencladoresLectura', 'entity', array(
                'class' => 'BackendBundle:Nomenclador',
                'query_builder' => function (EntityRepository $er) use ($workspace_id) {
                    $query = $er->createQueryBuilder('n')
                        ->select(array('n'))
                        ->where('n.workspace = :workspace_id')
                        ->setParameter('workspace_id', $workspace_id)
                        ->orderBy('n.nombre', 'ASC');

                    return $query;
                }
            )
        );
        $form_permisos->remove('nomencladoresEscritura');
        $form_permisos->add('nomencladoresEscritura', 'entity', array(
                'class' => 'BackendBundle:Nomenclador',
                'query_builder' => function (EntityRepository $er) use ($workspace_id) {
                    $query = $er->createQueryBuilder('n')
                        ->select(array('n'))
                        ->where('n.workspace = :workspace_id')
                        ->setParameter('workspace_id', $workspace_id)
                        ->orderBy('n.nombre', 'ASC');

                    return $query;
                }
            )
        );

        $form_info_general = $this->createForm(new UsuarioInfoGeneralType(), $entity, array(
            'action' => $this->generateUrl('user_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        $form_password = $this->createForm(new UsuarioPasswordType(), $entity, array(
            'action' => $this->generateUrl('user_update_password', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        $form_avatar = $this->createForm(new UsuarioAvatarType(), $entity, array(
            'action' => $this->generateUrl('user_update_avatar', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        return $this->render('BackendBundle:User:profile.html.twig', array(
            'user' => $entity,
            'edit_info_general_form' => $form_info_general->createView(),
            'edit_password' => $form_password->createView(),
            'edit_avatar' => $form_avatar->createView(),
            'edit_permisos' => $form_permisos->createView(),
            'tab' => 'false'
        ));

    }

    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:User')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $form_info_general = $this->createForm(new UsuarioInfoGeneralType(), $entity, array(
            'action' => $this->generateUrl('user_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        $form_password = $this->createForm(new UsuarioPasswordType(), $entity, array(
            'action' => $this->generateUrl('user_update_password', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        $form_avatar = $this->createForm(new UsuarioAvatarType(), $entity, array(
            'action' => $this->generateUrl('user_update_avatar', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        $form_permisos = $this->createForm(new UsuarioPermisosType(), $entity, array(
            'action' => $this->generateUrl('user_update_permisos', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        return $this->render('BackendBundle:User:profile.html.twig', array(
            'user' => $entity,
            'edit_info_general_form' => $form_info_general->createView(),
            'edit_password' => $form_password->createView(),
            'edit_avatar' => $form_avatar->createView(),
            'edit_permisos' => $form_permisos->createView(),
            'tab' => 'false'
        ));
    }

    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

 $firma = $entity->getFirma();
 
        $form_info_general = $this->createForm(new UsuarioInfoGeneralType(), $entity, array(
            'action' => $this->generateUrl('user_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        $form_password = $this->createForm(new UsuarioPasswordType(), $entity, array(
            'action' => $this->generateUrl('user_update_password', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        $form_avatar = $this->createForm(new UsuarioAvatarType(), $entity, array(
            'action' => $this->generateUrl('user_update_avatar', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        $form_permisos = $this->createForm(new UsuarioPermisosType(), $entity, array(
            'action' => $this->generateUrl('user_update_permisos', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        $form_info_general->handleRequest($request);

        if ($form_info_general->isValid()) {
            $replies = $em->getRepository('BackendBundle:Reply')->findAll();
            foreach ($replies as $reply) {
                $reply->setWorkspace($reply->getUser()->getWorkspace());

                foreach ($reply->getRecibos() as $receipe) {
                    if ($receipe->getUsuario()->getId() == $entity->getId()) {
                        $workspace = $receipe->getUsuario()->getWorkspace();
                        $workspace->addRecibo($receipe);
                    }
                }
                foreach ($reply->getVouchers() as $voucher) {
                    if ($voucher->getUser()->getId() == $entity->getId()) {
                        $workspace = $voucher->getUser()->getWorkspace();
                        if ($workspace) {
                        }
                        $workspace->addVoucher($voucher);
                    }
                }
            }
            
              if (null == $entity->getFirma() && $firma!=null) {
                $entity->setFirma($firma);
            } else {
                $entity->uploadFirma($this->container->getParameter('belraysa.route.firmas_users'));
            }
            
            $em->flush();
            $flash = $this->get('session')->getFlashBag();
            $flash->set('success', 'Los cambios han sido guardados satisfactoriamente.');
            return $this->redirect($this->generateUrl('user_show', array('id' => $entity->getId())) . '#tab_1-1');
        }
        $flash = $this->get('session')->getFlashBag();
        $flash->set('danger', 'No se ha podido completar la operación.');
        return $this->render('BackendBundle:User:profile.html.twig', array(
            'user' => $entity,
            'edit_info_general_form' => $form_info_general->createView(),
            'edit_password' => $form_password->createView(),
            'edit_avatar' => $form_avatar->createView(),
            'edit_permisos' => $form_permisos->createView(),
            'tab' => '#tab_1-1'
        ));
    }

    public function updatePasswordAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $password = $entity->getPassword();
        $form_info_general = $this->createForm(new UsuarioInfoGeneralType(), $entity, array(
            'action' => $this->generateUrl('user_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        $form_password = $this->createForm(new UsuarioPasswordType(), $entity, array(
            'action' => $this->generateUrl('user_update_password', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        $form_avatar = $this->createForm(new UsuarioAvatarType(), $entity, array(
            'action' => $this->generateUrl('user_update_avatar', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        $form_permisos = $this->createForm(new UsuarioPermisosType(), $entity, array(
            'action' => $this->generateUrl('user_update_permisos', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        $form_password->handleRequest($request);

        if ($form_password->isValid()) {
            // El usuario no cambia su contraseña, utilizar la original
            if (null == $entity->getPassword()) {
                $entity->setPassword($password);
            } else {
                // El usuario cambia su contraseña
                //Codificating password
                $encoder = $this->get('security.encoder_factory')->getEncoder($entity);
                $entity->setSalt(md5(time()));
                $codificatedPassword = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
                $entity->setPassword($codificatedPassword);
            }
            $em->flush();
            $flash = $this->get('session')->getFlashBag();
            $flash->set('success', 'Los cambios han sido guardados satisfactoriamente.');
            return $this->redirect($this->generateUrl('user_show', array('id' => $entity->getId())) . '#tab_3-3');
        }
        $flash = $this->get('session')->getFlashBag();
        $flash->set('danger', 'No se ha podido completar la operación.');
        return $this->render('BackendBundle:User:profile.html.twig', array(
            'user' => $entity,
            'edit_info_general_form' => $form_info_general->createView(),
            'edit_password' => $form_password->createView(),
            'edit_avatar' => $form_avatar->createView(),
            'edit_permisos' => $form_permisos->createView(),
            'tab' => '#tab_3-3'
        ));
    }

    public function updateAvatarAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $photo = $entity->getPhoto();
       

        $form_info_general = $this->createForm(new UsuarioInfoGeneralType(), $entity, array(
            'action' => $this->generateUrl('user_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        $form_password = $this->createForm(new UsuarioPasswordType(), $entity, array(
            'action' => $this->generateUrl('user_update_password', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        $form_avatar = $this->createForm(new UsuarioAvatarType(), $entity, array(
            'action' => $this->generateUrl('user_update_avatar', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        $form_permisos = $this->createForm(new UsuarioPermisosType(), $entity, array(
            'action' => $this->generateUrl('user_update_permisos', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        $form_avatar->handleRequest($request);

        if ($form_avatar->isValid()) {
            if (null == $entity->getPhoto()) {
                $entity->setPhoto($photo);
            } else {
                $entity->uploadPhoto($this->container->getParameter('belraysa.route.photos_users'));
            }
            
          
            
            $em->flush();
            $flash = $this->get('session')->getFlashBag();
            $flash->set('success', 'Los cambios han sido guardados satisfactoriamente.');
            return $this->redirect($this->generateUrl('user_show', array('id' => $entity->getId())) . '#tab_2-2');
        }
        $flash = $this->get('session')->getFlashBag();
        $flash->set('danger', 'No se ha podido completar la operación.');
        return $this->render('BackendBundle:User:profile.html.twig', array(
            'user' => $entity,
            'edit_info_general_form' => $form_info_general->createView(),
            'edit_password' => $form_password->createView(),
            'edit_avatar' => $form_avatar->createView(),
            'edit_permisos' => $form_permisos->createView(),
            'tab' => '#tab_2-2'
        ));
    }

    public function updatePermisosAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }


        $form_info_general = $this->createForm(new UsuarioInfoGeneralType(), $entity, array(
            'action' => $this->generateUrl('user_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        $form_password = $this->createForm(new UsuarioPasswordType(), $entity, array(
            'action' => $this->generateUrl('user_update_password', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        $form_avatar = $this->createForm(new UsuarioAvatarType(), $entity, array(
            'action' => $this->generateUrl('user_update_avatar', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        $form_permisos = $this->createForm(new UsuarioPermisosType(), $entity, array(
            'action' => $this->generateUrl('user_update_permisos', array('id' => $entity->getId())),
            'method' => 'POST',
        ));


        $form_permisos->handleRequest($request);

        if ($form_permisos->isValid()) {
            $em->flush();
            $flash = $this->get('session')->getFlashBag();
            $flash->set('success', 'Los cambios han sido guardados satisfactoriamente.');
            return $this->redirect($this->generateUrl('user_show', array('id' => $entity->getId())) . '#tab_4-4');
        }else{
            print_r($form_permisos->getErrors());
            die;
        }
        $flash = $this->get('session')->getFlashBag();
        $flash->set('danger', 'No se ha podido completar la operación.');
        return $this->render('BackendBundle:User:profile.html.twig', array(
            'user' => $entity,
            'edit_info_general_form' => $form_info_general->createView(),
            'edit_password' => $form_password->createView(),
            'edit_avatar' => $form_avatar->createView(),
            'edit_permisos' => $form_permisos->createView(),
            'tab' => '#tab_4-4'
        ));
    }

    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:User')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        $entity->setActivo(false);
        $em->flush();

        $flash = $this->get('session')->getFlashBag();
        $flash->set('success', 'El usuario ha sido eliminado satisfactoriamente.');
        return $this->redirect($this->generateUrl('user'));
    }

    public function batchDeleteAction(Request $request)
    {
        $ids = $request->get('batch_action_checkbox');
        if ($ids) {
            $em = $this->getDoctrine()->getManager();
            foreach ($ids as $id) {
                $entity = $em->getRepository('BackendBundle:User')->find($id);
                if ($entity) {
                    $entity->setActivo(false);
                }
            }
            $em->flush();
            $flash = $this->get('session')->getFlashBag();
            $flash->set('success', 'Los usuarios han sido eliminados satisfactoriamente.');
        }
        return $this->redirect($this->generateUrl('user'));
    }

    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR, $session->get(SecurityContext::AUTHENTICATION_ERROR));

        return $this->render('BackendBundle:Default:login.html.twig', array('last_username' => $session->get(SecurityContext::LAST_USERNAME), 'error' => $error));

    }

    public function switchWorkspaceAction(Request $request, $name)
    {
        $em = $this->getDoctrine()->getManager();
        $workspace = $em->getRepository('BackendBundle:Workspace')->findOneByName($name);
        $session = $request->getSession();
        $session->set('workspace', $workspace);
        return $this->redirect($this->generateUrl('index'));
    }

    public function getOperatorsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $operators = $em->getRepository('BackendBundle:User')->findAll();

        $operatorsArray = array();

        foreach ($operators as $operator) {
            $operatorsArray[] = $operator->getFirstName();
        }

        foreach ($operatorsArray as $val) {
            $json[] = sprintf('%s', (is_string($val) ? $val : json_encode($val)));
        }
        return new JsonResponse($json);
    }

    public function getOperatorsByWsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $operators = $em->getRepository('BackendBundle:User')->findByWorkspace($_POST['ws']);

        $operatorsArray = array();

        foreach ($operators as $operator) {
            $operatorsArray[] = $operator->getFirstName();
        }

        $json = array();

        foreach ($operatorsArray as $val) {
            $json[] = sprintf('%s', (is_string($val) ? $val : json_encode($val)));
        }
        return new JsonResponse($json);
    }

    public function getLengthAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:User')->findAll();
        $length = sizeof($entities);

        return new JsonResponse("{$length}");
    }

    public function verificarPasswordAction()
    {
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('BackendBundle:User')->find($_POST['user']);
        $password = $_POST['password'];

        $encoder = $this->get('security.encoder_factory')->getEncoder($usuario);

        if ($encoder->isPasswordValid($usuario->getPassword(), $password, $usuario->getSalt())) {
            $flag = 'OK';
            $mensaje = 'Las contraseñas coinciden';
        } else {
            $flag = 'ERROR';
            $mensaje = 'La contraseña especificada no coincide con la actual';
        }
        return new JsonResponse(array('status' => $flag, 'message' => $mensaje));
    }

    public function verificarUsernameAction()
    {
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('BackendBundle:User')->find($_POST['user']);
        $username = $_POST['username'];
        $usuarios = $em->getRepository('BackendBundle:User')->findByUsername($username);
        $flag = 'OK';
        $mensaje = 'OK';
        if (sizeof($usuarios) > 1) {
            $flag = 'ERROR';
            $mensaje = 'El nombre de usuario especificado ya existe';
        } else if (sizeof($usuarios) == 1) {
            if (!$usuario) {
                $flag = 'ERROR';
                $mensaje = 'El nombre de usuario especificado ya existe';
            } else {
                if ($usuarios[0]->getId() != $usuario->getId()) {
                    $flag = 'ERROR';
                    $mensaje = 'El nombre de usuario especificado ya existe';
                }
            }
        } else {
            $flag = 'OK';
            $mensaje = 'OK';
        }
        return new JsonResponse(array('status' => $flag, 'message' => $mensaje));
    }

    public function verificarLetraAction()
    {
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('BackendBundle:User')->find($_POST['user']);
        $letra = $_POST['letra'];
        $flag = 'OK';
        $mensaje = 'OK';
        if (strlen($letra) != 1) {
            $flag = 'ERROR';
            $mensaje = 'Este campo solo admite un caracter de tipo alfabético.';
            return new JsonResponse(array('status' => $flag, 'message' => $mensaje));
        }
        $usuarios = $em->getRepository('BackendBundle:User')->findByLetra($letra);

        if (sizeof($usuarios) > 1) {
            $flag = 'ERROR';
            $mensaje = 'La letra especificada pertenece a otro usuario';
        } else if (sizeof($usuarios) == 1) {
            if (!$usuario) {
                $flag = 'ERROR';
                $mensaje = 'La letra especificada pertenece a otro usuario';
            } else {
                if ($usuarios[0]->getId() != $usuario->getId()) {
                    $flag = 'ERROR';
                    $mensaje = 'La letra especificada pertenece a otro usuario';
                }
            }
        } else {
            $flag = 'OK';
            $mensaje = 'OK';
        }
        return new JsonResponse(array('status' => $flag, 'message' => $mensaje));
    }

    public function filtrarAction()
    {
        $em = $this->getDoctrine()->getManager();
        $usuarios = $em->getRepository('BackendBundle:User')->findBusquedaSimple($_POST['query']);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $usuarios,
            $this->get('request')->query->get('page', 1),
            10);

        $flash = $this->get('session')->getFlashBag();
        $flash->set('success', 'Se obtuvieron ' . sizeof($usuarios) . ' resultados.');
        return $this->render('BackendBundle:User:index.html.twig', array(
            'entities' => $pagination
        ));
    }

}
