<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 14:52
 */

namespace Belraysa\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class WorkspaceRepository extends EntityRepository
{
    public function findBusquedaSimple($text_original)
    {
        $text = '%' . $text_original . '%';
        $text1 = $text_original . '%';
        $text2 = '%' . $text_original;

        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Workspace r WHERE r.name = :query OR r.name LIKE :query1 OR r.name LIKE :query2');
        $query->setParameter('query', $text);
        $query->setParameter('query1', $text1);
        $query->setParameter('query2', $text2);
        return $query->getResult();
    }
} 