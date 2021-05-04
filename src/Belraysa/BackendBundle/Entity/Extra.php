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
 * Belraysa\BackendBundle\Entity\Extra
 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\ExtraRepository")
 * @ORM\Table()
 */
class Extra
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
     * @var float $importe
     *
     * @ORM\Column(name="importe", type="float", precision=2)
     */
    private $importe;

    /**
     * @var string $concepto
     *
     * @ORM\Column(name="concepto", type="string")
     */
    private $concepto;

    /**
     * @var string $sisgerCode
     *
     * @ORM\Column(name="sisgerCode", type="string", nullable=true)
     */
    private $sisgerCode;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\User", inversedBy="extras")
     * @ORM\JoinColumn(name="usuario", referencedColumnName="id")
     */
    private $usuario;

    /**
     * @ORM\OneToOne(targetEntity="Belraysa\BackendBundle\Entity\BankingEntry", inversedBy="extra")
     * @ORM\JoinColumn(name="entrada", referencedColumnName="id")
     **/
    private $entrada;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Banking", inversedBy="extras")
     * @ORM\JoinColumn(name="banking", referencedColumnName="id")
     */
    private $banking;

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
    public function getBanking()
    {
        return $this->banking;
    }

    /**
     * @param mixed $banking
     */
    public function setBanking($banking)
    {
        $this->banking = $banking;
    }



    function __toString()
    {
        return $this->getSisgerCode();
    }
}