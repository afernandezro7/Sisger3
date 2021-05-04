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
 * Belraysa\BackendBundle\Entity\Ingreso
 * @ORM\Entity()
 * @ORM\Table()
 */
class Ingreso extends Recibo
{

    /**
     * @ORM\ManyToMany(targetEntity="Belraysa\BackendBundle\Entity\Service")
     * @ORM\JoinTable(name="ingreso_service",
     * joinColumns={@ORM\JoinColumn(name="ingreso_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="service_id", referencedColumnName="id")})
     */
    private $servicios;


    /**
     * @var string $recibiDe
     *
     * @ORM\Column(name="recibiDe", type="string")
     */
    private $recibiDe;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Client", inversedBy="ingresos")
     * @ORM\JoinColumn(name="cliente", referencedColumnName="id")
     */
    private $cliente;

    /**
     * @var string $detalles
     *
     * @ORM\Column(name="detalles", type="string", length=99999, nullable=true)
     */
    private $detalles;

    /**
     * @var float $saldoAnterior
     *
     * @ORM\Column(name="saldoAnterior", type="float", nullable=true)
     */
    private $saldoAnterior;
    /**
     * @var float $abono
     *
     * @ORM\Column(name="abono", type="float", nullable=true)
     */
    private $abono;
    /**
     * @var float $saldoPendiente
     *
     * @ORM\Column(name="saldoPendiente", type="float", nullable=true)
     */
    private $saldoPendiente;

    /**
     * @ORM\OneToOne(targetEntity="Belraysa\BackendBundle\Entity\IdIngreso", inversedBy="ingreso")
     * @ORM\JoinColumn(name="idIngreso", referencedColumnName="id")
     **/
    private $idIngreso;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cuentaXCobrar", type="boolean", nullable=false)
     */
    private $cuentaXCobrar;

    /**
     * @Assert\DateTime()
     * @ORM\Column(name="fechaLimite", type="datetime", nullable=true)
     */
    private $fechaLimite;

    /**
     * Ingreso constructor.
     * @param $servicios
     */
    public function __construct()
    {
        $this->servicios = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getIdIngreso()
    {
        return $this->idIngreso;
    }

    /**
     * @param mixed $idIngreso
     */
    public function setIdIngreso($idIngreso)
    {
        $this->idIngreso = $idIngreso;
    }

    /**
     * @return float
     */
    public function getSaldoAnterior()
    {
        return $this->saldoAnterior;
    }

    /**
     * @param float $saldoAnterior
     */
    public function setSaldoAnterior($saldoAnterior)
    {
        $this->saldoAnterior = $saldoAnterior;
    }

    /**
     * @return float
     */
    public function getAbono()
    {
        return $this->abono;
    }

    /**
     * @param float $abono
     */
    public function setAbono($abono)
    {
        $this->abono = $abono;
    }

    /**
     * @return float
     */
    public function getSaldoPendiente()
    {
        return $this->saldoPendiente;
    }

    /**
     * @param float $saldoPendiente
     */
    public function setSaldoPendiente($saldoPendiente)
    {
        $this->saldoPendiente = $saldoPendiente;
    }


    /**
     * @return string
     */
    public function getRecibiDe()
    {
        return $this->recibiDe;
    }

    /**
     * @param string $recibiDe
     */
    public function setRecibiDe($recibiDe)
    {
        $this->recibiDe = $recibiDe;
    }

    /**
     * @return mixed
     */
    public function getServicios()
    {
        return $this->servicios;
    }

    /**
     * @param mixed $servicios
     */
    public function setServicios($servicios)
    {
        $this->servicios = $servicios;
    }

    /**
     * @return string
     */
    public function getDetalles()
    {
        return $this->detalles;
    }

    /**
     * @param string $detalles
     */
    public function setDetalles($detalles)
    {
        $this->detalles = $detalles;
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

    public function getCuentaXCobrar()
    {
        return $this->cuentaXCobrar;
    }

    /**
     * @param boolean $cuentaXCobrar
     */
    public function setCuentaXCobrar($cuentaXCobrar)
    {
        $this->cuentaXCobrar = $cuentaXCobrar;
    }

    /**
     * @return mixed
     */
    public function getFechaLimite()
    {
        return $this->fechaLimite;
    }

    /**
     * @param mixed $fecha
     */
    public function setFechaLimite($fechaLimite)
    {
        $this->fechaLimite = $fechaLimite;
    }

    public function addServicio(Service $servicio)
    {
        if (!$this->hasServicio($servicio)) {
            $this->servicios->add($servicio);
        }
    }

    public function removeServicio(Service $servicio)
    {
        if ($this->hasServicio($servicio)) {
            $this->servicios->removeElement($servicio);
        }
    }

    public function hasServicio(Service $servicio)
    {
        return $this->servicios->contains($servicio);
    }
}