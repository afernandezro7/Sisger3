<?php
/**
 * Created by PhpStorm.
 * User: Rocio
 * Date: 07/10/2015
 * Time: 12:51
 */

namespace Belraysa\BackendBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * Belraysa\BackendBundle\Entity\IdEgreso
 * @ORM\Entity
 * @ORM\Table()
 */
class IdEgreso
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Belraysa\BackendBundle\Entity\Gasto", mappedBy="idEgreso")
     **/
    private $gasto;

    /**
     * @ORM\OneToOne(targetEntity="Belraysa\BackendBundle\Entity\Costo", mappedBy="idEgreso")
     **/
    private $costo;

    /**
     * @ORM\OneToOne(targetEntity="Belraysa\BackendBundle\Entity\CostoRecurrente", mappedBy="idEgreso")
     **/
    private $costoRecurrente;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getGasto()
    {
        return $this->gasto;
    }

    /**
     * @param mixed $gasto
     */
    public function setGasto($gasto)
    {
        $this->gasto = $gasto;
    }

    /**
     * @return mixed
     */
    public function getCosto()
    {
        return $this->costo;
    }

    /**
     * @param mixed $costo
     */
    public function setCosto($costo)
    {
        $this->costo = $costo;
    }

    /**
     * @return mixed
     */
    public function getCostoRecurrente()
    {
        return $this->costoRecurrente;
    }

    /**
     * @param mixed $costoRecurrente
     */
    public function setCostoRecurrente($costoRecurrente)
    {
        $this->costoRecurrente = $costoRecurrente;
    }

}