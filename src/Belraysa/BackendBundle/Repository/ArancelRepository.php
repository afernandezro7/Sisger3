<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 14:52
 */

namespace Belraysa\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ArancelRepository extends EntityRepository
{
    public function getCapitulos()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c FROM BackendBundle:Capitulo c ORDER BY c.numero ASC');
        return $query->getResult();
    }

    public function findBusquedaAvanzada($capitulo, $articulo)
    {
        $code = '%' . $articulo . '%';
        $code1 = $articulo . '%';
        $code2 = '%' . $articulo;


        $select = 'SELECT a FROM BackendBundle:Arancel a';
        $where = array();

        if ($articulo) {
            $where[] = '(a.articulos = :code OR a.articulos LIKE :code1 OR a.articulos LIKE :code2)';
        }

        if ($capitulo) {
            $where[] = 'a.capitulo = :capitulo';
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

        if ($articulo) {
            $query->setParameter('code', $code);
            $query->setParameter('code1', $code1);
            $query->setParameter('code2', $code2);
        }

        if ($capitulo) {
            $query->setParameter('capitulo', $capitulo);
        }
        return $query->getResult();
    }
} 