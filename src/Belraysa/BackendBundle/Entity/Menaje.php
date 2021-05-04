<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 13:15
 */

namespace Belraysa\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * Belraysa\BackendBundle\Entity\Menaje
 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\MenajeRepository")
 * @ORM\Table()
 */
class Menaje extends Concepto
{
    /**
     * @Assert\DateTime()
     * @ORM\Column(name="fechaAutorizacion", type="datetime")
     */
    private $fechaAutorizacion;

    /**
     * @return mixed
     */
    public function getFechaAutorizacion()
    {
        return $this->fechaAutorizacion;
    }

    /**
     * @param mixed $fechaAutorizacion
     */
    public function setFechaAutorizacion($fechaAutorizacion)
    {
        $this->fechaAutorizacion = $fechaAutorizacion;
    }


} 