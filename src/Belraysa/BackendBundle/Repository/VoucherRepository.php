<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 14:52
 */

namespace Belraysa\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class VoucherRepository extends EntityRepository
{

    public function findVouchersByRange($from, $to)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT v FROM BackendBundle:Voucher v WHERE v.creationDate BETWEEN :from AND :to');
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);
        return $query->getResult();
    }

    public function findVouchersByRangeAndWs($from, $to, $ws)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT v  FROM BackendBundle:Voucher v  WHERE v.creationDate BETWEEN :from AND :to AND v.workspace = :ws');
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);
        $query->setParameter('ws', $ws);
        return $query->getResult();
    }

    public function findTotalPaxVouchersByRange($from, $to)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT SUM(v.pax) FROM BackendBundle:Voucher v WHERE v.creationDate BETWEEN :from AND :to');
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);
        return $query->getResult();
    }

    public function findTotalPaxVouchersByRangeAndWs($from, $to, $ws)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT SUM(v.pax) FROM BackendBundle:Voucher v WHERE v.creationDate BETWEEN :from AND :to AND v.workspace = :ws');
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);
        $query->setParameter('ws', $ws);
        return $query->getResult();
    }

    public function findVouchersOnDescendantOrder()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT v FROM BackendBundle:Voucher v ORDER BY v.creationDate DESC');
        return $query->getResult();
    }
    public function findBusquedaSimple($text, $ws)
    {
        $text = '%' . $text . '%';
        $text1 = $text . '%';
        $text2 = '%' . $text;

        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT v FROM BackendBundle:Voucher v JOIN v.client c  WHERE v.workspace = :ws AND (c.firstName LIKE :query OR c.firstName LIKE :query1 OR c.firstName LIKE :query2 OR c.lastName LIKE :query OR c.lastName LIKE :query1 OR c.lastName LIKE :query2 OR v.sisgerCode LIKE :query OR v.sisgerCode LIKE :query1 OR v.sisgerCode LIKE :query2 OR CONCAT(c.firstName, ' ', c.lastName) LIKE :query OR CONCAT(c.firstName, ' ', c.lastName) LIKE :query1 OR CONCAT(c.firstName, ' ', c.lastName) LIKE :query2)");
        $query->setParameter('query', $text);
        $query->setParameter('query1', $text1);
        $query->setParameter('query2', $text2);
        $query->setParameter('ws', $ws);
        return $query->getResult();
    }

    public function findVouchersByWorkspaceOnDescendantOrder($workspace)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Voucher r  WHERE r.workspace = :workspace ORDER BY r.creationDate DESC');
        $query->setParameter('workspace', $workspace);
        return $query->getResult();
    }

    public function findBusquedaAvanzada($ws, $aavv, $c507, $lbrs, $sisgerCode, $from, $to, $user, $proveedor, $reply, $estado)
    {
        $code = '%' . $sisgerCode . '%';
        $code1 = $sisgerCode . '%';
        $code2 = '%' . $sisgerCode;

        $proveedor0 = '%' . $proveedor . '%';
        $proveedor1 = $proveedor . '%';
        $proveedor2 = '%' . $proveedor;

        $select = 'SELECT r FROM BackendBundle:Voucher r';
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

        if ($proveedor) {
            $where[] = '(r.provider = :proveedor0 OR r.provider LIKE :proveedor1 OR r.provider LIKE :proveedor2)';
        }

        if ($user) {
            $where[] = 'r.user = :user';
        }

        if ($reply) {
            $where[] = 'r.reply = :reply';
        }

        $activo = true;
        if ($estado == 2) {
            $where[] = 'r.activo = :activo';
        } elseif ($estado == 3) {
            $activo = false;
            $where[] = 'r.activo = :activo';
        }


        //$to->setTime(23, 59, 59);

        if ($from && $to) {
            $where[] = ' r.beginDate BETWEEN :from AND :to';
            $where[] = ' r.beginDate BETWEEN :from AND :to';
        } elseif ($from && !$to) {
            $where[] = ' r.beginDate >= :from';
        } elseif (!$from && $to) {
            $where[] = ' r.beginDate <= :to';
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
        // print_r($select);
        // die;
        $em = $this->getEntityManager();
        $query = $em->createQuery($select);

        if ($sisgerCode) {
            $query->setParameter('code', $code);
            $query->setParameter('code1', $code1);
            $query->setParameter('code2', $code2);
        }
        if ($proveedor) {
            $query->setParameter('proveedor0', $proveedor0);
            $query->setParameter('proveedor1', $proveedor1);
            $query->setParameter('proveedor2', $proveedor2);
        }

        if ($user) {
            $query->setParameter('user', $user);
        }
        if ($estado == 2 or $estado == 3) {
            $query->setParameter('activo', $activo);
        }
        if ($reply) {
            $query->setParameter('reply', $reply);
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