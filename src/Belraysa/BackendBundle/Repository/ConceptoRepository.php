<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 14:52
 */

namespace Belraysa\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ConceptoRepository extends EntityRepository
{

    public function findXTipoXContenedor($tipo, $contenedor)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c FROM BackendBundle:Concepto c WHERE c.tipo = :tipo AND c.contenedor = :contenedor');
        $query->setParameter('tipo', $tipo);
        $query->setParameter('contenedor', $contenedor);
        return $query->getResult();
    }
    public function findBusquedaSimple($text)
    {
        $value = '%' . $text . '%';

        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT c, e, n FROM BackendBundle:Concepto c JOIN c.consignado e JOIN c.contenedor n WHERE c.sisgerCode LIKE :query OR CONCAT(e.firstName, ' ', e.lastName) LIKE :query OR e.dni LIKE :query OR e.passport LIKE :query ORDER BY c.fechaCreacion DESC");
        $query->setParameter('query', $value);
        /*$query->setMaxResults(1000);*/
        return $query->getResult();
    }

}