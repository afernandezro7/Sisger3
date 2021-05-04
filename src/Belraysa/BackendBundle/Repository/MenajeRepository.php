<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 14:52
 */

namespace Belraysa\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class MenajeRepository extends EntityRepository
{

    public function findOrdenadas()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT e FROM BackendBundle:Menaje e ORDER BY e.fechaCreacion DESC');
        return $query->getResult();
    }


}