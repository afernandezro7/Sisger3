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
 * Belraysa\BackendBundle\Entity\Voucher
 * @ORM\Entity
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\VoucherRepository")
 */
class Voucher
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
     * @ORM\Column(name="creationDate", type="datetime")
     */
    private $creationDate;
    /**
     * @var integer $pax
     *
     * @ORM\Column(name="pax", type="integer")
     */
    private $pax;

    /**
     * @var string $provider
     *
     * @ORM\Column(name="provider", type="string")
     */
    private $provider;

    /**
     * @var string $refproviders
     *
     * @ORM\Column(name="refproviders", type="string", nullable=true, length=999)
     */
    private $refproviders;
    /**
     * @var string $services
     *
     * @ORM\Column(name="services", type="string", nullable=true, length=9999999999999999999999999999999999999999999999)
     */
    private $services;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Client", inversedBy="vouchers")
     * @ORM\JoinColumn(name="client", referencedColumnName="id")
     */
    private $client;
    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Reply", inversedBy="vouchers")
     * @ORM\JoinColumn(name="reply", referencedColumnName="id")
     */
    private $reply;
    /**
     * @var string $sisgerCode
     *
     * @ORM\Column(name="sisgerCode", type="string", nullable=true)
     */
    private $sisgerCode;


    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\User", inversedBy="vouchers")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     */
    private $user;
    
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
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Workspace", inversedBy="vouchers")
     * @ORM\JoinColumn(name="workspace", referencedColumnName="id")
     */
    private $workspace;

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
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $client
     */
    public function setClient($client)
    {
        $this->client = $client;
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
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return mixed
     */
    public function getReply()
    {
        return $this->reply;
    }

    /**
     * @param mixed $reply
     */
    public function setReply($reply)
    {
        $this->reply = $reply;
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
     * @return string
     */
    public function getRefproviders()
    {
        return $this->refproviders;
    }

    /**
     * @param string $refproviders
     */
    public function setRefproviders($refproviders)
    {
        $this->refproviders = $refproviders;
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
    public function isFirmado()
    {
        return $this->firmado;
    }

    /**
     * @param mixed $firmado
     */
    public function setFirmado($firmado)
    {
        $this->firmado = $firmado;
    }


}