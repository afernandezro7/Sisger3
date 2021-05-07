<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 14:52
 */

namespace Belraysa\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class MenajeRepository extends EntityRepository
{

    public function findOrdenadas()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT e FROM BackendBundle:Menaje e ORDER BY e.fechaCreacion DESC');
        return $query->getResult();
    }

    public function advanceSearch($text)
    {


        $em = $this->getEntityManager();

        $select = 'SELECT e FROM BackendBundle:Menaje e ';
       

        if(strpos($text, 'Contenedor # ') !== false){
            $join = 'JOIN e.contenedor c ';
            $where = 'WHERE c.indiceAnual = :query ';

        }else{

            $text1 = '%' . $text . '%';
            $text2 = $text . '%';
            $text3 = '%' . $text;

            $join = 'JOIN e.remitente r ';
            $where = 'WHERE '.
                        'e.sisgerCode LIKE :query1 OR '.  
                        'e.sisgerCode LIKE :query2 OR '. 
                        'e.sisgerCode LIKE :query3 OR '. 
                        'r.firstName LIKE :query1 OR '. 
                        'r.firstName LIKE :query2 OR '. 
                        'r.firstName LIKE :query3 OR '. 
                        'r.lastName LIKE :query1 OR '. 
                        'r.lastName LIKE :query2 OR '. 
                        'r.lastName LIKE :query3 '
            ;

        }

        $sql = $select . $join . $where . 'ORDER BY e.fechaCreacion DESC';
        $query = $em->createQuery($sql);

        if(strpos($text, 'Contenedor # ') !== false){

            $indiceAnual = explode(" ", $text)[2];
            $query->setParameter('query', $indiceAnual);
        }else {
            $query->setParameter('query1', $text1);
            $query->setParameter('query2', $text2);
            $query->setParameter('query3', $text3);

        }
        return $query->getResult();
    }


}