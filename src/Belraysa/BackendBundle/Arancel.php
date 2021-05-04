<?php

namespace Belraysa\BackendBundle;

class Arancel
{
    public $aranceles;
    public $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getAranceles()
    {
        $em = $this->container->get('doctrine')->getManager();
        $this->aranceles = $em->getRepository('BackendBundle:Arancel')->findAll();
        return $this->aranceles;
    }

    public function getCapitulos()
    {
        $em = $this->container->get('doctrine')->getManager();
        $capitulos = $em->getRepository('BackendBundle:Arancel')->getCapitulos();
        return $capitulos;
    }

}
