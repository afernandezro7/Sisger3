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
 * Belraysa\BackendBundle\Entity\Gasto
 * @ORM\Entity()
 * @ORM\Table()
 */
class Gasto extends Recibo
{
    /**
     * @ORM\ManyToMany(targetEntity="Belraysa\BackendBundle\Entity\ConceptoGasto")
     * @ORM\JoinTable(name="gasto_conceptogasto",
     * joinColumns={@ORM\JoinColumn(name="gasto_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="conceptogasto_id", referencedColumnName="id")})
     */
    private $conceptosGasto;

    /**
     * @var string $detalles
     *
     * @ORM\Column(name="detalles", type="string", length=99999)
     */
    private $detalles;

    /**
     * @var string $pagueA
     *
     * @ORM\Column(name="pagueA", type="string")
     */
    private $pagueA;

    /**
     * @ORM\OneToOne(targetEntity="Belraysa\BackendBundle\Entity\IdEgreso", inversedBy="gasto")
     * @ORM\JoinColumn(name="idEgreso", referencedColumnName="id")
     **/
    private $idEgreso;

    public function __construct()
    {
        $this->conceptosGasto = new ArrayCollection();
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
    public function getConceptosGasto()
    {
        return $this->conceptosGasto;
    }

    /**
     * @param mixed $conceptosGasto
     */
    public function setConceptosGasto($conceptosGasto)
    {
        $this->conceptosGasto = $conceptosGasto;
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

    public function addConcepto(ConceptoGasto $concepto)
    {
        if (!$this->hasConcepto($concepto)) {
            $concepto->addGasto($this);
            $this->conceptosGasto->add($concepto);
        }
    }

    public function removeConcepto(ConceptoGasto $concepto)
    {
        if ($this->hasConcepto($concepto)) {
            $this->conceptosGasto->removeElement($concepto);
            $concepto->removeGasto($this);
        }
    }

    public function hasConcepto(ConceptoGasto $concepto)
    {
        return $this->conceptosGasto->contains($concepto);
    }

}