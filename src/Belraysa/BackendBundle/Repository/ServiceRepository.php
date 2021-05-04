<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 14:52
 */

namespace Belraysa\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ServiceRepository extends EntityRepository
{

    public function findBusquedaSimple($text, $ws)
    {
        $text = '%' . $text . '%';
        $text1 = $text . '%';
        $text2 = '%' . $text;

        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c FROM BackendBundle:Service c WHERE c.workspace = :ws AND (c.name LIKE :query OR c.name LIKE :query1 OR c.name LIKE :query2) ORDER BY c.name ASC');
        $query->setParameter('query', $text);
        $query->setParameter('query1', $text1);
        $query->setParameter('query2', $text2);
        $query->setParameter('ws', $ws);
        return $query->getResult();
    }



} 