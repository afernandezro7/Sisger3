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
 * Belraysa\BackendBundle\Entity\Service
 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\ServiceRepository")
 * @ORM\Table()
 */
class Service
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
     * @ORM\ManyToMany(targetEntity="Belraysa\BackendBundle\Entity\Reply", mappedBy="services")
     */
    private $replies;

    /**
     * @ORM\ManyToMany(targetEntity="Belraysa\BackendBundle\Entity\Ingreso", mappedBy="servicios")
     */
    private $ingresos;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Workspace", inversedBy="services")
     * @ORM\JoinColumn(name="workspace", referencedColumnName="id")
     */
    private $workspace;

    function __construct()
    {
        $this->ingresos = new ArrayCollection();
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
    public function getReplies()
    {
        return $this->replies;
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
     * @param mixed $replies
     */
    public function setReplies($replies)
    {
        $this->replies = $replies;
    }
    public function __toString()
    {
        return $this->getName();
    }

    public function addRecibo(Ingreso $recibo)
    {
        if (!$this->hasRecibo($recibo)) {
            $recibo->addServicio($this);
            $this->ingresos->add($recibo);
        }
    }

    public function removeRecibo(Ingreso $recibo)
    {
        if ($this->hasRecibo($recibo)) {
            $this->ingresos->removeElement($recibo);
            $recibo->removeServicio($this);
        }
    }

    public function hasRecibo(Ingreso $recibo)
    {
        return $this->ingresos->contains($recibo);
    }
}