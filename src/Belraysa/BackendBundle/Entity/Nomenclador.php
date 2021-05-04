<?php

namespace Belraysa\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Nomenclador
 *
 * @ORM\Table(name="Nomenclador")
 *
 * @ORM\Entity
 */
class Nomenclador
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Workspace", inversedBy="nomencladores")
     * @ORM\JoinColumn(name="workspace", referencedColumnName="id")
     */
    private $workspace;

    /**
     * @ORM\ManyToMany(targetEntity="Belraysa\BackendBundle\Entity\User", mappedBy="nomencladoresLectura")
     */
    private $usuariosLectura;

    /**
     * @ORM\ManyToMany(targetEntity="Belraysa\BackendBundle\Entity\User", mappedBy="nomencladoresEscritura")
     */
    private $usuariosEscritura;

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


    function __toString()
    {
        return $this->getNombre();
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


    /**
     * @return mixed
     */
    public function getUsuariosLectura()
    {
        return $this->usuariosLectura;
    }

    /**
     * @param mixed $usuariosLectura
     */
    public function setUsuariosLectura($usuariosLectura)
    {
        $this->usuariosLectura = $usuariosLectura;
    }

    /**
     * @return mixed
     */
    public function getUsuariosEscritura()
    {
        return $this->usuariosEscritura;
    }

    /**
     * @param mixed $usuariosEscritura
     */
    public function setUsuariosEscritura($usuariosEscritura)
    {
        $this->usuariosEscritura = $usuariosEscritura;
    }

}
