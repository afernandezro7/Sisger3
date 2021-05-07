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
 * Belraysa\BackendBundle\Entity\Recibo
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"recibo" = "Recibo", "ingreso" = "Ingreso", "gasto" = "Gasto", "costo" = "Costo", "costoRecurrente" = "CostoRecurrente" })
 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\ReciboRepository")
 */
class Recibo
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
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @Assert\DateTime()
     * @ORM\Column(name="creacion", type="datetime")
     */
    private $creacion;

    /**
     * @var float $importe
     *
     * @ORM\Column(name="importe", type="float", precision=2)
     */
    private $importe;

    /**
     * @var string $suma
     *
     * @ORM\Column(name="suma", type="string", nullable=true)
     */
    private $suma;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\PaymentMethod", inversedBy="recibos")
     * @ORM\JoinColumn(name="metodoPago", referencedColumnName="id")
     */
    private $metodoPago;

    /**
     * @var string $sisgerCode
     *
     * @ORM\Column(name="sisgerCode", type="string", nullable=true)
     */
    private $sisgerCode;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\User", inversedBy="recibos")
     * @ORM\JoinColumn(name="usuario", referencedColumnName="id")
     */
    private $usuario;

    /**
     * @var boolean
     *
     * @ORM\Column(name="activo", type="boolean", nullable=true)
     */
    private $activo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="firmado", type="boolean", nullable=true)
     */
    private $firmado;
    
    /**
     * @ORM\OneToOne(targetEntity="Belraysa\BackendBundle\Entity\BankingEntry", mappedBy="recibo",cascade={"all"})
     **/
    private $entrada;

    /**
     * @var string $tipo
     *
     * @ORM\Column(name="tipo", type="string", nullable=true)
     */
    private $tipo;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Workspace", inversedBy="recibos")
     * @ORM\JoinColumn(name="workspace", referencedColumnName="id")
     */
    private $workspace;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Reply", inversedBy="recibos")
     * @ORM\JoinColumn(name="expediente", referencedColumnName="id")
     */
    private $expediente;

    /**
     * @var string $refExpediente
     *
     * @ORM\Column(name="refExpediente", type="string", nullable=true)
     */
    private $refExpediente;

    /**
     * @var string $motivoCancelacion
     *
     * @ORM\Column(name="motivoCancelacion", type="string", nullable=true, length=99999999)
     */
    private $motivoCancelacion;

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
     * @return mixed
     */
    public function getEntrada()
    {
        return $this->entrada;
    }

    /**
     * @param mixed $entrada
     */
    public function setEntrada($entrada)
    {
        $this->entrada = $entrada;
    }

    /**
     * @return mixed
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param mixed $fecha
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
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
    public function getMetodoPago()
    {
        return $this->metodoPago;
    }

    /**
     * @param mixed $metodoPago
     */
    public function setMetodoPago($metodoPago)
    {
        $this->metodoPago = $metodoPago;
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
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param string $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * @return mixed
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * @param mixed $usuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
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
     * @return string
     */
    public function getMotivoCancelacion()
    {
        return $this->motivoCancelacion;
    }

    /**
     * @param string $motivoCancelacion
     */
    public function setMotivoCancelacion($motivoCancelacion)
    {
        $this->motivoCancelacion = $motivoCancelacion;
    }

    /**
     * @return mixed
     */
    public function getCreacion()
    {
        return $this->creacion;
    }

    /**
     * @param mixed $creacion
     */
    public function setCreacion($creacion)
    {
        $this->creacion = $creacion;
    }
    
      /**
     * @return mixed
     */
    public function isFirmado()
    {
        return $this->firmado;
    }

    /**
     * @param boolean $firmado
     */
    public function setFirmado($firmado)
    {
        $this->firmado = $firmado;
    }

    function __toString()
    {
        return $this->getSisgerCode();
    }
}