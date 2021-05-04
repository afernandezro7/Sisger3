<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 14:52
 */

namespace Belraysa\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findBusquedaSimple($text)
    {
        $text = '%' . $text . '%';
        $text1 = $text . '%';
        $text2 = '%' . $text;

        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT u FROM BackendBundle:User u WHERE (u.firstName LIKE :query OR u.firstName LIKE :query1 OR u.firstName LIKE :query2 OR u.lastName LIKE :query OR u.lastName LIKE :query1 OR u.lastName LIKE :query2)');
        $query->setParameter('query', $text);
        $query->setParameter('query1', $text1);
        $query->setParameter('query2', $text2);
        return $query->getResult();
    }




} 