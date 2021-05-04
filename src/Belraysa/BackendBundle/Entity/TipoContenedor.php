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
 * Belraysa\BackendBundle\Entity\TipoContenedor
 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\TipoContenedorRepository")
 * @ORM\Table()
 */
class TipoContenedor
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
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=999999999)
     */
    private $nombre;

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
    private $maxPesoKg;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Contenedor", mappedBy="tipo", cascade={"remove"})
     */
    private $contenedores;

    public function __construct()
    {
        $this->contenedores = new ArrayCollection();
    }

    public function addContenedor(Contenedor $contenedor)
    {
        $this->contenedores[] = $contenedor;
        $contenedor->setTipo($this);
    }

    public function removeContenedor(Contenedor $contenedor)
    {
        $this->contenedores->removeElement($contenedor);
        $contenedor->setTipo(null);
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
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param string $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * @return float
     */
    public function getVolumenM3()
    {
        return $this->volumenM3;
    }

    /**
     * @param float $volumenM3
     */
    public function setVolumenM3($volumenM3)
    {
        $this->volumenM3 = $volumenM3;
    }

    /**
     * @return float
     */
    public function getMaxPesoKg()
    {
        return $this->maxPesoKg;
    }

    /**
     * @param float $maxPesoKg
     */
    public function setMaxPesoKg($maxPesoKg)
    {
        $this->maxPesoKg = $maxPesoKg;
    }

    /**
     * @return mixed
     */
    public function getContenedores()
    {
        return $this->contenedores;
    }

    /**
     * @param mixed $contenedores
     */
    public function setContenedores($contenedores)
    {
        $this->contenedores = $contenedores;
    }

    public function __toString()
    {
      return $this->getNombre();
    }


}