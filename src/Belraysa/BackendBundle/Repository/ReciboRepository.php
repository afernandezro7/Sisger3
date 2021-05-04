<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 14:52
 */

namespace Belraysa\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ReciboRepository extends EntityRepository
{

    public function findRecibosByRange($from, $to)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT v FROM BackendBundle:Recibo v WHERE v.fecha BETWEEN :from AND :to ORDER BY v.id DESC');
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);
        return $query->getResult();
    }


    public function findRecibosByRangeAndWs($from, $to, $ws)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT v FROM BackendBundle:Recibo v WHERE v.fecha BETWEEN :from AND :to AND v.workspace = :ws ORDER BY v.id DESC');
        $query->setParameter('from', $from);
        $query->setParameter('to',    $to);
        $query->setParameter('ws', $ws);
        return $query->getResult();
    }

    public function findTotalImporteRecibosByRangeAndWs($from, $to, $ws)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT SUM(v.importe) FROM BackendBundle:Recibo v WHERE v.activo = :activo AND v.fecha BETWEEN :from AND :to AND v.workspace = :ws');
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);
        $query->setParameter('activo', true);
        $query->setParameter('ws', $ws);
        return $query->getResult();
    }

    public function findTotalImporteRecibosByRangeAndType($from, $to, $type, $ws)
    {
        $consulta = 'SELECT SUM(v.importe) FROM BackendBundle:Recibo v WHERE v.activo = :activo AND v.fecha BETWEEN :from AND :to AND v.tipo = :tipo';
        if ($ws) {
            $consulta = $consulta . ' AND v.workspace = :ws';
        }

        $em = $this->getEntityManager();
        $query = $em->createQuery($consulta);
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);
        $query->setParameter('activo', true);
        $query->setParameter('tipo', $type);
        if ($ws) {
            $query->setParameter('ws', $ws);
        }
        return $query->getResult();
    }

    public function findRepliesByUserAndMonth($from, $to, $type, $user)
    {
        $consulta = 'SELECT SUM(v.importe) FROM BackendBundle:Recibo v WHERE v.activo = :activo AND v.usuario = :usuario AND v.fecha BETWEEN :from AND :to AND v.tipo = :tipo';

        $em = $this->getEntityManager();
        $query = $em->createQuery($consulta);
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);
        $query->setParameter('activo', true);
        $query->setParameter('tipo', $type);
        $query->setParameter('usuario', $user);
        return $query->getResult();
    }


    public function findTotalImporteRecibosByRange($from, $to)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT SUM(v.importe) FROM BackendBundle:Recibo v WHERE v.activo = :activo AND v.fecha BETWEEN :from AND :to');
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);
        $query->setParameter('activo', true);
        return $query->getResult();
    }

    public function findDateAndTotalImporteRecibosByRange($from, $to, $ws)
    {
        $consulta = 'SELECT v.fecha, v.importe FROM BackendBundle:Recibo v WHERE v.activo = :activo AND v.fecha BETWEEN :from AND :to';
        if ($ws) {
            $consulta = $consulta . ' AND v.workspace = :ws';
        }
        $consulta = $consulta . ' ORDER BY v.fecha ASC';
        $em = $this->getEntityManager();
        $query = $em->createQuery($consulta);
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);
        $query->setParameter('activo', true);
        if ($ws) {
            $query->setParameter('ws', $ws);
        }
        return $query->getResult();
    }

    public function findExpendituresByRange($from, $to)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT v FROM BackendBundle:Recibo v WHERE v.type = :type AND v.activo = :activo AND v.fecha BETWEEN :from AND :to');
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);
        $query->setParameter('activo', true);
        $query->setParameter('type', 'Gasto');
        return $query->getResult();
    }


    public function findTotalImporteRecibosByReply($reply_id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT SUM(v.importe) FROM BackendBundle:Recibo v WHERE v.activo = :activo AND v.reply = :reply_id');
        $query->setParameter('reply_id', $reply_id);
        $query->setParameter('activo', true);
        return $query->getResult();
    }

    public function findRecibosOnDescendantOrder()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT v FROM BackendBundle:Recibo v ORDER BY v.id DESC');
        return $query->getResult();
    }

    public function findBusquedaSimple($text, $ws)
    {
        $text = '%' . $text . '%';
        $text1 = $text . '%';
        $text2 = '%' . $text;

        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Recibo r WHERE r.workspace = :ws AND (r.sisgerCode LIKE :query OR r.sisgerCode LIKE :query1 OR r.sisgerCode LIKE :query2) OR r.id IN (SELECT i.id FROM BackendBundle:Ingreso i WHERE i.recibiDe = :query OR i.recibiDe LIKE :query1 OR i.recibiDe LIKE :query2) OR r.id IN (SELECT g.id FROM BackendBundle:Gasto g WHERE g.pagueA = :query OR g.pagueA LIKE :query1 OR g.pagueA LIKE :query2) OR r.id IN (SELECT x.id FROM BackendBundle:Costo x WHERE x.pagueA = :query OR x.pagueA LIKE :query1 OR x.pagueA LIKE :query2) OR r.id IN (SELECT y.id FROM BackendBundle:CostoRecurrente y WHERE y.pagueA = :query OR y.pagueA LIKE :query1 OR y.pagueA LIKE :query2)');
        $query->setParameter('query', $text);
        $query->setParameter('query1', $text1);
        $query->setParameter('query2', $text2);
        $query->setParameter('ws', $ws);
        return $query->getResult();
    }

    public function findRecibosByWorkspaceOnDescendantOrder($workspace)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Recibo r WHERE r.workspace = :workspace ORDER BY r.id DESC');
        $query->setParameter('workspace', $workspace);
        return $query->getResult();
    }

    public function findIngresosByWorkspaceOnDescendantOrder($workspace)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Recibo r WHERE r.workspace = :workspace AND r.tipo = :ingreso ORDER BY r.id DESC');
        $query->setParameter('workspace', $workspace);
        $query->setParameter('ingreso', 'Ingreso');
        return $query->getResult();
    }

    public function findEgresosByWorkspaceOnDescendantOrder($workspace)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Recibo r WHERE r.workspace = :workspace AND r.tipo <> :ingreso ORDER BY r.id DESC');
        $query->setParameter('workspace', $workspace);
        $query->setParameter('ingreso', 'Ingreso');
        return $query->getResult();
    }

    public function findOrdenados()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Recibo r ORDER BY r.id DESC');
        return $query->getResult();
    }

    public function findOrdenados1()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT r FROM BackendBundle:Recibo r ORDER BY r.id ASC');
        return $query->getResult();
    }

    public function findBusquedaAvanzada($ws, $aavv, $c507, $lbrs, $sisgerCode, $from, $to, $user, $tipo, $importe_desde, $importe_hasta, $cuenta, $concepto, $recibide, $reply, $estado)
    {
        $code = '%' . $sisgerCode . '%';
        $code1 = $sisgerCode . '%';
        $code2 = '%' . $sisgerCode;

        $recibi = '%' . $recibide . '%';
        $recibi1 = $recibide . '%';
        $recibi2 = '%' . $recibide;

        $select = 'SELECT r FROM BackendBundle:Recibo r';
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
        if ($user) {
            $where[] = 'r.usuario = :user';
        }

        if ($reply) {
            $where[] = 'r.expediente = :reply';
        }

        $activo = true;
        if ($estado == 2) {
            $where[] = 'r.activo = :activo';
        } elseif ($estado == 3) {
            $activo = false;
            $where[] = 'r.activo = :activo';
        }

        if ($cuenta) {
            $select = 'SELECT r, e FROM BackendBundle:Recibo r JOIN r.entrada e';
            $where[] = 'e.banking = :cuenta';
        }


        if ($from && $to) {
            $where[] = ' r.fecha BETWEEN :from AND :to';
            $where[] = ' r.fecha BETWEEN :from AND :to';
        } elseif ($from && !$to) {
            $where[] = ' r.fecha >= :from';
        } elseif (!$from && $to) {
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
            if ($tipo == 'Ingreso' || $tipo == 'cuentaxCobrar') {
                $whereTipo = 'r.id IN (SELECT i.id FROM BackendBundle:Ingreso i';

                if ($concepto) {
                    $whereTipo = $whereTipo . ' LEFT JOIN i.servicios s WHERE s.id = :concepto';
                }
                if ($recibide) {
                    if ($concepto) {
                        $whereTipo = $whereTipo . ' AND ';
                    } else {
                        $whereTipo = $whereTipo . ' WHERE ';
                    }
                    $whereTipo = $whereTipo . '(i.recibiDe = :recibi OR i.recibiDe LIKE :recibi1 OR i.recibiDe LIKE :recibi2)';
                }
                if($concepto || $recibide){
                    $whereTipo = $whereTipo . ' AND i.cuentaXCobrar = :cuentaXCobrar';
                }
                else{
                    $whereTipo = $whereTipo . ' WHERE i.cuentaXCobrar = :cuentaXCobrar';
                }
                $whereTipo = $whereTipo . ')';
                $where[] = $whereTipo;
            } elseif ($tipo == 'Gasto') {
                $whereTipo = 'r.id IN (SELECT i.id FROM BackendBundle:Gasto i';

                if ($concepto) {
                    $whereTipo = $whereTipo . ' LEFT JOIN i.conceptosGasto s WHERE s.id = :concepto';
                }
                if ($recibide) {
                    if ($concepto) {
                        $whereTipo = $whereTipo . ' AND ';
                    } else {
                        $whereTipo = $whereTipo . ' WHERE ';
                    }
                    $whereTipo = $whereTipo . '(i.pagueA = :recibi OR i.pagueA LIKE :recibi1 OR i.pagueA LIKE :recibi2)';
                }
                $whereTipo = $whereTipo . ')';
                $where[] = $whereTipo;
            } elseif ($tipo == 'Costo') {
                $whereTipo = 'r.id IN (SELECT i.id FROM BackendBundle:Costo i';

                if ($concepto) {
                    $whereTipo = $whereTipo . ' LEFT JOIN i.conceptosCosto s WHERE s.id = :concepto';
                }
                if ($recibide) {
                    if ($concepto) {
                        $whereTipo = $whereTipo . ' AND ';
                    } else {
                        $whereTipo = $whereTipo . ' WHERE ';
                    }
                    $whereTipo = $whereTipo . '(i.pagueA = :recibi OR i.pagueA LIKE :recibi1 OR i.pagueA LIKE :recibi2)';
                }
                $whereTipo = $whereTipo . ')';
                $where[] = $whereTipo;
            } elseif ($tipo == 'Costo recurrente') {
                $whereTipo = 'r.id IN (SELECT i.id FROM BackendBundle:CostoRecurrente i';

                if ($concepto) {
                    $whereTipo = $whereTipo . ' LEFT JOIN i.inventarios s WHERE s.id = :concepto';
                }
                if ($recibide) {
                    if ($concepto) {
                        $whereTipo = $whereTipo . ' AND ';
                    } else {
                        $whereTipo = $whereTipo . ' WHERE ';
                    }
                    $whereTipo = $whereTipo . '(i.pagueA = :recibi OR i.pagueA LIKE :recibi1 OR i.pagueA LIKE :recibi2)';
                }
                $whereTipo = $whereTipo . ')';
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

        if ($tipo == 'Ingreso'){
            $query->setParameter('cuentaXCobrar', 0);
        }

        if ($tipo == 'cuentaxCobrar'){
            $query->setParameter('cuentaXCobrar', 1);
        }

        if ($sisgerCode) {
            $query->setParameter('code', $code);
            $query->setParameter('code1', $code1);
            $query->setParameter('code2', $code2);
        }
        if ($recibide) {
            $query->setParameter('recibi', $recibi);
            $query->setParameter('recibi1', $recibi1);
            $query->setParameter('recibi2', $recibi2);
        }
        if ($concepto) {
            $query->setParameter('concepto', $concepto);
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

    public function findImporteBusquedaAvanzada($ws, $aavv, $c507, $lbrs, $sisgerCode, $from, $to, $user, $tipo, $importe_desde, $importe_hasta, $cuenta, $concepto, $recibide, $reply, $estado)
    {
        $code = '%' . $sisgerCode . '%';
        $code1 = $sisgerCode . '%';
        $code2 = '%' . $sisgerCode;

        $recibi = '%' . $recibide . '%';
        $recibi1 = $recibide . '%';
        $recibi2 = '%' . $recibide;

        $select = 'SELECT SUM(r.importe) FROM BackendBundle:Recibo r';
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
        if ($user) {
            $where[] = 'r.usuario = :user';
        }

        if ($reply) {
            $where[] = 'r.expediente = :reply';
        }

        $activo = true;
        if ($estado == 2) {
            $where[] = 'r.activo = :activo';
        } elseif ($estado == 3) {
            $activo = false;
            $where[] = 'r.activo = :activo';
        }

        if ($cuenta) {
            $select = 'SELECT SUM(r.importe) FROM BackendBundle:Recibo r JOIN r.entrada e';
            $where[] = 'e.banking = :cuenta';
        }


        if ($from && $to) {
            $where[] = ' r.fecha BETWEEN :from AND :to';
            $where[] = ' r.fecha BETWEEN :from AND :to';
        } elseif ($from && !$to) {
            $where[] = ' r.fecha >= :from';
        } elseif (!$from && $to) {
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
            if ($tipo == 'Ingreso' || $tipo == 'cuentaxCobrar') {
                $whereTipo = 'r.id IN (SELECT i.id FROM BackendBundle:Ingreso i';

                if ($concepto) {
                    $whereTipo = $whereTipo . ' LEFT JOIN i.servicios s WHERE s.id = :concepto';
                }
                if ($recibide) {
                    if ($concepto) {
                        $whereTipo = $whereTipo . ' AND ';
                    } else {
                        $whereTipo = $whereTipo . ' WHERE ';
                    }
                    $whereTipo = $whereTipo . '(i.recibiDe = :recibi OR i.recibiDe LIKE :recibi1 OR i.recibiDe LIKE :recibi2)';
                }
                if($concepto || $recibide){
                    $whereTipo = $whereTipo . ' AND i.cuentaXCobrar = :cuentaXCobrar';
                }
                else{
                    $whereTipo = $whereTipo . ' WHERE i.cuentaXCobrar = :cuentaXCobrar';
                }
                $whereTipo = $whereTipo . ')';
                $where[] = $whereTipo;
            } elseif ($tipo == 'Gasto') {
                $whereTipo = 'r.id IN (SELECT i.id FROM BackendBundle:Gasto i';

                if ($concepto) {
                    $whereTipo = $whereTipo . ' LEFT JOIN i.conceptosGasto s WHERE s.id = :concepto';
                }
                if ($recibide) {
                    if ($concepto) {
                        $whereTipo = $whereTipo . ' AND ';
                    } else {
                        $whereTipo = $whereTipo . ' WHERE ';
                    }
                    $whereTipo = $whereTipo . '(i.pagueA = :recibi OR i.pagueA LIKE :recibi1 OR i.pagueA LIKE :recibi2)';
                }
                $whereTipo = $whereTipo . ')';
                $where[] = $whereTipo;
            } elseif ($tipo == 'Costo') {
                $whereTipo = 'r.id IN (SELECT i.id FROM BackendBundle:Costo i';

                if ($concepto) {
                    $whereTipo = $whereTipo . ' LEFT JOIN i.conceptosCosto s WHERE s.id = :concepto';
                }
                if ($recibide) {
                    if ($concepto) {
                        $whereTipo = $whereTipo . ' AND ';
                    } else {
                        $whereTipo = $whereTipo . ' WHERE ';
                    }
                    $whereTipo = $whereTipo . '(i.pagueA = :recibi OR i.pagueA LIKE :recibi1 OR i.pagueA LIKE :recibi2)';
                }
                $whereTipo = $whereTipo . ')';
                $where[] = $whereTipo;
            } elseif ($tipo == 'Costo recurrente') {
                $whereTipo = 'r.id IN (SELECT i.id FROM BackendBundle:CostoRecurrente i';

                if ($concepto) {
                    $whereTipo = $whereTipo . ' LEFT JOIN i.inventarios s WHERE s.id = :concepto';
                }
                if ($recibide) {
                    if ($concepto) {
                        $whereTipo = $whereTipo . ' AND ';
                    } else {
                        $whereTipo = $whereTipo . ' WHERE ';
                    }
                    $whereTipo = $whereTipo . '(i.pagueA = :recibi OR i.pagueA LIKE :recibi1 OR i.pagueA LIKE :recibi2)';
                }
                $whereTipo = $whereTipo . ')';
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

        if ($tipo == 'Ingreso'){
            $query->setParameter('cuentaXCobrar', 0);
        }

        if ($tipo == 'cuentaxCobrar'){
            $query->setParameter('cuentaXCobrar', 1);
        }

        if ($sisgerCode) {
            $query->setParameter('code', $code);
            $query->setParameter('code1', $code1);
            $query->setParameter('code2', $code2);
        }
        if ($recibide) {
            $query->setParameter('recibi', $recibi);
            $query->setParameter('recibi1', $recibi1);
            $query->setParameter('recibi2', $recibi2);
        }
        if ($concepto) {
            $query->setParameter('concepto', $concepto);
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

    public function findTotalImporteRecibosByRangeAndTypeAndUser($from, $to, $type, $user)
    {
        $consulta = 'SELECT SUM(v.importe) FROM BackendBundle:Recibo v WHERE v.activo = :activo AND v.usuario = :usuario AND v.fecha BETWEEN :from AND :to AND v.tipo = :tipo';
        $em = $this->getEntityManager();
        $query = $em->createQuery($consulta);
        $query->setParameter('from', $from);
        $query->setParameter('to', $to);
        $query->setParameter('activo', true);
        $query->setParameter('tipo', $type);
        $query->setParameter('usuario', $user);

        return $query->getResult();
    }
}