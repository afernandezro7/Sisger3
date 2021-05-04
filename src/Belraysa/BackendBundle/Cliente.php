<?php

namespace Belraysa\BackendBundle;

class Cliente
{
    public $clientes;
    public $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getClientes()
    {
        $em = $this->container->get('doctrine')->getManager();
        $this->clientes = $em->getRepository('BackendBundle:Client')->findAll();
        return $this->clientes;
    }
}
