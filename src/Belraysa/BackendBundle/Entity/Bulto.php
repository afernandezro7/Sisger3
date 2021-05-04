<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 13:15
 */

namespace Belraysa\BackendBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * Belraysa\BackendBundle\Entity\Bulto
 * @ORM\Table(name="Bulto")
 * @ORM\Entity()
 */
class Bulto
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
     * @var string $sisgerCode
     *
     * @ORM\Column(name="sisgerCode", type="string", nullable=true)
     */
    private $sisgerCode;
    /**
     * @var integer $indice
     *
     * @ORM\Column(name="indice", type="integer")
     */
    private $indice;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Mercancia", mappedBy="bulto",cascade={"all"})
     */
    private $mercancias;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Concepto", inversedBy="bultos")
     * @ORM\JoinColumn(name="concepto", referencedColumnName="id")
     */
    private $concepto;


    function __construct()
    {
        $this->mercancias = new ArrayCollection();
    }


    public function addMercancia(Mercancia $mercancia)
    {
        if (!$this->hasMercancia($mercancia)) {
            $mercancia->setBulto($this);
            $this->mercancias->add($mercancia);
        }
    }

    public function removeMercancia(Mercancia $mercancia)
    {
        if ($this->hasMercancia($mercancia)) {
            $this->mercancias->removeElement($mercancia);
            $mercancia->setBulto(null);
        }
    }

    public function hasMercancia(Mercancia $mercancia)
    {
        return $this->mercancias->contains($mercancia);
    }


    public function getValorAduanal()
    {
        $valor = 0;
        foreach ($this->mercancias as $mercancia) {
            if (!$mercancia->getMiRelacionada()) {
                $valor += $mercancia->getArancel()->getArancel();
            }
        }
        return $valor;
    }

    /**
     * @return mixed
     */
    public function getConcepto()
    {
        return $this->concepto;
    }

    /**
     * @param mixed $concepto
     */
    public function setConcepto($concepto)
    {
        $this->concepto = $concepto;
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

    /**
     * @return int
     */
    public function getIndice()
    {
        return $this->indice;
    }

    /**
     * @param int $indice
     */
    public function setIndice($indice)
    {
        $this->indice = $indice;
    }

    /**
     * @return string
     */
    public function getSisgerCode()
    {
        return $this->sisgerCode;
    }

    /**
     * @param string $sisgerCode
     */
    public function setSisgerCode($sisgerCode)
    {
        $this->sisgerCode = $sisgerCode;
    }

    /**
     * @return int
     */
    public function getPesoKg()
    {
        $valor = 0;
        foreach ($this->mercancias as $mercancia) {
            $valor += $mercancia->getPesoKg();
        }
        return $valor;
    }

    /**
     * @return int
     */
    public function getVolumenM3()
    {
        $valor = 0;
        foreach ($this->mercancias as $mercancia) {
            $valor += $mercancia->getVolumenM3();
        }
        return $valor;
    }

}