<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 14:52
 */

namespace Belraysa\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TraspasoRepository extends EntityRepository
{

    public function findTraspasosOnDescendantOrder()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Traspaso r ORDER BY r.creationDate DESC');
        return $query->getResult();
    }

}