<?php

namespace Belraysa\BackendBundle;

class Anno
{
    public $annos;
    public $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getAnnos()
    {
        $em = $this->container->get('doctrine')->getManager();
        $this->annos = $em->getRepository('BackendBundle:Anno')->findAll();
        return $this->annos;
    }

}
