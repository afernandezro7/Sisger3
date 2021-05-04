<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 14:52
 */

namespace Belraysa\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PermisoRepository extends EntityRepository
{

    public function findPermisoPorNomencladorYUsuario($nomenclador, $usuario)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT p FROM BackendBundle:Permiso p WHERE p.nomenclador = :nomenclador AND p.usuario = :usuario ');
        $query->setParameter('nomenclador', $nomenclador);
        $query->setParameter('usuario', $usuario);
        return $query->getResult();
    }

}