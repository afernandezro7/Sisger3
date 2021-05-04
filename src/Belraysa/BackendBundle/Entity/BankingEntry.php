<?php
/**
 * Created by PhpStorm.
 * User: Ro
 * Date: 07/10/2015
 * Time: 13:15
 */

namespace Belraysa\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * Belraysa\BackendBundle\Entity\BankingEntry
 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\BankingEntryRepository")
 */
class BankingEntry
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
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Banking", inversedBy="entries")
     * @ORM\JoinColumn(name="banking", referencedColumnName="id")
     */
    private $banking;

    /**
     * @ORM\OneToOne(targetEntity="Belraysa\BackendBundle\Entity\Receipe", mappedBy="entry")
     **/
    private $receipe;

    /**
     * @ORM\OneToOne(targetEntity="Belraysa\BackendBundle\Entity\Recibo", inversedBy="entrada")
     * @ORM\JoinColumn(name="recibo", referencedColumnName="id")
     **/
    private $recibo;

    /**
     * @ORM\OneToOne(targetEntity="Belraysa\BackendBundle\Entity\Extra", mappedBy="entrada")
     **/
    private $extra;

    /**
     * @ORM\OneToOne(targetEntity="Belraysa\BackendBundle\Entity\Ajuste", mappedBy="entrada")
     **/
    private $ajuste;

    /**
     * @ORM\OneToOne(targetEntity="Belraysa\BackendBundle\Entity\Traspaso", mappedBy="entryOrigen")
     **/
    private $origenTraspaso;


    /**
     * @ORM\OneToOne(targetEntity="Belraysa\BackendBundle\Entity\Traspaso", mappedBy="entryDestino")
     **/
    private $destinoTraspaso;

    /**
     * @var datetime $date
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var string $details
     *
     * @ORM\Column(name="details", type="string", length=99999999999, nullable=false)
     */
    private $details;

    /**
     * @var float $credit
     *
     * @ORM\Column(name="credit", type="float", nullable=false)
     */
    private $credit;

    /**
     * @var float $debit
     *
     * @ORM\Column(name="debit", type="float", nullable=false)
     */
    private $debit;

    /**
     * @var float $balance
     *
     * @ORM\Column(name="balance", type="float", nullable=false)
     */
    private $balance;

    /**
     * @var boolean
     *
     * @ORM\Column(name="activo", type="boolean", nullable=true)
     */
    private $activo;

    /**
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param float $balance
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

    /**
     * @return float
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * @param float $credit
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;
    }

    /**
     * @return datetime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param datetime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return float
     */
    public function getDebit()
    {
        return $this->debit;
    }

    /**
     * @param float $debit
     */
    public function setDebit($debit)
    {
        $this->debit = $debit;
    }

    /**
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param string $details
     */
    public function setDetails($details)
    {
        $this->details = $details;
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
    public function getReceipe()
    {
        return $this->receipe;
    }

    /**
     * @param mixed $receipe
     */
    public function setReceipe($receipe)
    {
        $this->receipe = $receipe;
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

    /**
     * @return mixed
     */
    public function getDestinoTraspaso()
    {
        return $this->destinoTraspaso;
    }

    /**
     * @param mixed $destinoTraspaso
     */
    public function setDestinoTraspaso($destinoTraspaso)
    {
        $this->destinoTraspaso = $destinoTraspaso;
    }

    /**
     * @return mixed
     */
    public function getOrigenTraspaso()
    {
        return $this->origenTraspaso;
    }

    /**
     * @param mixed $origenTraspaso
     */
    public function setOrigenTraspaso($origenTraspaso)
    {
        $this->origenTraspaso = $origenTraspaso;
    }

    /**
     * @return mixed
     */
    public function getRecibo()
    {
        return $this->recibo;
    }

    /**
     * @param mixed $recibo
     */
    public function setRecibo($recibo)
    {
        $this->recibo = $recibo;
    }

    /**
     * @return mixed
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param mixed $extra
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;
    }

    public function getValue()
    {
        if ($this->getCredit() > 0) {
            return $this->getCredit();
        } else {
            return $this->getDebit();
        }
    }

    /**
     * @return bool
     */
    public function isActivo()
    {
        return $this->activo;
    }

    /**
     * @param bool $activo
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;
    }

    /**
     * @return mixed
     */
    public function getAjuste()
    {
        return $this->ajuste;
    }

    /**
     * @param mixed $ajuste
     */
    public function setAjuste($ajuste)
    {
        $this->ajuste = $ajuste;
    }

}