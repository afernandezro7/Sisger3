<?php

namespace Belraysa\BackendBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EnvioEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('remitenteNombre')
            ->add('remitenteCedula')
            ->add('contenedor', 'entity', array(
                'class' => 'BackendBundle:Contenedor',
                'empty_value' => 'Seleccione un elemento de la lista',
                'required' => true,
                'query_builder' => function (EntityRepository $repository) {
                    $qb = $repository->createQueryBuilder('contenedor')
                        ->where("(contenedor.estado = 'COMPLETANDO' or contenedor.estado = 'RESERVANDO')");
                    return $qb;
                }))
            ->add('consignado');

    }
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        $resolver->setDefaults(array(
            'data_class' => 'Belraysa\BackendBundle\Entity\Envio'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'belraysa_backendbundle_envio_edit';
    }
}
