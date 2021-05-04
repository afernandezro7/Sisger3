<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 13:15
 */

namespace Belraysa\BackendBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * Belraysa\BackendBundle\Entity\Concepto
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"concepto" = "Concepto", "ena" = "ENA", "envio" = "Envio", "menaje" = "Menaje" })
 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\ConceptoRepository")
 */
class Concepto
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
     * @var datetime $fechaCreacion
     *
     * @ORM\Column(name="fechaCreacion", type="datetime", nullable=false)
     */
    private $fechaCreacion;

    /**
     * @var string $tipo
     *
     * @ORM\Column(name="tipo", type="string", nullable=false)
     */
    private $tipo;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="estado", type="string", nullable=false)
     */
    private $estado;

    /**
     * @var string $sisgerCode
     *
     * @ORM\Column(name="sisgerCode", type="string", nullable=true)
     */
    private $sisgerCode;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Bulto", mappedBy="concepto",cascade={"all"})
     */
    private $bultos;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Contenedor", inversedBy="conceptos")
     * @ORM\JoinColumn(name="contenedor", referencedColumnName="id")
     */
    private $contenedor;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Client", inversedBy="remitenteConceptos")
     * @ORM\JoinColumn(name="remitente", referencedColumnName="id")
     */
    private $remitente;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Client", inversedBy="consignadoConceptos")
     * @ORM\JoinColumn(name="consignado", referencedColumnName="id")
     */
    private $consignado;

    /**
     * @Assert\DateTime()
     * @ORM\Column(name="fechaHBL", type="datetime", nullable=true)
     */
    private $fechaHBL;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Reply", inversedBy="expedienteConceptos")
     * @ORM\JoinColumn(name="expediente", referencedColumnName="id")
     */
    private $expediente;

    function __construct()
    {
        $this->bultos = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getConsignado()
    {
        return $this->consignado;
    }

    /**
     * @param mixed $consignado
     */
    public function setConsignado($consignado)
    {
        $this->consignado = $consignado;
    }

    /**
     * @return datetime
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * @param datetime $fechaCreacion
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;
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
    public function getBultos()
    {
        return $this->bultos;
    }

    /**
     * @param mixed $bultos
     */
    public function setBultos($bultos)
    {
        $this->bultos = $bultos;
    }


    /**
     * @return mixed
     */
    public function getRemitente()
    {
        return $this->remitente;
    }

    /**
     * @param mixed $remitente
     */
    public function setRemitente($remitente)
    {
        $this->remitente = $remitente;
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

    public function addBulto(Bulto $bulto)
    {
        if (!$this->hasBulto($bulto)) {
            $bulto->setConcepto($this);
            $this->bultos->add($bulto);
        }
    }

    public function removeBulto(Bulto $bulto)
    {
        if ($this->hasBulto($bulto)) {
            $this->bultos->removeElement($bulto);
            $bulto->setConcepto(null);
        }
    }

    public function hasBulto(Bulto $bulto)
    {
        return $this->bultos->contains($bulto);
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
        $now = new \DateTime();
        $this->sisgerCode = $sisgerCode;
        foreach ($this->bultos as $bulto) {
            $bulto->setSisgerCode("BRS" . date_format($now, 'y'). str_pad($this->bultos->count(), 3, 0, STR_PAD_LEFT) . str_pad($this->id, 6, 0, STR_PAD_LEFT) . "-" . str_pad($bulto->getIndice(), 4, 0, STR_PAD_LEFT));
        }
    }

    public function getValorAduanal()
    {
        $valor = 0;
        foreach ($this->bultos as $bulto) {
            $valor += $bulto->getValorAduanal();
        }
        return $valor;
    }

    /**
     * @return mixed
     */
    public function getEnvio()
    {
        return $this->envio;
    }

    /**
     * @param mixed $envio
     */
    public function setEnvio($envio)
    {
        $this->envio = $envio;
    }


    public function getMercancias()
    {
        $mercancias = array();
        foreach ($this->bultos as $bulto) {
            foreach ($bulto->getMercancias() as $mercancia) {
                $mercancias[] = $mercancia;
            }
        }
        return $mercancias;
    }

    /**
     * @return mixed
     */
    public function getContenedor()
    {
        return $this->contenedor;
    }

    /**
     * @param mixed $contenedor
     */
    public function setContenedor($contenedor)
    {
        $this->contenedor = $contenedor;
    }

    /**
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param string $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function getPesoKg()
    {
        $valor = 0;
        foreach ($this->bultos as $bulto) {
            $valor += $bulto->getPesoKg();
        }
        return $valor;
    }

    /**
     * @return int
     */
    public function getVolumenM3()
    {
        $valor = 0;
        foreach ($this->bultos as $bulto) {
            $valor += $bulto->getVolumenM3();
        }
        return $valor;
    }

    function __toString()
    {
        return $this->sisgerCode;
    }

    /**
     * @return mixed
     */
    public function getFechaHBL()
    {
        return $this->fechaHBL;
    }

    /**
     * @param mixed $fechaHBL
     */
    public function setFechaHBL($fechaHBL)
    {
        $this->fechaHBL = $fechaHBL;
    }

    public function getExpediente()
    {
        return $this->expediente;
    }

    public function setExpediente($expediente)
    {
        $this->expediente = $expediente;
    }

}