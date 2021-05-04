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
 * Belraysa\BackendBundle\Entity\UM
 * @ORM\Entity
 * @ORM\Table()
 */
class UM
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
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Arancel", mappedBy="um",cascade={"remove"})
     */
    private $aranceles;

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
    public function getAranceles()
    {
        return $this->aranceles;
    }

    /**
     * @param mixed $aranceles
     */
    public function setAranceles($aranceles)
    {
        $this->aranceles = $aranceles;
    }

    public function __toString()
    {
       return $this->getNombre();
    }

}