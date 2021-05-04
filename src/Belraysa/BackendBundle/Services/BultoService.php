<?php
/**
 * Created by PhpStorm.
 * User: Mary
 * Date: 02/09/2018
 * Time: 17:57
 */

namespace Belraysa\BackendBundle\Services;


use Belraysa\BackendBundle\Entity\Bulto;

class BultoService
{
    public function createBultoHelper($id, $em){
        $now = new \DateTime();
        
        $bulto = new Bulto();
        $concepto = $em->getRepository('BackendBundle:Concepto')->find($id);
        $indice = sizeof($concepto->getBultos()) + 1;
        $bulto->setIndice($indice);
        $concepto->addBulto($bulto);
        $concepto->setSisgerCode("BRS" . date_format($now, 'y'). str_pad($indice, 3, 0, STR_PAD_LEFT) . str_pad($concepto->getId(), 6, 0, STR_PAD_LEFT));

        $em->persist($bulto);
        $em->flush();
        return $bulto;
    }
}