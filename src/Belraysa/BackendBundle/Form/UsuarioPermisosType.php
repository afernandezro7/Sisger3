<?php

namespace Belraysa\BackendBundle\Form;

use Doctrine\ORM\EntityRepository;
use Evercode\DependentSelectBundle\Form\Type\DependentFilteredEntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UsuarioPermisosType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('role', null, array('required' => true))
            ->add('workspace', null, array('required' => true))
            //     ->add('nomencladoresLectura', null, array('required' => true))
            //      ->add('nomencladoresEscritura', null, array('required' => true))
        ;

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
        return 'belraysa_backendbundle_usuario_permisos';
    }
}
