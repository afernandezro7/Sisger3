<?php

namespace Belraysa\BackendBundle;

class TipoContenedor
{
    public $tipos;
    public $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getTipos()
    {
        $em = $this->container->get('doctrine')->getManager();
        $this->tipos = $em->getRepository('BackendBundle:TipoContenedor')->findAll();
        return $this->tipos;
    }
}
