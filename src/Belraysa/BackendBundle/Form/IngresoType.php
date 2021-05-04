<?php

namespace Belraysa\BackendBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IngresoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha', 'date', [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'form-control input-inline datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-mm-yyyy',
                    'required' => true
                ]
            ])
            ->add('importe', 'number', array(
                "invalid_message" => "Este campo requiere un número"
            ))
            ->add('cliente', null, array('required' => true))
            ->add('detalles', 'textarea', array('required' => false))
            ->add('suma', null, array('required' => true))
            ->add('metodoPago', 'entity', array(
                'class' => 'BackendBundle:PaymentMethod',
                'empty_value' => 'Seleccione un elemento de la lista',
                'required' => true,
                'query_builder' => function (EntityRepository $repository) {
                    $qb = $repository->createQueryBuilder('metodoPago');
                    return $qb;
                }))
            ->add('servicios', null, array('required' => true))
            ->add('refExpediente')
            ->add('saldoAnterior', null, array('required' => true))
            ->add('fechaLimite', 'date', [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'data' => new \DateTime(),
                'required' => false,
                'attr' => [
                    'class' => 'form-control input-inline datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-mm-yyyy',
                    'required' => false
                ]
            ]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Belraysa\BackendBundle\Entity\Ingreso'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'belraysa_backendbundle_ingreso';
    }
}
