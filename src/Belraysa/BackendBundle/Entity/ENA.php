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
 * Belraysa\BackendBundle\Entity\ENA
 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\ENARepository")
 * @ORM\Table()
 */
class ENA extends Concepto
{

    /**
     * @Assert\DateTime()
     * @ORM\Column(name="fechaEntrada", type="datetime")
     */
    private $fechaEntrada;

    /**
     * @Assert\DateTime()
     * @ORM\Column(name="fechaSalida", type="datetime")
     */
    private $fechaSalida;



    /**
     * @return mixed
     */
    public function getFechaEntrada()
    {
        return $this->fechaEntrada;
    }

    /**
     * @param mixed $fechaEntrada
     */
    public function setFechaEntrada($fechaEntrada)
    {
        $this->fechaEntrada = $fechaEntrada;
    }

    /**
     * @return mixed
     */
    public function getFechaSalida()
    {
        return $this->fechaSalida;
    }

    /**
     * @param mixed $fechaSalida
     */
    public function setFechaSalida($fechaSalida)
    {
        $this->fechaSalida = $fechaSalida;
    }


} 