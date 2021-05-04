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
 * Belraysa\BackendBundle\Entity\Workspace
 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\WorkspaceRepository")
 * @ORM\Table()
 */
class Workspace
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string $fullname
     *
     * @ORM\Column(name="fullname", type="string")
     */
    private $fullname;

    /**
     * @var boolean $enabled
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private $enabled;

    /**
     * @Assert\DateTime()
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\User", mappedBy="workspace",cascade={"all"})
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Service", mappedBy="workspace",cascade={"all"})
     */
    private $services;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\ConceptoGasto", mappedBy="workspace",cascade={"all"})
     */
    private $conceptosGasto;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\ConceptoCosto", mappedBy="workspace",cascade={"all"})
     */
    private $conceptosCosto;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Inventario", mappedBy="workspace",cascade={"all"})
     */
    private $inventarios;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Reply", mappedBy="workspace",cascade={"all"})
     */
    private $replies;

    /**
     * @var string $logo
     *
     * @ORM\Column(name="logo", type="string", nullable=false)
     */
    private $logo;

    /**
     * @ORM\ManyToMany(targetEntity="Belraysa\BackendBundle\Entity\Banking", mappedBy="workspaces")
     */
    private $bankings;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Receipe", mappedBy="workspace",cascade={"all"})
     */
    private $receipes;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Recibo", mappedBy="workspace",cascade={"all"})
     */
    private $recibos;



    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Voucher", mappedBy="workspace",cascade={"all"})
     */
    private $vouchers;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Nomenclador", mappedBy="workspace",cascade={"all"})
     */
    private $nomencladores;

    function __construct()
    {
        $this->users = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->replies = new ArrayCollection();
        $this->receipes = new ArrayCollection();
        $this->recibos = new ArrayCollection();
        $this->vouchers = new ArrayCollection();
        $this->bankings = new ArrayCollection();
        $this->nomencladores = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getConceptosCosto()
    {
        return $this->conceptosCosto;
    }

    /**
     * @param mixed $conceptosCosto
     */
    public function setConceptosCosto($conceptosCosto)
    {
        $this->conceptosCosto = $conceptosCosto;
    }

    /**
     * @return mixed
     */
    public function getConceptosGasto()
    {
        return $this->conceptosGasto;
    }

    /**
     * @param mixed $conceptosGasto
     */
    public function setConceptosGasto($conceptosGasto)
    {
        $this->conceptosGasto = $conceptosGasto;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param mixed $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
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
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param string $logo
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
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
    public function getBankings()
    {
        return $this->bankings;
    }

    /**
     * @param mixed $bankings
     */
    public function setBankings($bankings)
    {
        $this->bankings = $bankings;
    }


    public function __toString()
    {
        return $this->getName();
    }

    public function addReply(Reply $reply)
    {
        if (!$this->hasReply($reply)) {
            $reply->setWorkspace($this);
            $this->replies->add($reply);
        }
    }

    public function removeReply(Reply $reply)
    {
        if ($this->hasReply($reply)) {
            $this->replies->removeElement($reply);
            $reply->setWorkspace(null);
        }
    }

    public function hasReply(Reply $reply)
    {
        return $this->replies->contains($reply);
    }

    public function addUser(User $user)
    {
        if (!$this->hasUser($user)) {
            $user->setWorkspace($this);
            $this->users->add($user);
        }
    }

    public function removeUser(User $user)
    {
        if ($this->hasUser($user)) {
            $this->users->removeElement($user);
            $user->setWorkspace(null);
        }
    }

    public function hasUser(User $user)
    {
        return $this->users->contains($user);
    }


    public function addReceipe(Receipe $receipe)
    {
        if (!$this->hasReceipe($receipe)) {
            $receipe->setWorkspace($this);
            $this->receipes->add($receipe);
        }
    }

    public function removeReceipe(Receipe $receipe)
    {
        if ($this->hasReceipe($receipe)) {
            $this->receipes->removeElement($receipe);
            $receipe->setWorkspace(null);
        }
    }

    public function hasReceipe(Receipe $receipe)
    {
        return $this->receipes->contains($receipe);
    }

    public function addVoucher(Voucher $voucher)
    {
        if (!$this->hasVoucher($voucher)) {
            $voucher->setWorkspace($this);
            $this->vouchers->add($voucher);
        }
    }

    public function removeVoucher(Voucher $voucher)
    {
        if ($this->hasVoucher($voucher)) {
            $this->vouchers->removeElement($voucher);
            $voucher->setWorkspace(null);
        }
    }

    public function hasVoucher(Voucher $voucher)
    {
        return $this->vouchers->contains($voucher);
    }

    public function addBanking(Banking $banking)
    {
        $this->bankings->add($banking);
        $banking->setWorkspace($this);
    }

    public function removeBanking(Banking $banking)
    {
        $this->bankings->removeElement($banking);
        $banking->setWorkspace(null);
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


    public function addService(Service $service)
    {
        if (!$this->hasService($service)) {
            $service->setWorkspace($this);
            $this->services->add($service);
        }
    }

    public function removeService(Service $service)
    {
        if ($this->hasService($service)) {
            $this->services->removeElement($service);
            $service->setWorkspace(null);
        }
    }

    public function hasService(Service $service)
    {
        return $this->services->contains($service);
    }

    public function uploadLogo($route)
    {
        if (null === $this->logo) {
            $nameFile = 'belraysa.png';
            $this->setLogo($nameFile);

        } else {
            $nameFile = uniqid('ws-logo') . '-foto1.jpg';

            $this->logo->move($route, $nameFile);

            $this->setLogo($nameFile);
        }
    }

    public function addRecibo(Recibo $recibo)
    {
        if (!$this->hasRecibo($recibo)) {
            $recibo->setWorkspace($this);
            $this->recibos->add($recibo);
        }
    }

    public function removeRecibo(Recibo $recibo)
    {
        if ($this->hasRecibo($recibo)) {
            $this->recibos->removeElement($recibo);
            $recibo->setWorkspace(null);
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
    public function getInventarios()
    {
        return $this->inventarios;
    }

    /**
     * @param mixed $inventarios
     */
    public function setInventarios($inventarios)
    {
        $this->inventarios = $inventarios;
    }

    /**
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * @param string $fullname
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
    }



    /**
     * @return mixed
     */
    public function getNomencladores()
    {
        return $this->nomencladores;
    }

    /**
     * @param mixed $nomencladores
     */
    public function setNomencladores($nomencladores)
    {
        $this->nomencladores = $nomencladores;
    }

}