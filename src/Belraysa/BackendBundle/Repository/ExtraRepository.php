<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 14:52
 */

namespace Belraysa\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ExtraRepository extends EntityRepository
{


    public function findBusquedaAvanzada($ws, $aavv, $c507, $lbrs, $sisgerCode, $from, $to, $user, $tipo, $importe_desde, $importe_hasta, $cuenta)
    {
        $code = '%' . $sisgerCode . '%';
        $code1 = $sisgerCode . '%';
        $code2 = '%' . $sisgerCode;


        $select = 'SELECT r FROM BackendBundle:Extra r';
        $where = array();


        if ($sisgerCode) {
            $where[] = '(r.sisgerCode = :code OR r.sisgerCode LIKE :code1 OR r.sisgerCode LIKE :code2)';
        }
        if ($user) {
            $where[] = 'r.usuario = :user';
        }


        if ($cuenta) {
            $select = 'SELECT r, e FROM BackendBundle:Extra r JOIN r.entrada e';
            $where[] = 'e.banking = :cuenta';
        }



        if ($from && $to) {
            $to->setTime(23, 59, 59);
            $where[] = ' r.fecha BETWEEN :from AND :to';
            $where[] = ' r.fecha BETWEEN :from AND :to';
        } elseif ($from && !$to) {
            $where[] = ' r.fecha >= :from';
        } elseif (!$from && $to) {
            $to->setTime(23, 59, 59);
            $where[] = ' r.fecha <= :to';
        }

        if ($importe_desde && $importe_hasta) {
            $where[] = ' r.importe BETWEEN :importe_desde AND :importe_hasta';
        } elseif ($importe_desde && !$importe_hasta) {
            $where[] = ' r.importe > :importe_desde';
        } elseif (!$importe_desde && $importe_hasta) {
            $where[] = ' r.importe < :importe_hasta';
        }

        if ($tipo) {
            if ($tipo == 'Ingreso') {
                $whereTipo = 'r.importe > 0';

                $where[] = $whereTipo;

            } else {
                $whereTipo = 'r.importe < 0';

                $where[] = $whereTipo;
            }

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

        if ($user) {
            $query->setParameter('user', $user);
        }

        if ($cuenta) {
            $query->setParameter('cuenta', $cuenta);
        }
        if ($from && $to) {
            $query->setParameter('from', $from);
            $query->setParameter('to', $to);
        } elseif ($from && !$to) {
            $query->setParameter('from', $from);
        } elseif (!$from && $to) {
            $query->setParameter('to', $to);
        }
        if ($importe_desde && $importe_hasta) {
            $query->setParameter('importe_desde', $importe_desde);
            $query->setParameter('importe_hasta', $importe_hasta);
        } elseif ($importe_desde && !$importe_hasta) {
            $query->setParameter('importe_desde', $importe_desde);
        } elseif (!$importe_desde && $importe_hasta) {
            $query->setParameter('importe_hasta', $importe_hasta);
        }

        return $query->getResult();
    }

    public function findImporteBusquedaAvanzada($ws, $aavv, $c507, $lbrs, $sisgerCode, $from, $to, $user, $tipo, $importe_desde, $importe_hasta, $cuenta)
    {
        $code = '%' . $sisgerCode . '%';
        $code1 = $sisgerCode . '%';
        $code2 = '%' . $sisgerCode;


        $select = 'SELECT SUM(r.importe) FROM BackendBundle:Extra r';
        $where = array();

        if ($sisgerCode) {
            $where[] = '(r.sisgerCode = :code OR r.sisgerCode LIKE :code1 OR r.sisgerCode LIKE :code2)';
        }
        if ($user) {
            $where[] = 'r.usuario = :user';
        }


        if ($cuenta) {
            $select = 'SELECT SUM(r.importe) FROM BackendBundle:Extra r JOIN r.entrada e';
            $where[] = 'e.banking = :cuenta';
        }

        if ($from && $to) {
            $to->setTime(23, 59, 59);
            $where[] = ' r.fecha BETWEEN :from AND :to';
        } elseif ($from && !$to) {
            $where[] = ' r.fecha > :from';
        } elseif (!$from && $to) {
            $to->setTime(23, 59, 59);
            $where[] = ' r.fecha < :to';
        }

        if ($importe_desde && $importe_hasta) {
            $where[] = ' r.importe BETWEEN :importe_desde AND :importe_hasta';
        } elseif ($importe_desde && !$importe_hasta) {
            $where[] = ' r.importe > :importe_desde';
        } elseif (!$importe_desde && $importe_hasta) {
            $where[] = ' r.importe < :importe_hasta';
        }

        if ($tipo) {
            if ($tipo == 'Ingreso') {
                $whereTipo = 'r.importe > 0';

                $where[] = $whereTipo;

            } else {
                $whereTipo = 'r.importe < 0';

                $where[] = $whereTipo;
            }

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

        if ($user) {
            $query->setParameter('user', $user);
        }

        if ($cuenta) {
            $query->setParameter('cuenta', $cuenta);
        }
        if ($from && $to) {
            $query->setParameter('from', $from);
            $query->setParameter('to', $to);
        } elseif ($from && !$to) {
            $query->setParameter('from', $from);
        } elseif (!$from && $to) {
            $query->setParameter('to', $to);
        }
        if ($importe_desde && $importe_hasta) {
            $query->setParameter('importe_desde', $importe_desde);
            $query->setParameter('importe_hasta', $importe_hasta);
        } elseif ($importe_desde && !$importe_hasta) {
            $query->setParameter('importe_desde', $importe_desde);
        } elseif (!$importe_desde && $importe_hasta) {
            $query->setParameter('importe_hasta', $importe_hasta);
        }

        return $query->getResult();
    }

    public function findExtrasOnDescendantOrder()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Extra r ORDER BY r.fecha DESC');
        return $query->getResult();
    }

    public function findPorFechas($from, $to)
    {

        $select = 'SELECT r FROM BackendBundle:Extra r';
        $where = array();

        if ($from && $to) {
            //$to->setTime(23, 59, 59);
            $where[] = ' r.fecha BETWEEN :from AND :to';
            $where[] = ' r.fecha BETWEEN :from AND :to';
        } elseif ($from && !$to) {
            $where[] = ' r.fecha >= :from';
        } elseif (!$from && $to) {
            //$to->setTime(23, 59, 59);
            $where[] = ' r.fecha <= :to';
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


        if ($from && $to) {
            $query->setParameter('from', $from);
            $query->setParameter('to', $to);
        } elseif ($from && !$to) {
            $query->setParameter('from', $from);
        } elseif (!$from && $to) {
            $query->setParameter('to', $to);
        }

        return $query->getResult();
    }

    public function findImportePorFechas($from, $to)
    {

        $select = 'SELECT SUM(r.importe) FROM BackendBundle:Extra r';
        $where = array();


        if ($from && $to) {
            //$to->setTime(23, 59, 59);
            $where[] = ' r.fecha BETWEEN :from AND :to';
        } elseif ($from && !$to) {
            $where[] = ' r.fecha > :from';
        } elseif (!$from && $to) {
            //$to->setTime(23, 59, 59);
            $where[] = ' r.fecha < :to';
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


        if ($from && $to) {
            $query->setParameter('from', $from);
            $query->setParameter('to', $to);
        } elseif ($from && !$to) {
            $query->setParameter('from', $from);
        } elseif (!$from && $to) {
            $query->setParameter('to', $to);
        }
        return $query->getResult();
    }

}