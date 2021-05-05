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

 * Belraysa\BackendBundle\Entity\Banking

 * @ORM\InheritanceType("JOINED")

 * @ORM\DiscriminatorColumn(name="discr", type="string")

 * @ORM\DiscriminatorMap({"banking" = "Banking", "cash" = "Cash", "card" = "Card", "bank" = "Bank" })

 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\BankingRepository")

 */

class Banking

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

     * @ORM\Column(name="name", type="string", nullable=false)

     */

    private $name;



    /**

     * @var boolean $enabled

     *

     * @ORM\Column(name="enabled", type="boolean", nullable=false)

     */

    private $enabled;



    /**

     * @var datetime $createdAt

     *

     * @ORM\Column(name="createdAt", type="datetime", nullable=false)

     */

    private $createdAt;



    /**

     * @var string $type

     *

     * @ORM\Column(name="type", type="string", nullable=false)

     */

    private $type;



    /**

     * @var float $balance

     *

     * @ORM\Column(name="balance", type="float", nullable=false)

     */

    private $balance;



    /**

     * @var float $initBalance

     *

     * @ORM\Column(name="initBalance", type="float", nullable=false)

     */

    private $initBalance;



    /**

     * @ORM\ManyToMany(targetEntity="Belraysa\BackendBundle\Entity\Workspace", inversedBy="bankings")

     * @ORM\JoinTable(name="workspace_banking")

     */

    private $workspaces;



    /**

     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\BankingEntry", mappedBy="banking",cascade={"all"})
     * @ORM\OrderBy({"date" = "DESC"})

     */

    private $entries;



    /**

     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Traspaso", mappedBy="origen",cascade={"all"})

     */

    private $origenTraspasos;



    /**

     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Traspaso", mappedBy="destino",cascade={"all"})

     */

    private $destinoTraspasos;



    /**

     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Extra", mappedBy="banking",cascade={"all"})

     */

    private $extras;



    /**

     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Ajuste", mappedBy="banking",cascade={"all"})

     */

    private $ajustes;



    /**

     * @ORM\ManyToMany(targetEntity="Belraysa\BackendBundle\Entity\PaymentMethod", inversedBy="bankings")

     * @ORM\JoinTable(name="metodopago_banking")

     */

    private $metodosPago;



    function __construct()

    {

        $this->entries = new ArrayCollection();

    }



    /**

     * @return datetime

     */

    public function getCreatedAt()

    {

        return $this->createdAt;

    }



    /**

     * @param datetime $createdAt

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

     * @return mixed

     */

    public function getEntries()

    {

        // for ($i = sizeof($this->entries) - 1; $i >= 0; $i--) {

        //     $swapped = false;

        //     for ($j = 0; $j < $i; $j++) {

        //         if ($this->entries[$j]->getDate() > $this->entries[$j + 1]->getDate()) {

        //             $tmp = $this->entries[$j];

        //             $this->entries[$j] = $this->entries[$j + 1];

        //             $this->entries[$j + 1] = $tmp;

        //             $swapped = true;

        //         }

        //     }

        //     if (!$swapped) return $this->entries;

        // }

        return $this->entries;

    }

    // /**

    //  * @return mixed

    //  */

    // public function getEntriesLast()

    // {
    //     $tmp= array();
    //     for ($i = sizeof($this->entries) - 1; $i >= sizeof($this->entries) - 10; $i--) {
    //         $tmp[] = $this->entries[$i];
    //     }


    //     return $tmp;
    // }

    /**

     * @param mixed $entries

     */

    public function setEntries($entries)

    {

        $this->entries = $entries;

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

    public function getName()

    {

        return $this->name;

    }



    /**

     * @param string $name

     */

    public function setName($name)

    {

        $this->name = $name;

    }



    /**

     * @return string

     */

    public function getType()

    {

        return $this->type;

    }



    /**

     * @param string $type

     */

    public function setType($type)

    {

        $this->type = $type;

    }



    /**

     * @return mixed

     */

    public function getWorkspaces()

    {

        return $this->workspaces;

    }



    /**

     * @param mixed $workspaces

     */

    public function setWorkspaces($workspaces)

    {

        $this->workspaces = $workspaces;

    }







    function __toString()

    {

        return $this->getName();

    }



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

    public function getInitBalance()

    {

        return $this->initBalance;

    }



    /**

     * @param float $initBalance

     */

    public function setInitBalance($initBalance)

    {

        $this->initBalance = $initBalance;

    }



    /**

     * @return mixed

     */

    public function getDestinoTraspasos()

    {

        return $this->destinoTraspasos;

    }



    /**

     * @param mixed $destinoTraspasos

     */

    public function setDestinoTraspasos($destinoTraspasos)

    {

        $this->destinoTraspasos = $destinoTraspasos;

    }



    /**

     * @return mixed

     */

    public function getOrigenTraspasos()

    {

        return $this->origenTraspasos;

    }



    /**

     * @param mixed $origenTraspasos

     */

    public function setOrigenTraspasos($origenTraspasos)

    {

        $this->origenTraspasos = $origenTraspasos;

    }



    /**

     * @return mixed

     */

    public function getExtras()

    {

        return $this->extras;

    }



    /**

     * @param mixed $extras

     */

    public function setExtras($extras)

    {

        $this->extras = $extras;

    }



    /**

     * @return mixed

     */

    public function getMetodosPago()

    {

        return $this->metodosPago;

    }



    /**

     * @param mixed $metodosPago

     */

    public function setMetodosPago($metodosPago)

    {

        $this->metodosPago = $metodosPago;

    }



    public function addEntry(BankingEntry $entry)

    {

        $entry->setBanking($this);

        if (!$this->hasEntry($entry)) {

            $this->entries->add($entry);

        }

    }



    public function removeEntry(BankingEntry $entry)

    {

        if ($this->hasEntry($entry)) {

            $this->entries->removeElement($entry);

            $entry->setBanking(null);

        }

    }



    public function hasEntry(BankingEntry $entry)

    {

        return $this->entries->contains($entry);

    }



    public function ultimaOperacion()

    {

        return $this->entries[sizeof($this->entries) - 1];

    }



    public function getImportes()

    {

        $importes = array();



        foreach ($this->entries as $entry) {



            $importes[] = $entry->getValue();

        }





        return $importes;

    }



    /**

     * @return mixed

     */

    public function getAjustes()

    {

        return $this->ajustes;

    }



    /**

     * @param mixed $ajustes

     */

    public function setAjustes($ajustes)

    {

        $this->ajustes = $ajustes;

    }





}