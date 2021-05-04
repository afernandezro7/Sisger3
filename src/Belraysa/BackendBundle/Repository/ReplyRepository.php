<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 14:52
 */

namespace Belraysa\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ReplyRepository extends EntityRepository
{


    public function findRepliesByUserAndMonth($operator, $from, $to)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Reply r WHERE r.user = :operator AND r.createdAt BETWEEN :from AND :to');
        $query->setParameter('operator', $operator);
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);

        return $query->getResult();
    }

    public function findRepliesWithNoUser($user)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Reply r WHERE r.user != :user ORDER BY r.createdAt DESC');
        $query->setParameter('user', $user);

        return $query->getResult();
    }

    public function findRepliesWithNoUserWS($user, $ws)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Reply r WHERE r.workspace = :ws AND r.user != :user ORDER BY r.createdAt DESC');
        $query->setParameter('user', $user);
        $query->setParameter('ws', $ws);
        return $query->getResult();
    }

    public function findRepliesOrderAsc()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Reply r ORDER BY r.beginDate ASC');


        return $query->getResult();
    }

    public function findRepliesByWorkspaceOrderAsc($ws)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Reply r WHERE r.workspace = :ws ORDER BY r.beginDate ASC');
        $query->setParameter('ws', $ws);

        return $query->getResult();
    }

    public function findRepliesByMonthOfYear($month, $year)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Reply r WHERE r.year = :year AND r.month = :month ORDER BY r.beginDate ASC');
        $query->setParameter('month', $month);
        $query->setParameter('year', $year);
        return $query->getResult();
    }

    public function findRepliesByMonthOfYearAndWS($month, $year, $ws)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Reply r WHERE r.year = :year AND r.month = :month AND r.workspace = :ws ORDER BY r.beginDate ASC');
        $query->setParameter('month', $month);
        $query->setParameter('year', $year);
        $query->setParameter('ws', $ws);
        return $query->getResult();
    }


    public function findRepliesByYear($year)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Reply r WHERE r.year = :year ORDER BY r.beginDate ASC');
        $query->setParameter('year', $year);
        return $query->getResult();
    }

    public function findRepliesByYearAndWS($year, $ws)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Reply r WHERE r.year = :year AND r.workspace = :ws ORDER BY r.beginDate ASC');
        $query->setParameter('year', $year);
        $query->setParameter('ws', $ws);
        return $query->getResult();
    }

    public function findRepliesByMonthOfYearWithUser($month, $year, $user)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Reply r WHERE r.year = :year AND r.month = :month AND r.user = :user ORDER BY r.beginDate ASC');
        $query->setParameter('month', $month);
        $query->setParameter('year', $year);
        $query->setParameter('user', $user);
        return $query->getResult();
    }

    public function findRepliesByMonthOfYearWithUserAndWS($month, $year, $user, $ws)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Reply r WHERE r.year = :year AND r.month = :month AND r.user = :user AND r.workspace = :ws ORDER BY r.beginDate ASC');
        $query->setParameter('month', $month);
        $query->setParameter('year', $year);
        $query->setParameter('user', $user);
        $query->setParameter('ws', $ws);
        return $query->getResult();
    }


    public function findRepliesByYearWithoutUser($year, $user)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Reply r WHERE r.year = :year AND r.user = :user ORDER BY r.beginDate ASC');
        $query->setParameter('year', $year);
        $query->setParameter('user', $user);
        return $query->getResult();
    }

    public function findRepliesByYearWithoutUserAndWS($year, $user, $ws)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Reply r WHERE r.year = :year AND r.user = :user AND r.workspace = :ws ORDER BY r.beginDate ASC');
        $query->setParameter('year', $year);
        $query->setParameter('user', $user);
        $query->setParameter('ws', $ws);
        return $query->getResult();
    }


    public function findRepliesByRange($from, $to)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT v FROM BackendBundle:Reply v WHERE v.createdAt BETWEEN :from AND :to');
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);
        return $query->getResult();
    }

    public function findRepliesByRangeAndWS($from, $to, $ws)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT v FROM BackendBundle:Reply v WHERE v.createdAt BETWEEN :from AND :to AND v.workspace = :ws');
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);
        $query->setParameter('ws', $ws);
        return $query->getResult();
    }

    public function findBusquedaSimple($text_original, $ws)
    {
        $text = '%' . $text_original . '%';
        $text1 = $text_original . '%';
        $text2 = '%' . $text_original;

        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT r FROM BackendBundle:Reply r JOIN r.precioVentaLider p JOIN p.cliente l  WHERE r.workspace = :ws AND (r.sisgerCode = :query OR r.sisgerCode LIKE :query1 OR r.sisgerCode LIKE :query2 OR l.firstName LIKE :query or l.firstName LIKE :query1 OR l.firstName LIKE :query2 or l.lastName LIKE :query or l.lastName LIKE :query1 OR l.lastName LIKE :query2 OR CONCAT(l.firstName, ' ', l.lastName) LIKE :query OR CONCAT(l.firstName, ' ', l.lastName) LIKE :query1 OR CONCAT(l.firstName, ' ', l.lastName) LIKE :query2)");
        $query->setParameter('query', $text);
        $query->setParameter('query1', $text1);
        $query->setParameter('query2', $text2);
        $query->setParameter('ws', $ws);
        return $query->getResult();
    }

    public function findRepliesOnDescendantOrder()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Reply r ORDER BY r.createdAt DESC');
        return $query->getResult();
    }

    public function findRepliesByUser($operator)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Reply r WHERE r.user = :operator');
        $query->setParameter('operator', $operator);


        return $query->getResult();
    }

    public function findRepliesByUserWS($operator, $ws)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Reply r WHERE r.workspace = :ws AND r.user = :operator');
        $query->setParameter('operator', $operator);
        $query->setParameter('ws', $ws);

        return $query->getResult();
    }

    public function findBusquedaAvanzada($ws, $aavv, $c507, $lbrs, $sisgerCode, $from, $to, $lider, $user)
    {
        $code = '%' . $sisgerCode . '%';
        $code1 = $sisgerCode . '%';
        $code2 = '%' . $sisgerCode;

        $select = 'SELECT r FROM BackendBundle:Reply r';
        $where = array();
        $workspaces = array();
        if ($ws) {

            if ($aavv) {
                $workspaces[] = 'r.workspace = :aavv';
            }
            if ($c507) {
                $workspaces[] = 'r.workspace = :c507';
            }
            if ($lbrs) {
                $workspaces[] = 'r.workspace = :lbrs';
            }
        }

        if (sizeof($workspaces) > 0) {
            $whereWS = '';
            for ($i = 0; $i < sizeof($workspaces); $i++) {
                if ($i > 0) {
                    $whereWS = $whereWS . ' OR ';
                }
                $whereWS = $whereWS . $workspaces[$i];
            }
            $where[] = $whereWS;
        }

        if ($sisgerCode) {
            $where[] = '(r.sisgerCode = :code OR r.sisgerCode LIKE :code1 OR r.sisgerCode LIKE :code2)';
        }
        if ($lider) {
            $select = 'SELECT r, l FROM BackendBundle:Reply r JOIN r.precioVentaLider l';
            $where[] = 'l.cliente = :lider';
        }
        if ($user) {
            $where[] = 'r.user = :user';
        }


        if ($from && $to) {
            $where[] = ' r.beginDate BETWEEN :from AND :to';
            $where[] = ' r.finishDate BETWEEN :from AND :to';
        } elseif ($from && !$to) {
            $where[] = ' r.beginDate >= :from';
            $where[] = ' r.finishDate >= :from';
        } elseif (!$from && $to) {
            $where[] = ' r.beginDate <= :to';
            $where[] = ' r.finishDate <= :to';
        }

        if (sizeof($where) > 0) {
            $select = $select . ' WHERE ';
            for ($i = 0; $i < sizeof($where); $i++) {
                if ($i > 0) {
                    $select = $select . ' AND ';
                }
                $select = $select . $where[$i];
            }
        }
        //print_r($select);
        //die;
        $em = $this->getEntityManager();
        $query = $em->createQuery($select);

        if ($sisgerCode) {
            $query->setParameter('code', $code);
            $query->setParameter('code1', $code1);
            $query->setParameter('code2', $code2);
        }
        if ($lider) {
            $query->setParameter('lider', $lider);
        }
        if ($user) {
            $query->setParameter('user', $user);
        }
        if ($from && $to) {
            $query->setParameter('from', $from);
            $query->setParameter('to', $to);
        } elseif ($from && !$to) {
            $query->setParameter('from', $from);
        } elseif (!$from && $to) {
            $query->setParameter('to', $to);
        }
        if ($ws) {
            if ($aavv) {
                $query->setParameter('aavv', $aavv);
            }
            if ($c507) {
                $query->setParameter('c507', $c507);
            }
            if ($lbrs) {
                $query->setParameter('lbrs', $lbrs);
            }
        }
        return $query->getResult();
    }

} 