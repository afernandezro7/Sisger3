<?php

namespace Belraysa\BackendBundle;

class Mercancia
{
    public $mercancias;
    public $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getMercancias()
    {
        $em = $this->container->get('doctrine')->getManager();
        $this->mercancias = $em->getRepository('BackendBundle:Mercancia')->findAll();
        return $this->mercancias;
    }

    public function getMercanciasPorConcepto($concepto)
    {
        $em = $this->container->get('doctrine')->getManager();
        $relacionables = $em->getRepository('BackendBundle:Mercancia')->findMercanciasPorConcepto($concepto);
        return $relacionables;
    }

    public function getRelacionables($mercancia, $concepto)
    {
        $em = $this->container->get('doctrine')->getManager();
        $relacionables = $em->getRepository('BackendBundle:Mercancia')->findRelacionables($mercancia, $concepto);
        return $relacionables;
    }

}
