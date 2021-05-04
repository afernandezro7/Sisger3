<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 14:52
 */

namespace Belraysa\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ENARepository extends EntityRepository
{

    public function findOrdenadas()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT e FROM BackendBundle:ENA e ORDER BY e.fechaCreacion DESC');
        return $query->getResult();
    }


}