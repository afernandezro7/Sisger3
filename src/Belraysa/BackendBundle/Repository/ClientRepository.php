<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 14:52
 */

namespace Belraysa\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ClientRepository extends EntityRepository
{

    public function findClientByFullName()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c FROM BackendBundle:Client c ORDER BY c.createdAt DESC ');
        return $query->getResult();
    }

    public function findClientsByUserAndMonth($operator, $from, $to)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c FROM BackendBundle:Client c WHERE c.user = :operator AND c.createdAt BETWEEN :from AND :to');
        $query->setParameter('operator', $operator);
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);


        return $query->getResult();
    }


    public function findClientsWithNoUserOrderedDesc($user)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c FROM BackendBundle:Client c WHERE c.user != :user ORDER BY c.createdAt DESC');
        $query->setParameter('user', $user);

        return $query->getResult();
    }

    public function findByUserOrderedDesc()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c FROM BackendBundle:Client c ORDER BY c.createdAt DESC');


        return $query->getResult();
    }

    public function findBusquedaSimple($text)
    {
        $text = '%' . $text . '%';
        $text1 = $text . '%';
        $text2 = '%' . $text;


        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT r FROM BackendBundle:Client r WHERE (r.firstName LIKE :query OR r.firstName LIKE :query1 OR r.firstName LIKE :query2 OR r.lastName LIKE :query OR r.lastName LIKE :query1 OR r.lastName LIKE :query2 OR CONCAT(r.firstName, ' ', r.lastName) LIKE :query OR CONCAT(r.firstName, ' ', r.lastName) LIKE :query1 OR CONCAT(r.firstName, ' ', r.lastName) LIKE :query2) ORDER BY r.createdAt DESC");
        $query->setParameter('query', $text);
        $query->setParameter('query1', $text1);
        $query->setParameter('query2', $text2);
        return $query->getResult();
    }


}