<?php

namespace Belraysa\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReplyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('beginDate', 'date', [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'form-control input-inline datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-mm-yyyy',
                    'required' => true
                ]
            ])
            ->add('finishDate', 'date', [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'form-control input-inline datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-mm-yyyy',
                    'required' => true
                ]
            ])
            ->add('precioVentaLider', 'collection', array(
                'label' => '',
                'type' => new PrecioVentaLiderType(),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'required' => true,
                'by_reference' => false,
                'attr' => array(
                    'class' => 'precioVentaLider',
                )
            ))
            ->add('preciosVenta', 'collection', array(
                'label' => '',
                'type' => new PrecioVentaType(),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'required' => true,
                'by_reference' => false,
                'attr' => array(
                    'class' => 'preciosVenta',
                )
            ))
            //->add('pax', 'text')
            ->add('observations', 'textarea', array('required' => false))
            ->add('services', null, array('required' => true));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Belraysa\BackendBundle\Entity\Reply'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'belraysa_backendbundle_reply';
    }
}
