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
 * Belraysa\BackendBundle\Entity\Traspaso
 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\TraspasoRepository")
 * @ORM\Table()
 */
class Traspaso
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
     * @var string $sisgerCode
     *
     * @ORM\Column(name="sisgerCode", type="string", nullable=true)
     */
    private $sisgerCode;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\User", inversedBy="traspasos")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="Belraysa\BackendBundle\Entity\BankingEntry", inversedBy="origenTraspaso")
     * @ORM\JoinColumn(name="entryOrigen", referencedColumnName="id")
     **/
    private $entryOrigen;

    /**
     * @ORM\OneToOne(targetEntity="Belraysa\BackendBundle\Entity\BankingEntry", inversedBy="destinoTraspaso")
     * @ORM\JoinColumn(name="entryDestino", referencedColumnName="id")
     **/
    private $entryDestino;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Banking", inversedBy="origenTraspasos")
     * @ORM\JoinColumn(name="origen", referencedColumnName="id")
     */
    private $origen;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Banking", inversedBy="destinoTraspasos")
     * @ORM\JoinColumn(name="destino", referencedColumnName="id")
     */
    private $destino;

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
    public function getDestino()
    {
        return $this->destino;
    }

    /**
     * @param mixed $destino
     */
    public function setDestino($destino)
    {
        $this->destino = $destino;
    }

    /**
     * @return mixed
     */
    public function getEntryDestino()
    {
        return $this->entryDestino;
    }

    /**
     * @param mixed $entryDestino
     */
    public function setEntryDestino($entryDestino)
    {
        $this->entryDestino = $entryDestino;
    }

    /**
     * @return mixed
     */
    public function getEntryOrigen()
    {
        return $this->entryOrigen;
    }

    /**
     * @param mixed $entryOrigen
     */
    public function setEntryOrigen($entryOrigen)
    {
        $this->entryOrigen = $entryOrigen;
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
    public function getOrigen()
    {
        return $this->origen;
    }

    /**
     * @param mixed $origen
     */
    public function setOrigen($origen)
    {
        $this->origen = $origen;
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
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }



    function __toString()
    {
        return $this->getSisgerCode();
    }
}