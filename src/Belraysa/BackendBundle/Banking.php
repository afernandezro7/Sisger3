<?php

namespace Belraysa\BackendBundle;

class Banking
{
    public $bankings;
    public $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getBankings()
    {
        $em = $this->container->get('doctrine')->getManager();
        $this->bankings = $em->getRepository('BackendBundle:Banking')->findAll();
        return $this->bankings;
    }

}
