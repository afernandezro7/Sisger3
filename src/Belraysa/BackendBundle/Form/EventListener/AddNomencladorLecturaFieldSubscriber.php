<?php

namespace Belraysa\BackendBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityRepository;

class AddNomencladorLecturaFieldSubscriber implements EventSubscriberInterface
{
    private $factory;

    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_BIND => 'preBind'
        );
    }

    private function addNomencladorLecturaForm($form, $workspace)
    {
        $form->add($this->factory->createNamed('nomencladoresLectura', 'entity', array(
            'class' => 'BackendBundle:Nomenclador',
            'mapped' => false,
            'query_builder' => function (EntityRepository $repository) use ($workspace) {
                $qb = $repository->createQueryBuilder('nomenclador.nomenclador');
                $qb->where('workspace_nomenclador.workspace = :workspace')
                    ->setParameter('workspace', $workspace);
                return $qb;
            }
        )));
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }

        $workspace = $data->getWorkspace();
        $this->addNomencladorLecturaForm($form, $workspace);
    }

    public function preBind(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }

        $workspace = array_key_exists('workspace', $data) ? $data['workspace'] : null;
        $this->addNomencladorLecturaForm($form, $workspace);
    }
}