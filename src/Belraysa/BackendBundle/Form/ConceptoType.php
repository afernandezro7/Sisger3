<?php

namespace Belraysa\BackendBundle\Form;

use Belraysa\BackendBundle\Entity\Mercancia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConceptoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('fechaCreacion')
            // ->add('tipo')
            ->add('remitente')
            ->add('consignado')
            ->add('mercancias', 'collection', array(
                'label' => 'Mercancía',
                'type' => new MercanciaType(),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'required' => true,
                'by_reference' => false,
                'attr' => array(
                    'class' => 'collection',
                )));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Belraysa\BackendBundle\Entity\Concepto'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'belraysa_backendbundle_concepto';
    }
}
