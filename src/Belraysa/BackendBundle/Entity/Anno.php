<?php

namespace Belraysa\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Anno
 *
 * @ORM\Table(name="Anno")
 *
 * @ORM\Entity()
 */
class Anno
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
     * @var integer
     *
     * @ORM\Column(name="nombre", type="integer", nullable=false)
     */
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Mes", mappedBy="anno", cascade={"remove"})
     */
    private $meses;


    public function __construct()
    {
        $this->meses = new ArrayCollection();
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
    public function getMeses()
    {
        return $this->meses;
    }

    /**
     * @param mixed $meses
     */
    public function setMeses($meses)
    {
        $this->meses = $meses;
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

    public function addMes(Mes $mes)
    {
        $this->meses[] = $mes;
        $mes->setAnno($this);
    }

    public function removeMes(Mes $mes)
    {
        $this->meses->removeElement($mes);
        $mes->setAnno(null);
    }


    public function getMesesOrdenados()
    {
        for ($i = sizeof($this->meses) - 1; $i >= 0; $i--)
        {
            $swapped = false;
            for ($j = 0; $j < $i; $j++)
            {
                if ($this->meses[$j]->getNumero() > $this->meses[$j + 1]->getNumero())
                {
                    $tmp = $this->meses[$j];
                    $this->meses[$j] = $this->meses[$j + 1];
                    $this->meses[$j + 1] = $tmp;
                    $swapped = true;
                }
            }
            if (!$swapped) return $this->meses;
        }
        return $this->meses;
    }
}
