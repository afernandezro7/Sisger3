<?php

namespace Belraysa\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('firstName')
            ->add('lastName')
            ->add('email', 'email', array('required' => false))
            ->add('phone', null, array('required' => false))
            ->add('letra', null)
            ->add('password', 'password', array(
                'label' => 'Password',
                'attr' => array('class' => 'form-control', 'minlength' => 2),
                'label_attr' => array('class' => 'control-label')
            ))
            ->add('role', null, array('required' => true))
            ->add('workspace', null, array('required' => true))
          //  ->add('nomencladoresLectura', null, array('required' => true))
          //  ->add('nomencladoresEscritura', null, array('required' => true))
            ->add('photo', 'file', array('required' => false, 'data_class' => null,))
            ->add('firma', 'file', array('required' => false, 'data_class' => null,));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Belraysa\BackendBundle\Entity\User',

        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'belraysa_backendbundle_user';
    }
}
