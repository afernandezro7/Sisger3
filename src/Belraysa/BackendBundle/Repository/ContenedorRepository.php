<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 14:52
 */

namespace Belraysa\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ContenedorRepository extends EntityRepository
{
    public function findBusquedaAvanzada($codigo, $sello, $tipo)
    {
        $code = '%' . $codigo . '%';
        $code1 = $codigo . '%';
        $code2 = '%' . $codigo;

        $sello = '%' . $sello . '%';
        $sello1 = $sello . '%';
        $sello2 = '%' . $sello;

        $select = 'SELECT c FROM BackendBundle:Contenedor c';
        $where = array();

        if ($codigo) {
            $where[] = '(c.codigo = :code OR c.codigo LIKE :code1 OR c.codigo LIKE :code2)';
        }

        if ($sello) {
            $where[] = '(c.sello = :sello OR c.sello LIKE :sello1 OR c.sello LIKE :sello2)';
        }

        if ($tipo) {
            $where[] = 'c.tipo = :tipo';
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

        if ($codigo) {
            $query->setParameter('code', $code);
            $query->setParameter('code1', $code1);
            $query->setParameter('code2', $code2);
        }
        if ($sello) {
            $query->setParameter('sello', $sello);
            $query->setParameter('sello1', $sello1);
            $query->setParameter('sello2', $sello2);
        }
        if ($tipo) {
            $query->setParameter('tipo', $tipo);
        }

        return $query->getResult();
    }

    public function findContenedorEnUso()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c FROM BackendBundle:Contenedor c WHERE c.estado =:completando');
        $query->setParameter('completando', 'COMPLETANDO');
        $query->setMaxResults(1);
        return $query->getResult();
    }

    public function findOrdenados()
    {
        $em = $this->getEntityManager();
        $containers = [];
        $query = $em->createQuery('SELECT c FROM BackendBundle:Contenedor c JOIN c.mes m JOIN m.anno a WHERE c.estado = \'COMPLETANDO\' ORDER BY a.nombre, m.numero ASC')->getResult();
        foreach($query as $c){
            $containers[] =$c;
        }
//        $contenedores = array_merge($containers, $query->getResult());
        $query = $em->createQuery('SELECT c FROM BackendBundle:Contenedor c JOIN c.mes m JOIN m.anno a WHERE c.estado = \'RESERVANDO\' ORDER BY a.nombre ASC, m.numero ASC, c.indice ASC')->getResult();
        foreach($query as $c){
            $containers[] =$c;
        }
//        $contenedores = array_merge($containers, $query->getResult());
        $query = $em->createQuery('SELECT c FROM BackendBundle:Contenedor c JOIN c.mes m JOIN m.anno a WHERE c.estado = \'CERRADO\' ORDER BY a.nombre DESC, m.numero DESC, c.indice DESC')->getResult();
        foreach($query as $c){
            $containers[] =$c;
        }
//        $contenedores = array_merge($containers, $query->getResult());
        return $containers;
    }

    public function findContenedoresAnteriores($anno, $mes)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c FROM BackendBundle:Contenedor c JOIN c.mes m JOIN m.anno a WHERE a.nombre <= :anno AND m.numero <= :mes ORDER BY a.nombre, m.numero ASC');
        $query->setParameter('anno', $anno);
        $query->setParameter('mes', $mes);
        return $query->getResult();
    }

    public function findContenedoresPosteriores($anno, $mes)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c FROM BackendBundle:Contenedor c JOIN c.mes m JOIN m.anno a WHERE a.nombre >= :anno AND m.numero > :mes ORDER BY a.nombre, m.numero ASC');
        $query->setParameter('anno', $anno);
        $query->setParameter('mes', $mes);
        return $query->getResult();
    }

    public function findContenedoresPosterioresIndice($anno, $indice)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c FROM BackendBundle:Contenedor c JOIN c.mes m JOIN m.anno a WHERE a.nombre >= :anno AND c.indiceAnual > :indice');
        $query->setParameter('anno', $anno);
        $query->setParameter('indice', $indice);
        return $query->getResult();
    }

    public function findContenedoresAnterioresAnno($anno, $mes)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c FROM BackendBundle:Contenedor c JOIN c.mes m JOIN m.anno a WHERE a.nombre = :anno AND m.numero <= :mes ORDER BY a.nombre, m.numero ASC');
        $query->setParameter('anno', $anno);
        $query->setParameter('mes', $mes);
        return $query->getResult();
    }

    public function findContenedoresPosterioresAnno($anno, $mes)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c FROM BackendBundle:Contenedor c JOIN c.mes m JOIN m.anno a WHERE a.nombre = :anno AND m.numero > :mes ORDER BY a.nombre, m.numero ASC');
        $query->setParameter('anno', $anno);
        $query->setParameter('mes', $mes);
        return $query->getResult();
    }

    public function findContenedoresParaCierreAutomatico($anno, $mes)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c FROM BackendBundle:Contenedor c JOIN c.mes m JOIN m.anno a WHERE a.nombre <= :anno AND m.numero < :mes AND c.estado <> :cerrado ORDER BY a.nombre, m.numero ASC');
        $query->setParameter('anno', $anno);
        $query->setParameter('mes', $mes);
        $query->setParameter('cerrado', 'CERRADO');
        return $query->getResult();
    }


}