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
 * Belraysa\BackendBundle\Entity\ConceptoCosto
 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\ConceptoCostoRepository")
 * @ORM\Table()
 */
class ConceptoCosto
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string")
     */
    private $nombre;

    /**
     * @ORM\ManyToMany(targetEntity="Belraysa\BackendBundle\Entity\Costo", mappedBy="conceptosCosto")
     */
    private $costos;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Workspace", inversedBy="conceptosCosto")
     * @ORM\JoinColumn(name="workspace", referencedColumnName="id")
     */
    private $workspace;

    public function __construct()
    {
        $this->costos = new ArrayCollection();
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
     * @return mixed
     */
    public function getCostos()
    {
        return $this->costos;
    }

    /**
     * @param mixed $costos
     */
    public function setCostos($costos)
    {
        $this->costos = $costos;
    }

    /**
     * @return mixed
     */
    public function getWorkspace()
    {
        return $this->workspace;
    }

    /**
     * @param mixed $workspace
     */
    public function setWorkspace($workspace)
    {
        $this->workspace = $workspace;
    }


    public function __toString()
    {
        return $this->getNombre();
    }

    public function addCosto(Costo $costo)
    {
        if (!$this->hasCosto($costo)) {
            $this->costos->add($costo);
        }
    }

    public function removeCosto(Costo $costo)
    {
        if ($this->hasCosto($costo)) {
            $this->costos->removeElement($costo);
        }
    }

    public function hasCosto(Costo $costo)
    {
        return $this->costos->contains($costo);
    }
}