<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 14:52
 */

namespace Belraysa\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AjusteRepository extends EntityRepository
{

    public function findAjustesOnDescendantOrder()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Ajuste r ORDER BY r.fecha DESC');
        return $query->getResult();
    }
}