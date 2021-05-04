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
 * Belraysa\BackendBundle\Entity\PaymentMethod
 * @ORM\Entity
 * @ORM\Table()
 */
class PaymentMethod
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
     * @var string $url
     *
     * @ORM\Column(name="url", type="string", nullable=false)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Receipe", mappedBy="paymentMethod",cascade={"remove"})
     */
    private $receipes;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Recibo", mappedBy="metodoPago",cascade={"remove"})
     */
    private $recibos;

    /**
     * @ORM\ManyToMany(targetEntity="Belraysa\BackendBundle\Entity\Banking", mappedBy="metodosPago")
     */
    private $bankings;

    function __construct($recibos)
    {
        $this->recibos = new ArrayCollection();
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getReceipes()
    {
        return $this->receipes;
    }

    /**
     * @param mixed $receipes
     */
    public function setReceipes($receipes)
    {
        $this->receipes = $receipes;
    }

    /**
     * @return mixed
     */
    public function getRecibos()
    {
        return $this->recibos;
    }

    /**
     * @param mixed $recibos
     */
    public function setRecibos($recibos)
    {
        $this->recibos = $recibos;
    }


    public function __toString()
    {
        return $this->name;
    }

    public function addRecibo(Recibo $recibo)
    {
        if (!$this->hasRecibo($recibo)) {
            $recibo->setMetodoPago($this);
            $this->recibos->add($recibo);
        }
    }

    public function removeRecibo(Recibo $recibo)
    {
        if ($this->hasRecibo($recibo)) {
            $this->recibos->removeElement($recibo);
            $recibo->setMetodoPago(null);
        }
    }

    public function hasRecibo(Recibo $recibo)
    {
        return $this->recibos->contains($recibo);
    }

    /**
     * @return mixed
     */
    public function getBankings()
    {
        return $this->bankings;
    }

    /**
     * @param mixed $bankings
     */
    public function setBankings($bankings)
    {
        $this->bankings = $bankings;
    }


}