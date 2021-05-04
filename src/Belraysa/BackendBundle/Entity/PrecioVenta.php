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
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Belraysa\BackendBundle\Entity\PrecioVenta
 * @ORM\Table()
 * @ORM\Entity()
 */
class PrecioVenta
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
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Reply", inversedBy="preciosVenta")
     * @ORM\JoinColumn(name="expediente", referencedColumnName="id")
     */
    private $expediente;


    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Reply", inversedBy="precioVentaLider")
     * @ORM\JoinColumn(name="expedienteLider", referencedColumnName="id")
     */
    private $expedienteLider;


    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Client", inversedBy="preciosVenta")
     * @ORM\JoinColumn(name="cliente", referencedColumnName="id")
     */
    private $cliente;

    /**
     * @var float $precio
     *
     * @ORM\Column(name="precio", type="float", nullable=true)
     */
    private $precio;

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
    public function getExpediente()
    {
        return $this->expediente;
    }

    /**
     * @param mixed $expediente
     */
    public function setExpediente($expediente)
    {
        $this->expediente = $expediente;
    }

    /**
     * @return mixed
     */
    public function getExpedienteLider()
    {
        return $this->expedienteLider;
    }

    /**
     * @param mixed $expedienteLider
     */
    public function setExpedienteLider($expedienteLider)
    {
        $this->expedienteLider = $expedienteLider;
    }

    /**
     * @return mixed
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * @param mixed $cliente
     */
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;
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

    public function __toString()
    {
        return $this->cliente->getFirstName() . ' ' . $this->cliente->getLastName();
    }


}