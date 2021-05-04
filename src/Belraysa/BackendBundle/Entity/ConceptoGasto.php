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
 * Belraysa\BackendBundle\Entity\ConceptoGasto
 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\ConceptoGastoRepository")
 * @ORM\Table()
 */
class ConceptoGasto
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
     * @ORM\ManyToMany(targetEntity="Belraysa\BackendBundle\Entity\Gasto", mappedBy="conceptosGasto")
     */
    private $gastos;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Workspace", inversedBy="conceptosGasto")
     * @ORM\JoinColumn(name="workspace", referencedColumnName="id")
     */
    private $workspace;

    public function __construct()
    {
        $this->gastos = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getGastos()
    {
        return $this->gastos;
    }

    /**
     * @param mixed $gastos
     */
    public function setGastos($gastos)
    {
        $this->gastos = $gastos;
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

    public function addGasto(Gasto $gasto)
    {
        if (!$this->hasGasto($gasto)) {
            $this->gastos->add($gasto);
        }
    }

    public function removeGasto(Gasto $gasto)
    {
        if ($this->hasGasto($gasto)) {
            $this->gastos->removeElement($gasto);
        }
    }

    public function hasGasto(Gasto $gasto)
    {
        return $this->gastos->contains($gasto);
    }

}