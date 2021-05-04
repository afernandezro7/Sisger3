<?php

namespace Belraysa\BackendBundle;

class MetodoPago
{
    public $metodos;
    public $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getMetodos()
    {
        $em = $this->container->get('doctrine')->getManager();
        $this->metodos = $em->getRepository('BackendBundle:Recibo')->findAll();

        return $this->metodos;
    }

}
