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
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Belraysa\BackendBundle\Entity\Client
 * @ORM\Entity
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\ClientRepository")
 */
class Client
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
     * @var string $firstName
     *
     * @ORM\Column(name="firstName", type="string", nullable=false)
     */
    private $firstName;
    /**
     * @var string $lastName
     *
     * @ORM\Column(name="lastName", type="string", nullable=false)
     */
    private $lastName;

    /**
     * @var string $dni
     *
     * @ORM\Column(name="dni", type="string", nullable=true)
     */
    private $dni;

    /**
     * @var string $passport
     *
     * @ORM\Column(name="passport", type="string", nullable=true)
     */
    private $passport;

    /**
     * @var string $address
     *
     * @ORM\Column(name="address", type="string", nullable=true)
     */
    private $address;

    /**
     * @var string $municipality
     *
     * @ORM\Column(name="municipality", type="string", nullable=true)
     */
    private $municipality;

    /**
     * @var string $province
     *
     * @ORM\Column(name="province", type="string", nullable=true)
     */
    private $province;

    /**
     * @var string $country
     *
     * @ORM\Column(name="country", type="string", nullable=true)
     */
    private $country;

    /**
     * @ORM\ManyToMany(targetEntity="Belraysa\BackendBundle\Entity\Reply", mappedBy="customers")
     */
    private $replies;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\PrecioVenta", mappedBy="cliente",cascade={"remove"})
     */
    private $preciosVenta;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Reply", mappedBy="leader",cascade={"remove"})
     */
    private $repliesLeader;
    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Voucher", mappedBy="client",cascade={"remove"})
     */
    private $vouchers;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Ingreso", mappedBy="cliente",cascade={"remove"})
     */
    private $ingresos;

    /**
     * @Assert\DateTime()
     * @ORM\Column(name="createdAt", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var string $phones
     *
     * @ORM\Column(name="phones", type="string", nullable=true)
     */
    private $phones;

    /**
     * @var string $cell
     *
     * @ORM\Column(name="cell", type="string", nullable=true)
     */
    private $cell;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    private $email;
    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\User", inversedBy="customers")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     */
    private $user;


    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Document", mappedBy="client",cascade={"remove"})
     * @var Collection|Document[]
     */
    private $documents;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Concepto", mappedBy="remitente",cascade={"all"})
     */
    private $remitenteConceptos;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Concepto", mappedBy="consignado",cascade={"all"})
     */
    private $consignadoConceptos;


    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->documents = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getFirstName() . " " . $this->getLastName();
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }


    /**
     * @return mixed
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * @param mixed $phones
     */
    public function setPhones($phones)
    {
        $this->phones = $phones;
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
    public function getReplies()
    {
        return $this->replies;
    }

    /**
     * @param mixed $replies
     */
    public function setReplies($replies)
    {
        $this->replies = $replies;
    }

    /**
     * @return mixed
     */
    public function getRepliesLeader()
    {
        return $this->repliesLeader;
    }

    /**
     * @param mixed $repliesLeader
     */
    public function setRepliesLeader($repliesLeader)
    {
        $this->repliesLeader = $repliesLeader;
    }

    public function addReply(Reply $reply)
    {
        if (!$this->hasReply($reply)) {
            $reply->addClient($this);
            $this->replies->add($reply);
        }
    }

    public function removeReply(Reply $reply)
    {
        if ($this->hasReply($reply)) {
            $this->replies->removeElement($reply);
            $reply->removeClient($this);
        }
    }

    public function hasReply(Reply $reply)
    {
        return $this->replies->contains($reply);
    }


    public function hasReplyLeader(Reply $reply)
    {
        return $this->repliesLeader->contains($reply);
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
     * @return mixed
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @param mixed $documents
     */
    public function setDocuments($documents)
    {
        $this->documents = $documents;
    }

    public function addDocument(Document $document)
    {
        if (!$this->hasDocument($document)) {
            $document->setClient($this);
            $this->documents->add($document);
        }
    }

    public function removeDocument(Document $document)
    {
        if ($this->hasDocument($document)) {
            $this->documents->removeElement($document);
            $document->setClient(null);
        }
    }

    public function hasDocument(Document $document)
    {
        return $this->documents->contains($document);
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getCell()
    {
        return $this->cell;
    }

    /**
     * @param string $cell
     */
    public function setCell($cell)
    {
        $this->cell = $cell;
    }

    /**
     * @return string
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * @param string $dni
     */
    public function setDni($dni)
    {
        $this->dni = $dni;
    }

    /**
     * @return string
     */
    public function getMunicipality()
    {
        return $this->municipality;
    }

    /**
     * @param string $municipality
     */
    public function setMunicipality($municipality)
    {
        $this->municipality = $municipality;
    }

    /**
     * @return string
     */
    public function getPassport()
    {
        return $this->passport;
    }

    /**
     * @param string $passport
     */
    public function setPassport($passport)
    {
        $this->passport = $passport;
    }

    /**
     * @return string
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * @param string $province
     */
    public function setProvince($province)
    {
        $this->province = $province;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getConsignadoConceptos()
    {
        return $this->consignadoConceptos;
    }

    /**
     * @param mixed $consignadoConceptos
     */
    public function setConsignadoConceptos($consignadoConceptos)
    {
        $this->consignadoConceptos = $consignadoConceptos;
    }

    /**
     * @return mixed
     */
    public function getRemitenteConceptos()
    {
        return $this->remitenteConceptos;
    }

    /**
     * @param mixed $remitenteConceptos
     */
    public function setRemitenteConceptos($remitenteConceptos)
    {
        $this->remitenteConceptos = $remitenteConceptos;
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
    public function getIngresos()
    {
        return $this->ingresos;
    }

    /**
     * @param mixed $ingresos
     */
    public function setIngresos($ingresos)
    {
        $this->ingresos = $ingresos;
    }


}