<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 14:52
 */

namespace Belraysa\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TipoContenedorRepository extends EntityRepository
{

    public function findBusquedaSimple($text)
    {
        $text = '%' . $text . '%';
        $text1 = $text . '%';
        $text2 = '%' . $text;

        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c FROM BackendBundle:TipoContenedor c WHERE (c.nombre LIKE :query OR c.nombre LIKE :query1 OR c.nombre LIKE :query2) ORDER BY c.nombre ASC');
        $query->setParameter('query', $text);
        $query->setParameter('query1', $text1);
        $query->setParameter('query2', $text2);
        return $query->getResult();
    }



} 