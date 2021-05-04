<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 14:52
 */

namespace Belraysa\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BankingEntryRepository extends EntityRepository
{

    public function findEntradasPordates($from, $to, $banking)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT e FROM BackendBundle:BankingEntry e WHERE e.date BETWEEN :from AND :to AND e.banking = :banking ORDER BY e.date DESC');
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);
        $query->setParameter('banking', $banking);
        return $query->getResult();
    }

    public function findDateAndBalanceByRange($from, $to)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT v.date, v.credit, v.debit FROM BackendBundle:BankingEntry v WHERE v.date BETWEEN :from AND :to ORDER BY v.date ASC');
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);
        return $query->getResult();
    }

    public function findBalancesIniciales()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT SUM(v.initBalance) FROM BackendBundle:Banking v');
        return $query->getResult();
    }

    public function findCreditosByRange($from, $to)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT SUM(v.credit) FROM BackendBundle:BankingEntry v WHERE v.date BETWEEN :from AND :to');
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);
        return $query->getResult();
    }

    public function findDebitosByRange($from, $to)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT SUM(v.debit) FROM BackendBundle:BankingEntry v WHERE v.date BETWEEN :from AND :to');
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);
        return $query->getResult();
    }

} 