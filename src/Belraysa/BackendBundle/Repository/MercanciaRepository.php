<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 14:52
 */

namespace Belraysa\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class MercanciaRepository extends EntityRepository
{
    public function findMercanciasPorConcepto($concepto)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT m FROM BackendBundle:Mercancia m JOIN m.bulto b WHERE b.concepto = :concepto');
        $query->setParameter('concepto', $concepto);
        return $query->getResult();
    }

    public function findRelacionables($mercancia, $concepto)
    {
        $em = $this->getEntityManager();
        $consulta = 'SELECT m FROM BackendBundle:Mercancia m JOIN m.bulto b WHERE b.concepto = :concepto';
        if ($mercancia) {
            $consulta = $consulta . ' AND m.id != :id';
        }
        $query = $em->createQuery($consulta);
        $query->setParameter('concepto', $concepto);
        if ($mercancia) {
            $query->setParameter('id', $mercancia->getId());
        }
        return $query->getResult();
    }
} 