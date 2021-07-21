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

 * Belraysa\BackendBundle\Entity\Contenedor

 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\ContenedorRepository")

 * @ORM\Table()

 */

class Contenedor

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

     * @var string $codigo

     *

     * @ORM\Column(name="codigo", type="string", length=999999999, nullable=true)

     */

    private $codigo;



    /**

     * @var string $sello

     *

     * @ORM\Column(name="sello", type="string", length=999999999, nullable=true)

     */

    private $sello;





    /**

     * @var string $estado

     *

     * @ORM\Column(name="estado", type="string", length=999999999, nullable=false)

     */

    private $estado;



    /**

     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\TipoContenedor", inversedBy="contenedores")

     * @ORM\JoinColumn(name="tipo", referencedColumnName="id")

     */

    private $tipo;



    /**

     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Mes", inversedBy="contenedores")

     * @ORM\JoinColumn(name="mes", referencedColumnName="id")

     */

    private $mes;



    /**

     * @var float $volumenM3

     *

     * @ORM\Column(name="volumenM3", type="float", precision=2)

     */

    private $volumenM3;



    /**

     * @var float $maxPesoKg

     *

     * @ORM\Column(name="maxPesoKg", type="float", precision=2)

     */

    private $maxPesoKg;





    /**

     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Concepto", mappedBy="contenedor", cascade={"remove"} , fetch="EAGER")

     */

    private $conceptos;



    /**

     * @var string $motonave

     *

     * @ORM\Column(name="motonave", type="string", length=999999999, nullable=true)

     */

    private $motonave;



    /**

     * @var string $mbl

     *

     * @ORM\Column(name="mbl", type="string", length=999999999, nullable=true)

     */

    private $mbl;



    /**

     * @var string $puertoSalida

     *

     * @ORM\Column(name="puertoSalida", type="string", length=999999999, nullable=true)

     */

    private $puertoSalida;



    /**

     * @var string $puertoEntrada

     *

     * @ORM\Column(name="puertoEntrada", type="string", length=999999999, nullable=true)

     */

    private $puertoEntrada;





    /**

     * @Assert\DateTime()

     * @ORM\Column(name="fechaEntrada", type="datetime", nullable=true)

     */

    private $fechaEntrada;



    /**

     * @Assert\DateTime()

     * @ORM\Column(name="fechaSalida", type="datetime", nullable=true)

     */

    private $fechaSalida;



    /**

     * @var float $indice

     *

     * @ORM\Column(name="indice", type="integer", nullable=false)

     */

    private $indice;



    /**

     * @var float $indiceAnual

     *

     * @ORM\Column(name="indiceAnual", type="integer", nullable=false)

     */

    private $indiceAnual;



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

    public function getCodigo()

    {

        return $this->codigo;

    }



    /**

     * @param string $codigo

     */

    public function setCodigo($codigo)

    {

        $this->codigo = $codigo;

    }



    /**

     * @return mixed

     */

    public function getTipo()

    {

        return $this->tipo;

    }



    /**

     * @param mixed $tipo

     */

    public function setTipo($tipo)

    {

        $this->tipo = $tipo;

    }



    /**

     * @return mixed

     */

    public function getMes()

    {

        return $this->mes;

    }



    /**

     * @param mixed $mes

     */

    public function setMes($mes)

    {

        $this->mes = $mes;

    }





    /**

     * @return float

     */

    public function getMaxPesoKg()

    {

        return $this->maxPesoKg;

    }



    /**

     * @param float $maxPesoKg

     */

    public function setMaxPesoKg($maxPesoKg)

    {

        $this->maxPesoKg = $maxPesoKg;

    }



    /**

     * @return float

     */

    public function getVolumenM3()

    {

        return $this->volumenM3;

    }



    /**

     * @param float $volumenM3

     */

    public function setVolumenM3($volumenM3)

    {

        $this->volumenM3 = $volumenM3;

    }



    /**

     * @return string

     */

    public function getSello()

    {

        return $this->sello;

    }



    /**

     * @param string $sello

     */

    public function setSello($sello)

    {

        $this->sello = $sello;

    }



    /**

     * @return mixed

     */

    public function getConceptos()

    {

        return $this->conceptos;

    }
    /**

     * @return mixed

     */

    public function getsizeOfConceptos()

    {

        return sizeof($this->conceptos);

    }

    /**

     * @return int

    */
    public function getConceptosPages($lenght=100)
    {
        $totalConceptos = sizeof($this->getConceptos());

       
       return ceil($totalConceptos/$lenght);
       
    }

    /**

     * @return mixed

     */
    public function getRangeConceptos($start=0 , $lenght=100)
    {
        $criteria = \Doctrine\Common\Collections\Criteria::create()->setFirstResult($start*$lenght)->setMaxResults($lenght);
        return $this->conceptos->matching($criteria);


    }



    /**

     * @param mixed $conceptos

     */

    public function setConceptos($conceptos)

    {

        $this->conceptos = $conceptos;

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



    public function getFecha()

    {

        return date_create_from_format('Y/m/d', $this->getMes()->getAnno()->getNombre() . '/' . $this->getMes()->getNumero() . '/01');

    }



    public function addConcepto(Concepto $concepto)

    {

        if (!$this->hasConcepto($concepto)) {

            $concepto->setContenedor($this);

            $this->conceptos->add($concepto);

        }

    }



    public function removeConcepto(Concepto $concepto)

    {

        if ($this->hasConcepto($concepto)) {

            $this->conceptos->removeElement($concepto);

            $concepto->setContenedor(null);

        }

    }



    public function hasConcepto(Concepto $concepto)

    {

        return $this->conceptos->contains($concepto);

    }



    /**

     * @return mixed

     */

    public function getFechaEntrada()

    {

        return $this->fechaEntrada;

    }



    /**

     * @param mixed $fechaEntrada

     */

    public function setFechaEntrada($fechaEntrada)

    {

        $this->fechaEntrada = $fechaEntrada;

    }



    /**

     * @return mixed

     */

    public function getFechaSalida()

    {

        return $this->fechaSalida;

    }



    /**

     * @param mixed $fechaSalida

     */

    public function setFechaSalida($fechaSalida)

    {

        $this->fechaSalida = $fechaSalida;

    }



    /**

     * @return string

     */

    public function getMbl()

    {

        return $this->mbl;

    }



    /**

     * @param string $mbl

     */

    public function setMbl($mbl)

    {

        $this->mbl = $mbl;

    }



    /**

     * @return string

     */

    public function getMotonave()

    {

        return $this->motonave;

    }



    /**

     * @param string $motonave

     */

    public function setMotonave($motonave)

    {

        $this->motonave = $motonave;

    }



    /**

     * @return string

     */

    public function getPuertoEntrada()

    {

        return $this->puertoEntrada;

    }



    /**

     * @param string $puertoEntrada

     */

    public function setPuertoEntrada($puertoEntrada)

    {

        $this->puertoEntrada = $puertoEntrada;

    }



    /**

     * @return string

     */

    public function getPuertoSalida()

    {

        return $this->puertoSalida;

    }



    /**

     * @param string $puertoSalida

     */

    public function setPuertoSalida($puertoSalida)

    {

        $this->puertoSalida = $puertoSalida;

    }



    public function getPesoKg()

    {

        $valor = 0;

        foreach ($this->conceptos as $concepto) {

            $valor += $concepto->getPesoKg();

        }

        return $valor;

    }



    /**

     * @return int

     */

    public function getVolumenConceptosM3()

    {

        $valor = 0;

        foreach ($this->conceptos as $concepto) {

            $valor += $concepto->getVolumenM3();

        }

        return $valor;

    }



    function __toString()

    {

        return "Contenedor # " . $this->indiceAnual ." / " . $this->getMes()->getNombre() . " - " . $this->getMes()->getAnno()->getNombre();

    }



    /**

     * @return float

     */

    public function getIndice()

    {

        return $this->indice;

    }



    /**

     * @param float $indice

     */

    public function setIndice($indice)

    {

        $this->indice = $indice;

    }



    /**

     * @return float

     */

    public function getIndiceAnual()

    {

        return $this->indiceAnual;

    }



    /**

     * @param float $indiceAnual

     */

    public function setIndiceAnual($indiceAnual)

    {

        $this->indiceAnual = $indiceAnual;

    }





}