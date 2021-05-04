<?php

namespace Belraysa\BackendBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CostoRecurrenteType extends AbstractType
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
            ->add('pagueA')
            ->add('detalles', 'textarea')
            ->add('suma')
            ->add('metodoPago', 'entity', array(
                'class' => 'BackendBundle:PaymentMethod',
                'empty_value' => 'Seleccione un elemento de la lista',
                'required' => true,
                'query_builder' => function (EntityRepository $repository) {
                    $qb = $repository->createQueryBuilder('metodoPago');
                    return $qb;
                }))
            ->add('inventarios')

            ->add('expediente')

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Belraysa\BackendBundle\Entity\CostoRecurrente'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'belraysa_backendbundle_costorecurrente';
    }
}
