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
 * Belraysa\BackendBundle\Entity\Costo
 * @ORM\Entity()
 * @ORM\Table()
 */
class Costo extends Recibo
{

    /**
     * @ORM\ManyToMany(targetEntity="Belraysa\BackendBundle\Entity\ConceptoCosto")
     * @ORM\JoinTable(name="costo_conceptocosto",
     * joinColumns={@ORM\JoinColumn(name="costo_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="conceptocosto_id", referencedColumnName="id")})
     */
    private $conceptosCosto;

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
     * @ORM\OneToOne(targetEntity="Belraysa\BackendBundle\Entity\IdEgreso", inversedBy="costo")
     * @ORM\JoinColumn(name="idEgreso", referencedColumnName="id")
     **/
    private $idEgreso;

    public function __construct()
    {
        $this->conceptosCosto = new ArrayCollection();
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
    public function getConceptosCosto()
    {
        return $this->conceptosCosto;
    }

    /**
     * @param mixed $conceptosCosto
     */
    public function setConceptosCosto($conceptosCosto)
    {
        $this->conceptosCosto = $conceptosCosto;
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

    public function addConcepto(ConceptoCosto $concepto)
    {
        if (!$this->hasConcepto($concepto)) {
            $concepto->addCosto($this);
            $this->conceptosCosto->add($concepto);
        }
    }

    public function removeConcepto(ConceptoCosto $concepto)
    {
        if ($this->hasConcepto($concepto)) {
            $this->conceptosCosto->removeElement($concepto);
            $concepto->removeCosto($this);
        }
    }

    public function hasConcepto(ConceptoCosto $concepto)
    {
        return $this->conceptosCosto->contains($concepto);
    }
}