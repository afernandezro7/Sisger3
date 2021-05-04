<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 10/12/2015
 * Time: 14:22
 */

namespace Belraysa\BackendBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', 'text', array(
                'attr' => array(
                    'placeholder' => 'Escriba su nombre'
                )
            ))
            ->add('email', 'email', array(
                'attr' => array(
                    'placeholder' => 'Email para que pueda responderle'
                )
            ))
            ->add('motivo', 'text', array(
                'attr' => array(
                    'placeholder' => 'El motivo de contactar con nosotros'
                )
            ))
            ->add('mensaje', 'textarea', array(
                'attr' => array(
                    'cols' => 90,
                    'rows' => 10,
                    'placeholder' => 'Mensaje que quieres enviarnos'
                )
            ));
        // ->add('save', 'submit', array('label' => 'Enviar'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {


        $resolver->setDefaults(array());
    }

    public function getName()
    {
        return 'contacto';
    }
}