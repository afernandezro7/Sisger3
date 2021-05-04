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
 * Belraysa\BackendBundle\Entity\Mercancia
 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\MercanciaRepository")
 * @ORM\Table()
 */
class Mercancia
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
     * @Assert\DateTime()
     * @ORM\Column(name="fechaCreacion", type="datetime")
     */
    private $fechaCreacion;

    /**
     * @var string $descripcion
     *
     * @ORM\Column(name="descripcion", type="string", length=999999999)
     */
    private $descripcion;

    /**
     * @var integer $cantidad
     *
     * @ORM\Column(name="cantidad", type="integer")
     */
    private $cantidad;


    /**
     * @var float $alturaCm
     *
     * @ORM\Column(name="alturaCm", type="float", precision=2)
     */
    private $alturaCm;

    /**
     * @var float $anchoCm
     *
     * @ORM\Column(name="anchoCm", type="float", precision=2)
     */
    private $anchoCm;

    /**
     * @var float $profundidadCm
     *
     * @ORM\Column(name="profundidadCm", type="float", precision=2)
     */
    private $profundidadCm;

    /**
     * @var float $volumenM3
     *
     * @ORM\Column(name="volumenM3", type="float", precision=2)
     */
    private $volumenM3;


    /**
     * @var float $pesoKg
     *
     * @ORM\Column(name="pesoKg", type="float", precision=2)
     */
    private $pesoKg;

    /**
     * @var float $pesoLb
     *
     * @ORM\Column(name="pesoLb", type="float", precision=2)
     */
    private $pesoLb;

    /**
     * @var float $relacion
     *
     * @ORM\Column(name="relacion", type="float", precision=2)
     */
    private $relacion;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Bulto", inversedBy="mercancias")
     * @ORM\JoinColumn(name="bulto", referencedColumnName="id")
     */
    private $bulto;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Arancel", inversedBy="mercancias")
     * @ORM\JoinColumn(name="arancel", referencedColumnName="id")
     */
    private $arancel;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Tarifa", inversedBy="mercancias")
     * @ORM\JoinColumn(name="tarifa", referencedColumnName="id")
     */
    private $tarifa;

    /**
     * @var float $tarifaAlternativa
     *
     * @ORM\Column(name="tarifaAlternativa", type="float", precision=2, nullable=true)
     */
    private $tarifaAlternativa;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Mercancia", mappedBy="miRelacionada", cascade={"all"})
     */
    private $relacionadasConmigo;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Mercancia", inversedBy="relacionadasConmigo")
     * @ORM\JoinColumn(name="miRelacionada", referencedColumnName="id")
     */
    private $miRelacionada;

    public function __construct()
    {
        $this->relacionadasConmigo = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * @param int $cantidad
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }

    /**
     * @return mixed
     */
    public function getBulto()
    {
        return $this->bulto;
    }

    /**
     * @param mixed $bulto
     */
    public function setBulto($bulto)
    {
        $this->bulto = $bulto;
    }


    /**
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * @param string $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    /**
     * @return mixed
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * @param mixed $fechaCreacion
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;
    }

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
     * @return int
     */
    public function getPesoKg()
    {
        return $this->pesoKg;
    }

    /**
     * @param int $pesoKg
     */
    public function setPesoKg($pesoKg)
    {
        $this->pesoKg = $pesoKg;
    }

    /**
     * @return int
     */
    public function getPesoLb()
    {
        return $this->pesoLb;
    }

    /**
     * @param int $pesoLb
     */
    public function setPesoLb($pesoLb)
    {
        $this->pesoLb = $pesoLb;
    }

    /**
     * @return float
     */
    public function getRelacion()
    {
        return $this->relacion;
    }

    /**
     * @param float $relacion
     */
    public function setRelacion($relacion)
    {
        $this->relacion = $relacion;
    }

    /**
     * @return int
     */
    public function getVolumenM3()
    {
        return $this->volumenM3;
    }

    /**
     * @param int $volumenM3
     */
    public function setVolumenM3($volumenM3)
    {
        $this->volumenM3 = $volumenM3;
    }

    /**
     * @return float
     */
    public function getAlturaCm()
    {
        return $this->alturaCm;
    }

    /**
     * @param float $alturaCm
     */
    public function setAlturaCm($alturaCm)
    {
        $this->alturaCm = $alturaCm;
    }

    /**
     * @return float
     */
    public function getAnchoCm()
    {
        return $this->anchoCm;
    }

    /**
     * @param float $anchoCm
     */
    public function setAnchoCm($anchoCm)
    {
        $this->anchoCm = $anchoCm;
    }

    /**
     * @return float
     */
    public function getProfundidadCm()
    {
        return $this->profundidadCm;
    }

    /**
     * @param float $profundidadCm
     */
    public function setProfundidadCm($profundidadCm)
    {
        $this->profundidadCm = $profundidadCm;
    }

    /**
     * @return mixed
     */
    public function getArancel()
    {
        return $this->arancel;
    }

    /**
     * @param mixed $arancel
     */
    public function setArancel($arancel)
    {
        $this->arancel = $arancel;
    }

    /**
     * @return mixed
     */
    public function getTarifa()
    {
        return $this->tarifa;
    }

    /**
     * @param mixed $tarifa
     */
    public function setTarifa($tarifa)
    {
        $this->tarifa = $tarifa;
    }

    /**
     * @return mixed
     */
    public function getMiRelacionada()
    {
        return $this->miRelacionada;
    }

    /**
     * @param mixed $miRelacionada
     */
    public function setMiRelacionada($miRelacionada)
    {
        $this->miRelacionada = $miRelacionada;
    }


    /**
     * @return mixed
     */
    public function getRelacionadasConmigo()
    {
        return $this->relacionadasConmigo;
    }

    /**
     * @param mixed $relacionadasConmigo
     */
    public function setRelacionadasConmigo($relacionadasConmigo)
    {
        $this->relacionadasConmigo = $relacionadasConmigo;
    }


    public function addRelacionadaConmigo(Mercancia $relacionada)
    {
        if (!$this->hasRelacionadaConmigo($relacionada)) {
            $this->relacionadasConmigo->add($relacionada);
            $relacionada->setMiRelacionada($this);
        }
    }

    public function removeRelacionadaConmigo(Mercancia $relacionada)
    {
        if ($this->hasRelacionadaConmigo($relacionada)) {
            $this->relacionadasConmigo->removeElement($relacionada);
            $relacionada->setMiRelacionada(null);
        }
    }

    public function hasRelacionadaConmigo(Mercancia $relacionada)
    {
        return $this->relacionadasConmigo->contains($relacionada);
    }

    function __toString()
    {
        return $this->getDescripcion() . ' - Bulto #' . $this->getBulto()->getIndice() . ' - ' . $this->getBulto()->getConcepto()->getSisgerCode();
    }

    public function getValorAduanal()
    {
        return $this->getArancel()->getArancel();
    }

    /**
     * @return float
     */
    public function getTarifaAlternativa()
    {
        return $this->tarifaAlternativa;
    }

    /**
     * @param float $tarifaAlternativa
     */
    public function setTarifaAlternativa($tarifaAlternativa)
    {
        $this->tarifaAlternativa = $tarifaAlternativa;
    }

    public function __clone()
    {
        $this->id = null;
    }
}