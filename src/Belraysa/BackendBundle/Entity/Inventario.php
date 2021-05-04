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
 * Belraysa\BackendBundle\Entity\Inventario
 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\InventarioRepository")
 * @ORM\Table()
 */
class Inventario
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
     * @ORM\Column(name="nombre", type="string")
     */
    private $nombre;


    /**
     * @ORM\ManyToMany(targetEntity="Belraysa\BackendBundle\Entity\CostoRecurrente", mappedBy="inventarios")
     */
    private $costosRecurrentes;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Workspace", inversedBy="inventarios")
     * @ORM\JoinColumn(name="workspace", referencedColumnName="id")
     */
    private $workspace;

    /**
     * @return mixed
     */
    public function getCostosRecurrentes()
    {
        return $this->costosRecurrentes;
    }

    /**
     * @param mixed $costosRecurrentes
     */
    public function setCostosRecurrentes($costosRecurrentes)
    {
        $this->costosRecurrentes = $costosRecurrentes;
    }

    public function __construct()
    {
        $this->costosRecurrentes = new ArrayCollection();
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

    function __toString()
    {
        return $this->getNombre();
    }

    public function addCostoRecurrente(CostoRecurrente $costoRecurrente)
    {
        if (!$this->hasCostoRecurrente($costoRecurrente)) {
            $this->costosRecurrentes->add($costoRecurrente);
        }
    }

    public function removeCostoRecurrente(CostoRecurrente $costoRecurrente)
    {
        if ($this->hasCostoRecurrente($costoRecurrente)) {
            $this->costosRecurrentes->removeElement($costoRecurrente);
        }
    }

    public function hasCostoRecurrente(CostoRecurrente $costoRecurrente)
    {
        return $this->costosRecurrentes->contains($costoRecurrente);
    }
}