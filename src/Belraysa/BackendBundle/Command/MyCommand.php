<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 14/10/2015
 * Time: 15:54
 */

namespace Belraysa\BackendBundle\Command;

use Belraysa\BackendBundle\Entity\Client;
use Belraysa\BackendBundle\Entity\Ingreso;
use Belraysa\BackendBundle\Entity\PrecioVenta;
use Belraysa\BackendBundle\Workspace;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class MyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('BackendBundle:my_command');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $anno = date_format(new \DateTime(), 'Y');
        $mes = date_format(new \DateTime(), 'm');
        $contenedores = $em->getRepository('BackendBundle:Contenedor')->findContenedoresParaCierreAutomatico($anno, $mes);
        if ($contenedores) {
            foreach ($contenedores as $contenedor) {
                if ($contenedor->getEstado() == 'COMPLETANDO') {
                    $nominado = $em->getRepository('BackendBundle:Contenedor')->findBy(array('estado' => 'RESERVANDO'), array('indice' => 'ASC'))[0];
                    if($nominado) {
                        $nominado->setEstado('COMPLETANDO');
                    }
                }
                $contenedor->setEstado('CERRADO');
            }
        }

        $output->writeln('OK revision de contenedores a cerrar automaticamente.');

    }
}