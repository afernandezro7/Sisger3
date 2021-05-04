<?php
/**
 * Created by PhpStorm.
 * User: Rocio
 * Date: 07/10/2015
 * Time: 12:51
 */

namespace Belraysa\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;


/**
 * Belraysa\BackendBundle\Entity\Receipe
 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\ReceipeRepository")
 * @ORM\Table()
 */
class Receipe
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
     * @Assert\DateTime()
     * @ORM\Column(name="creationDate", type="datetime")
     */
    private $creationDate;
    /**
     * @var float $importe
     *
     * @ORM\Column(name="importe", type="float")
     */
    private $importe;

    /**
     * @var string $suma
     *
     * @ORM\Column(name="suma", type="string")
     */
    private $suma;

    /**
     * @var string $concepto
     *
     * @ORM\Column(name="concepto", type="string")
     */
    private $concepto;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\PaymentMethod", inversedBy="receipes")
     * @ORM\JoinColumn(name="paymentMethod", referencedColumnName="id")
     */
    private $paymentMethod;

    /**
     * @var string $sisgerCode
     *
     * @ORM\Column(name="sisgerCode", type="string", nullable=true)
     */
    private $sisgerCode;



    /**
     * @var boolean
     *
     * @ORM\Column(name="activo", type="boolean", nullable=true)
     */
    private $activo;

    /**
     * @ORM\OneToOne(targetEntity="Belraysa\BackendBundle\Entity\BankingEntry", inversedBy="receipe")
     * @ORM\JoinColumn(name="entry", referencedColumnName="id")
     **/
    private $entry;

    /**
     * @var string $type
     *
     * @ORM\Column(name="type", type="string", nullable=true)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Reply", inversedBy="receipes")
     * @ORM\JoinColumn(name="reply", referencedColumnName="id")
     */
    private $reply;

    /**
     * @var string $recibide
     *
     * @ORM\Column(name="recibide", type="string")
     */
    private $recibide;

    /**
     * @var string $refExpediente
     *
     * @ORM\Column(name="refExpediente", type="string", nullable=true)
     */
    private $refExpediente;

    /**
     * @ORM\ManyToMany(targetEntity="Belraysa\BackendBundle\Entity\Receipe", mappedBy="ingresosRelacionados")
     **/
    private $costos;

    /**
     * @ORM\ManyToMany(targetEntity="Belraysa\BackendBundle\Entity\Receipe", inversedBy="costos")
     * @ORM\JoinTable(name="ingresos_relacionados",
     *      joinColumns={@ORM\JoinColumn(name="ingreso_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="costo_id", referencedColumnName="id")}
     *      )
     **/
    private $ingresosRelacionados;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Workspace", inversedBy="receipes")
     * @ORM\JoinColumn(name="workspace", referencedColumnName="id")
     */
    private $workspace;

    /**
     * @return boolean
     */
    public function isActivo()
    {
        return $this->activo;
    }

    /**
     * @param boolean $activo
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;
    }

    /**
     * @return string
     */
    public function getConcepto()
    {
        return $this->concepto;
    }

    /**
     * @param string $concepto
     */
    public function setConcepto($concepto)
    {
        $this->concepto = $concepto;
    }

    /**
     * @return mixed
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param mixed $creationDate
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    /**
     * @return mixed
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * @param mixed $entry
     */
    public function setEntry($entry)
    {
        $this->entry = $entry;
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
     * @return float
     */
    public function getImporte()
    {
        return $this->importe;
    }

    /**
     * @param float $importe
     */
    public function setImporte($importe)
    {
        $this->importe = $importe;
    }

    /**
     * @return mixed
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param mixed $paymentMethod
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @return string
     */
    public function getSisgerCode()
    {
        return $this->sisgerCode;
    }

    /**
     * @param string $sisgerCode
     */
    public function setSisgerCode($sisgerCode)
    {
        $this->sisgerCode = $sisgerCode;
    }

    /**
     * @return string
     */
    public function getSuma()
    {
        return $this->suma;
    }

    /**
     * @param string $suma
     */
    public function setSuma($suma)
    {
        $this->suma = $suma;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }



    /**
     * @return mixed
     */
    public function getReply()
    {
        return $this->reply;
    }

    /**
     * @param mixed $reply
     */
    public function setReply($reply)
    {
        $this->reply = $reply;
    }

    /**
     * @return string
     */
    public function getRecibide()
    {
        return $this->recibide;
    }

    /**
     * @param string $recibide
     */
    public function setRecibide($recibide)
    {
        $this->recibide = $recibide;
    }

    /**
     * @return string
     */
    public function getRefExpediente()
    {
        return $this->refExpediente;
    }

    /**
     * @param string $refExpediente
     */
    public function setRefExpediente($refExpediente)
    {
        $this->refExpediente = $refExpediente;
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
    public function getIngresosRelacionados()
    {
        return $this->ingresosRelacionados;
    }

    /**
     * @param mixed $ingresosRelacionados
     */
    public function setIngresosRelacionados($ingresosRelacionados)
    {
        $this->ingresosRelacionados = $ingresosRelacionados;
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
        return $this->getSisgerCode();
    }
}