<?php

namespace Belraysa\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Belraysa\BackendBundle\Form\ContactType;

/**
 * Contact controller.
 *
 */
class ContactController extends Controller
{

    public function contactAction(Request $query)
    {
        $form = $this->createForm(new ContactType());

        if ($query->isMethod('POST')) {
            $form->handleRequest($query);

            if ($form->isValid()) {
                $mailer = $this->get('mailer');
                $message = \Swift_Message::newInstance()
                    ->setSubject($form->get('motivo')->getData())
                    ->setFrom($form->get('email')->getData())
                    ->setTo('rocioxmp91@gmail.com')
                    ->setBody($this->renderView(
                        'BackendBundle:Notifications:contact-mail-1.html.twig', array('ip' => $query->getClientIp(),
                            'name' => $form->get('nombre')->getData(),
                            'from' => $form->get('email')->getData(),
                            'subject' => $form->get('motivo')->getData(),
                            'message' => $form->get('mensaje')->getData())), 'text/plain')
                    ->addPart($this->renderView(
                        'BackendBundle:Notifications:contact-mail-1.html.twig', array('ip' => $query->getClientIp(),
                            'name' => $form->get('nombre')->getData(),
                            'from' => $form->get('email')->getData(),
                            'subject' => $form->get('motivo')->getData(),
                            'message' => $form->get('mensaje')->getData())), 'text/html');

                //$mailer->send($message);

                return $this->render(
                    'BackendBundle:Notifications:contact-mail-1.html.twig', array('ip' => $query->getClientIp(),
                        'name' => $form->get('nombre')->getData(),
                        'from' => $form->get('email')->getData(),
                        'subject' => $form->get('motivo')->getData(),
                        'message' => $form->get('mensaje')->getData()));

                return $this->redirect($this->generateUrl('index'));
            }
        }

        return $this->render('BackendBundle:Default:contact_form.html.twig', array(
            'form' => $form->createView()
        ));
    }

}