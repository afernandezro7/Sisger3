<?php
/**
 * Created by PhpStorm.
 * User: Rocio
 * Date: 07/10/2015
 * Time: 12:51
 */

namespace Belraysa\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;


/**
 * Belraysa\BackendBundle\Entity\Arancel
 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\ArancelRepository")
 * @ORM\Table()
 */
class Arancel
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
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Capitulo", inversedBy="aranceles")
     * @ORM\JoinColumn(name="capitulo", referencedColumnName="id")
     */
    private $capitulo;

    /**
     * @var string $articulos
     *
     * @ORM\Column(name="articulos", type="string", length=999999999)
     */
    private $articulos;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\UM", inversedBy="aranceles")
     * @ORM\JoinColumn(name="um", referencedColumnName="id")
     */
    private $um;

    /**
     * @var integer $cantidad
     *
     * @ORM\Column(name="cantidad", type="integer")
     */
    private $cantidad;


    /**
     * @var float $arancel
     *
     * @ORM\Column(name="arancel", type="float", precision=2)
     */
    private $arancel;

    /**
     * @var string $observaciones
     *
     * @ORM\Column(name="observaciones", type="string", length=999999999)
     */
    private $observaciones;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Mercancia", mappedBy="arancel",cascade={"remove"})
     */
    private $mercancias;

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
     * @return string
     */
    public function getArticulos()
    {
        return $this->articulos;
    }

    /**
     * @param string $articulos
     */
    public function setArticulos($articulos)
    {
        $this->articulos = $articulos;
    }

    /**
     * @return string
     */
    public function getUm()
    {
        return $this->um;
    }

    /**
     * @param string $um
     */
    public function setUm($um)
    {
        $this->um = $um;
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
     * @return float
     */
    public function getArancel()
    {
        return $this->arancel;
    }

    /**
     * @param float $arancel
     */
    public function setArancel($arancel)
    {
        $this->arancel = $arancel;
    }

    /**
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * @param string $observaciones
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;
    }

    /**
     * @return int
     */
    public function getCapitulo()
    {
        return $this->capitulo;
    }

    /**
     * @param int $capitulo
     */
    public function setCapitulo($capitulo)
    {
        $this->capitulo = $capitulo;
    }

    /**
     * @return mixed
     */
    public function getMercancias()
    {
        return $this->mercancias;
    }

    /**
     * @param mixed $mercancias
     */
    public function setMercancias($mercancias)
    {
        $this->mercancias = $mercancias;
    }

    public function __toString()
    {
      return $this->getArticulos();
    }


}