<?php

namespace Belraysa\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ExtraType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('importe', null, array('required' => true))
            ->add('banking', null, array('required' => true))
            ->add('concepto', 'textarea', array('required' => true));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Belraysa\BackendBundle\Entity\Extra'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'belraysa_backendbundle_extra';
    }
}
