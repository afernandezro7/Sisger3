<?php

namespace Belraysa\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Mes
 *
 * @ORM\Table(name="Mes")
 *
 * @ORM\Entity()
 */
class Mes
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
     * @var integer
     *
     * @ORM\Column(name="numero", type="integer", nullable=false)
     */
    private $numero;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Contenedor", mappedBy="mes", cascade={"remove"})
     */
    private $contenedores;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Anno", inversedBy="meses",cascade={"persist"})
     * @ORM\JoinColumn(name="anno", referencedColumnName="id")
     */
    private $anno;


    public function __construct()
    {
        $this->contenedores = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getAnno()
    {
        return $this->anno;
    }

    /**
     * @param mixed $anno
     */
    public function setAnno($anno)
    {
        $this->anno = $anno;
    }

    /**
     * @return mixed
     */
    public function getContenedores()
    {
        return $this->contenedores;
    }

    public function getContenedoresActivos()
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
     * @return int
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @param int $numero
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }


    function __toString()
    {
        return $this->nombre;
    }

    public function addContenedor(Contenedor $contenedor)
    {
        $this->contenedores[] = $contenedor;
        $contenedor->setMes($this);
    }

    public function removeContenedor(Contenedor $contenedor)
    {
        $this->contenedores->removeElement($contenedor);
        $contenedor->setMes(null);
    }

    public function getContenedorsOrdenados()
    {
        for ($i = sizeof($this->contenedores) - 1; $i >= 0; $i--) {
            $swapped = false;
            for ($j = 0; $j < $i; $j++) {
                if ($this->contenedores[$j]->getFecha() > $this->contenedores[$j + 1]->getFecha()) {
                    $tmp = $this->contenedores[$j];
                    $this->contenedores[$j] = $this->contenedores[$j + 1];
                    $this->contenedores[$j + 1] = $tmp;
                    $swapped = true;
                }
            }
            if (!$swapped) return $this->contenedores;
        }
        return $this->contenedores;
    }
}
