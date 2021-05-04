<?php

namespace Belraysa\BackendBundle;

class User
{
    public $users;
    public $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getUsers()
    {
        $em = $this->container->get('doctrine')->getManager();
        $this->users = $em->getRepository('BackendBundle:User')->findAll();
        return $this->users;
    }

    public function getAnnos($user)
    {
        $em = $this->container->get('doctrine')->getManager();
        $ordenadas = $em->getRepository('BackendBundle:Reply')->findBy(array('user' => $user), array('createdAt' => 'ASC'));
        $annos = array();
        if ($ordenadas) {
            $anno = $ordenadas[0]->getBeginDate()->format('Y');
            $now = new \DateTime();

            $annoActual = $now->format('Y');
            $annos = array();
            for ($i = $anno; $i <= $annoActual; $i++) {
                $annos[] = $i;
            }
        }
        return $annos;
    }

    public function filtrarImportePorUsuario($user, $from, $to)
    {
        $em = $this->container->get('doctrine')->getManager();
        $importe = $em->getRepository('BackendBundle:Recibo')->findTotalImporteRecibosByRangeAndTypeAndUser($from, $to, 'Ingreso', $user)[0][1];

        return round($importe, 2);
    }

    public function filtrarExpedientesPorUsuario($user, $from, $to)
    {
        $em = $this->container->get('doctrine')->getManager();
        $expedientes = $em->getRepository('BackendBundle:Reply')->findRepliesByUserAndMonth($user, $from, $to);

        return $expedientes;
    }

    public function filtrarClientesPorUsuario($user, $from, $to)
    {
        $expedientes = $this->filtrarExpedientesPorUsuario($user, $from, $to);
        $clientes = 0;
        foreach ($expedientes as $expediente) {
            $clientes = $clientes + sizeof($expediente->getPreciosVenta());
        }
        return $clientes;
    }


}
