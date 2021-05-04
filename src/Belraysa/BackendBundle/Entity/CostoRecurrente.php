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
 * Belraysa\BackendBundle\Entity\CostoRecurrente
 * @ORM\Entity()
 * @ORM\Table()
 */
class CostoRecurrente extends Recibo
{

    /**
     * @ORM\ManyToMany(targetEntity="Belraysa\BackendBundle\Entity\Inventario")
     * @ORM\JoinTable(name="costorecurrente_inventario",
     * joinColumns={@ORM\JoinColumn(name="costorecurrente_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="inventario_id", referencedColumnName="id")})
     */
    private $inventarios;

    /**
     * @var string $pagueA
     *
     * @ORM\Column(name="pagueA", type="string")
     */
    private $pagueA;

    /**
     * @var string $detalles
     *
     * @ORM\Column(name="detalles", type="string", length=99999)
     */
    private $detalles;

    /**
     * @ORM\OneToOne(targetEntity="Belraysa\BackendBundle\Entity\IdEgreso", inversedBy="costoRecurrente")
     * @ORM\JoinColumn(name="idEgreso", referencedColumnName="id")
     **/
    private $idEgreso;

    public function __construct()
    {
        $this->inventarios = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getIdEgreso()
    {
        return $this->idEgreso;
    }

    /**
     * @param mixed $idEgreso
     */
    public function setIdEgreso($idEgreso)
    {
        $this->idEgreso = $idEgreso;
    }

    /**
     * @return mixed
     */
    public function getInventarios()
    {
        return $this->inventarios;
    }

    /**
     * @param mixed $inventarios
     */
    public function setInventarios($inventarios)
    {
        $this->inventarios = $inventarios;
    }



    /**
     * @return string
     */
    public function getPagueA()
    {
        return $this->pagueA;
    }

    /**
     * @param string $pagueA
     */
    public function setPagueA($pagueA)
    {
        $this->pagueA = $pagueA;
    }

    /**
     * @return string
     */
    public function getDetalles()
    {
        return $this->detalles;
    }

    /**
     * @param string $detalles
     */
    public function setDetalles($detalles)
    {
        $this->detalles = $detalles;
    }

    public function addInventario(Inventario $concepto)
    {
        if (!$this->hasInventario($concepto)) {
            $concepto->addCostoRecurrente($this);
            $this->inventarios->add($concepto);
        }
    }

    public function removeInventario(Inventario $concepto)
    {
        if ($this->hasInventario($concepto)) {
            $this->inventarios->removeElement($concepto);
            $concepto->removeCostoRecurrente($this);
        }
    }

    public function hasInventario(Inventario $concepto)
    {
        return $this->inventarios->contains($concepto);
    }
}