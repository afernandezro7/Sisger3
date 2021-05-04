<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 14:52
 */

namespace Belraysa\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ReceipeRepository extends EntityRepository
{

    public function findReceipesByRange($from, $to)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT v FROM BackendBundle:Receipe v WHERE v.creationDate BETWEEN :from AND :to');
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);
        return $query->getResult();
    }


    public function findReceipesByRangeAndWs($from, $to, $ws)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT v FROM BackendBundle:Receipe v WHERE v.creationDate BETWEEN :from AND :to AND v.workspace = :ws');
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);
        $query->setParameter('ws', $ws);
        return $query->getResult();
    }

    public function findTotalImporteReceipesByRangeAndWs($from, $to, $ws)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT SUM(v.importe) FROM BackendBundle:Receipe v WHERE v.activo = :activo AND v.creationDate BETWEEN :from AND :to AND :to AND v.workspace = :ws');
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);
        $query->setParameter('activo', true);
        $query->setParameter('ws', $ws);
        return $query->getResult();
    }

    public function findTotalImporteReceipesByRange($from, $to)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT SUM(v.importe) FROM BackendBundle:Receipe v WHERE v.activo = :activo AND v.creationDate BETWEEN :from AND :to');
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);
        $query->setParameter('activo', true);
        return $query->getResult();
    }

    public function findExpendituresByRange($from, $to)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT v FROM BackendBundle:Receipe v WHERE v.type = :type AND v.activo = :activo AND v.creationDate BETWEEN :from AND :to');
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);
        $query->setParameter('activo', true);
        $query->setParameter('type', 'Gasto');
        return $query->getResult();
    }


    public function findTotalImporteReceipesByReply($reply_id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT SUM(v.importe) FROM BackendBundle:Receipe v WHERE v.activo = :activo AND v.reply = :reply_id');
        $query->setParameter('reply_id', $reply_id);
        $query->setParameter('activo', true);
        return $query->getResult();
    }

    public function findReceipesOnDescendantOrder()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT v FROM BackendBundle:Receipe v ORDER BY v.creationDate DESC');
        return $query->getResult();
    }

    public function findBusquedaSimple($text, $ws)
    {
        $text = '%' . $text . '%';
        $text1 = $text . '%';
        $text2 = '%' . $text;

        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Receipe r WHERE r.workspace = :ws AND (r.recibide LIKE :query OR r.recibide LIKE :query1 OR r.recibide LIKE :query2 OR r.sisgerCode LIKE :query OR r.sisgerCode LIKE :query1 OR r.sisgerCode LIKE :query2)');
        $query->setParameter('query', $text);
        $query->setParameter('query1', $text1);
        $query->setParameter('query2', $text2);
        $query->setParameter('ws', $ws);
        return $query->getResult();
    }

    public function findReceipesByWorkspaceOnDescendantOrder($workspace)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Receipe r WHERE r.workspace = :workspace ORDER BY r.creationDate DESC');
        $query->setParameter('workspace', $workspace);
        return $query->getResult();
    }
} 