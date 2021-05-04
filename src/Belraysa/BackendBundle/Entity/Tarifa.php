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
 * Belraysa\BackendBundle\Entity\Tarifa
 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\TarifaRepository")
 * @ORM\Table()
 */
class Tarifa
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
     * @var integer $numero
     *
     * @ORM\Column(name="numero", type="integer")
     */
    private $numero;

    /**
     * @var string $articulo
     *
     * @ORM\Column(name="articulo", type="string", length=999999999)
     */
    private $articulo;

    /**
     * @var float $precio
     *
     * @ORM\Column(name="precio", type="float", precision=2)
     */
    private $precio;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Mercancia", mappedBy="tarifa")
     */
    private $mercancias;

    /**
     * @return string
     */
    public function getArticulo()
    {
        return $this->articulo;
    }

    /**
     * @param string $articulo
     */
    public function setArticulo($articulo)
    {
        $this->articulo = $articulo;
    }

    /**
     * @return float
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * @param float $precio
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;
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


    public function __toString()
    {
        return $this->getArticulo();
    }


}