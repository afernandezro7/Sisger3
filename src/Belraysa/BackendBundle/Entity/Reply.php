<?php
/**
 * Created by PhpStorm.
 * User: Rocio
 * Date: 07/10/2015
 * Time: 12:51
 */

namespace Belraysa\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;


/**
 * Belraysa\BackendBundle\Entity\Reply
 * @ORM\Entity
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\ReplyRepository")
 */
class Reply
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
     * @var integer $month
     *
     * @ORM\Column(name="month", type="integer")
     */
    private $month;
    /**
     * @var integer $year
     *
     * @ORM\Column(name="year", type="integer")
     */
    private $year;
    /**
     * @Assert\DateTime()
     * @ORM\Column(name="beginDate", type="datetime")
     */
    private $beginDate;
    /**
     * @Assert\DateTime()
     * @ORM\Column(name="finishDate", type="datetime")
     */
    private $finishDate;

    /**
     * @Assert\DateTime()
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;
    /**
     * @var integer $pax
     *
     * @ORM\Column(name="pax", type="integer")
     */
    private $pax;
    /**
     * @var string $observations
     *
     * @ORM\Column(name="observations", type="string", nullable=true)
     */
    private $observations;
    /**
     * @var integer $price
     *
     * @ORM\Column(name="price", type="integer", nullable=true)
     */
    private $price;

    /**
     * @ORM\ManyToMany(targetEntity="Belraysa\BackendBundle\Entity\Service", inversedBy="replies")
     * @ORM\JoinColumn(name="services", referencedColumnName="id")
     */
    private $services;

    /**
     * @ORM\ManyToMany(targetEntity="Belraysa\BackendBundle\Entity\Client", inversedBy="replies")
     * @ORM\JoinColumn(name="customers", referencedColumnName="id")
     * @var Collection|Client[]
     */
    private $customers;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\PrecioVenta", mappedBy="expediente",cascade={"all"})
     */
    private $preciosVenta;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\PrecioVenta", mappedBy="expedienteLider",cascade={"all"})
     */
    private $precioVentaLider;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Client", inversedBy="repliesLeader")
     * @ORM\JoinColumn(name="leader", referencedColumnName="id")
     */
    private $leader;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\User", inversedBy="replies")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Workspace", inversedBy="replies")
     * @ORM\JoinColumn(name="workspace", referencedColumnName="id")
     */
    private $workspace;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Voucher", mappedBy="reply",cascade={"all"})
     */
    private $vouchers;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Receipe", mappedBy="reply",cascade={"all"})
     */
    private $receipes;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Recibo", mappedBy="expediente",cascade={"all"})
     */
    private $recibos;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Concepto", mappedBy="expediente",cascade={"all"})
     */
    private $conceptos;


    /**
     * @var string sisgerCode
     *
     * @ORM\Column(name="sisgerCode", type="string", nullable=true)
     */
    private $sisgerCode;

    public function __construct()
    {
        $this->customers = new ArrayCollection();
        $this->preciosVenta = new ArrayCollection();
        $this->precioVentaLider = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getIngresos()
    {
        $ingresos = array();
        foreach ($this->recibos as $recibo) {
            if ($recibo->getTipo() == "Ingreso") {
                $ingresos[] = $recibo;
            }
        }
        return $ingresos;
    }

    /**
     * @param mixed $ingresos
     */
    public function setIngresos($ingresos)
    {
        $this->ingresos = $ingresos;
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
    public function getBeginDate()
    {
        return $this->beginDate;
    }

    /**
     * @param mixed $beginDate
     */
    public function setBeginDate($beginDate)
    {
        $this->beginDate = $beginDate;
    }

    /**
     * @return mixed
     */
    public function getFinishDate()
    {
        return $this->finishDate;
    }

    /**
     * @param mixed $finishDate
     */
    public function setFinishDate($finishDate)
    {
        $this->finishDate = $finishDate;
    }

    /**
     * @return int
     */
    public function getPax()
    {
        return $this->pax;
    }

    /**
     * @param int $pax
     */
    public function setPax($pax)
    {
        $this->pax = $pax;
    }

    /**
     * @return string
     */
    public function getObservations()
    {
        return $this->observations;
    }

    /**
     * @param string $observations
     */
    public function setObservations($observations)
    {
        $this->observations = $observations;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }


    /**
     * @return mixed
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param mixed $services
     */
    public function setServices($services)
    {
        $this->services = $services;
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


    /**
     * @return mixed
     */
    public function getAgencies()
    {
        return $this->agencies;
    }

    /**
     * @param mixed $agencies
     */
    public function setAgencies($agencies)
    {
        $this->agencies = $agencies;
    }

    /**
     * @return mixed
     */
    public function getVouchers()
    {
        return $this->vouchers;
    }

    /**
     * @param mixed $vouchers
     */
    public function setVouchers($vouchers)
    {
        $this->vouchers = $vouchers;
    }


    /**
     * @return mixed
     */
    public function getCustomers()
    {
        return $this->customers;
    }

    /**
     * @param mixed $customers
     */
    public function setCustomers($customers)
    {
        $this->customers = $customers;
    }

    public function __toString()
    {
        return '' . $this->sisgerCode;
    }

    /**
     * @return mixed
     */
    public function getLeader()
    {
        return $this->leader;
    }

    /**
     * @param mixed $leader
     */
    public function setLeader($leader)
    {
        $this->leader = $leader;
    }

    public function addClient(Client $client)
    {
        if (!$this->hasClient($client)) {
            $client->addReply($this);
            $this->customers->add($client);
        }
    }

    public function removeClient(Client $client)
    {
        if ($this->hasClient($client)) {
            $this->customers->removeElement($client);
            $client->removeReply($this);
        }
    }

    public function hasClient(Client $client)
    {
        return $this->customers->contains($client);
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
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @param int $month
     */
    public function setMonth($month)
    {
        $this->month = $month;
    }


    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param int $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    public function addRecibo(Recibo $recibo)
    {
        if (!$this->hasRecibo($recibo)) {
            $recibo->setExpediente($this);
            $this->recibos->add($recibo);
        }
    }

    public function removeRecibo(Recibo $recibo)
    {
        if ($this->hasRecibo($recibo)) {
            $this->recibos->removeElement($recibo);
            $recibo->setExpediente(null);
        }
    }

    public function hasRecibo(Recibo $recibo)
    {
        return $this->recibos->contains($recibo);
    }

    /**
     * @return mixed
     */
    public function getRecibos()
    {
        return $this->recibos;
    }

    public function getEgresos()
    {
        $egresos = array();
        foreach ($this->recibos as $recibo) {
            if ($recibo->getTipo() != "Ingreso") {
                $egresos[] = $recibo;
            }
        }
        return $egresos;
    }

    /**
     * @param mixed $recibos
     */
    public function setRecibos($recibos)
    {
        $this->recibos = $recibos;
    }

    /**
     * @return mixed
     */
    public function getConceptos()
    {
        return $this->conceptos;
    }

    /**
     * @param mixed $conceptos
     */
    public function setConceptos($conceptos)
    {
        $this->conceptos = $conceptos;
    }

    /**
     * @return mixed
     */
    public function getPreciosVenta()
    {
        return $this->preciosVenta;
    }

    /**
     * @param mixed $preciosVenta
     */
    public function setPreciosVenta($preciosVenta)
    {
        $this->preciosVenta = $preciosVenta;
    }

    /**
     * @return mixed
     */
    public function getPrecioVentaLider()
    {
        return $this->precioVentaLider;
    }

    /**
     * @param mixed $precioVentaLider
     */
    public function setPrecioVentaLider($precioVentaLider)
    {
        $this->precioVentaLider = $precioVentaLider;
    }

    public function addPrecioVenta(PrecioVenta $precioVenta)
    {
        if (!$this->hasPrecioVenta($precioVenta)) {
            $precioVenta->setExpediente($this);
            $this->preciosVenta->add($precioVenta);
        }
    }

    public function removePrecioVenta(PrecioVenta $precioVenta)
    {
        if ($this->hasPrecioVenta($precioVenta)) {
            $this->preciosVenta->removeElement($precioVenta);
            $precioVenta->setExpediente(null);
        }
    }

    public function hasPrecioVenta(PrecioVenta $precioVenta)
    {
        return $this->preciosVenta->contains($precioVenta);
    }

    public function addPrecioVentaLider(PrecioVenta $precioVenta)
    {
        if (!$this->hasPrecioVentaLider($precioVenta)) {
            $precioVenta->setExpedienteLider($this);
            $this->precioVentaLider->add($precioVenta);
        }
    }

    public function removePrecioVentaLider(PrecioVenta $precioVenta)
    {
        if ($this->hasPrecioVentaLider($precioVenta)) {
            $this->precioVentaLider->removeElement($precioVenta);
            $precioVenta->setExpedienteLider(null);
        }
    }

    public function hasPrecioVentaLider(PrecioVenta $precioVenta)
    {
        return $this->precioVentaLider->contains($precioVenta);
    }


    public function hasCliente($id)
    {
        foreach ($this->getPreciosVenta() as $precioVenta) {
            if ($precioVenta->getCliente()->getId() == $id) {
                return true;
            }
        }
        return false;
    }

    public function hasLider($id)
    {
        foreach ($this->getPrecioVentaLider() as $precioVenta) {
            if ($precioVenta->getCliente()->getId() == $id) {
                return true;
            }
        }
        return false;
    }
}