<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 14:52
 */

namespace Belraysa\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TarifaRepository extends EntityRepository
{

    public function findOrdenadas()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT t FROM BackendBundle:Tarifa t ORDER BY t.numero ASC');
        return $query->getResult();
    }

    public function findBusquedaAvanzada($articulo, $precio_desde, $precio_hasta)
    {
        $code = '%' . $articulo . '%';
        $code1 = $articulo . '%';
        $code2 = '%' . $articulo;


        $select = 'SELECT a FROM BackendBundle:Tarifa a';
        $where = array();

        if ($articulo) {
            $where[] = '(a.articulo = :code OR a.articulo LIKE :code1 OR a.articulo LIKE :code2)';
        }
        if ($precio_desde && $precio_hasta) {
            $where[] = ' a.precio BETWEEN :precio_desde AND :precio_hasta';
        } elseif ($precio_desde && !$precio_hasta) {
            $where[] = ' a.precio > :precio_desde';
        } elseif (!$precio_hasta && $precio_hasta) {
            $where[] = ' a.precio < :precio_hasta';
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
        $select = $select . ' ORDER BY a.numero ASC';
        // print_r($select);
        // die;
        $em = $this->getEntityManager();
        $query = $em->createQuery($select);

        if ($articulo) {
            $query->setParameter('code', $code);
            $query->setParameter('code1', $code1);
            $query->setParameter('code2', $code2);
        }
        if ($precio_desde && $precio_hasta) {
            $query->setParameter('precio_desde', $precio_desde);
            $query->setParameter('precio_hasta', $precio_hasta);
        } elseif ($precio_desde && !$precio_hasta) {
            $query->setParameter('precio_desde', $precio_desde);
        } elseif (!$precio_desde && $precio_hasta) {
            $query->setParameter('precio_hasta', $precio_hasta);
        }

        return $query->getResult();
    }
} 