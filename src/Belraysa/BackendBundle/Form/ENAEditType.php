<?php

namespace Belraysa\BackendBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ENAEditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('remitente', 'entity', array(
                'class' => 'BackendBundle:Client',
                'empty_value' => 'Seleccione un elemento de la lista',
                'required' => true,
                'query_builder' => function (EntityRepository $repository) {
                    $qb = $repository->createQueryBuilder('client')
                        ->orderBy("client.createdAt", 'DESC');
                    return $qb;
                }))
            ->add('contenedor', 'entity', array(
                'class' => 'BackendBundle:Contenedor',
                'empty_value' => 'Seleccione un elemento de la lista',
                'required' => true,
                'query_builder' => function (EntityRepository $repository) {
                    $qb = $repository->createQueryBuilder('contenedor')
                        ->where("(contenedor.estado = 'COMPLETANDO' or contenedor.estado = 'RESERVANDO')");
                    return $qb;
                }))
            /*->add('estado', 'choice', array(
                'required' => true,
                'choices' => array('RVA' => 'RVA', 'HBL' => 'HBL')
            ))*/
            ->add('fechaEntrada', 'date', [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'form-control input-inline datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-mm-yyyy'
                ],
                'required' => true
            ])
            ->add('fechaSalida', 'date', [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'form-control input-inline datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-mm-yyyy'
                ],
                'required' => true
            ]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Belraysa\BackendBundle\Entity\ENA'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'belraysa_backendbundle_ena_edit';
    }
}
