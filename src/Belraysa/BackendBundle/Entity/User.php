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
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * Belraysa\BackendBundle\Entity\User
 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\UserRepository")
 * @DoctrineAssert\UniqueEntity("username")
 * @ORM\Table()
 */
class User implements AdvancedUserInterface, \Serializable
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
     * @var string $username
     * @Assert\NotBlank()
     * @ORM\Column(name="username", type="string", unique=true)
     */
    private $username;
    /**
     * @var string $firstName
     * @Assert\NotBlank()
     * @Assert\Regex(pattern="/^[a-zA-Z\s]*$/", message = "Por favor, especifica un nombre válido")
     * @ORM\Column(name="firstName", type="string", nullable=false)
     */
    private $firstName;
    /**
     * @var string $lastName
     * @Assert\NotBlank()
     * @Assert\Regex(pattern="/^[a-zA-Z\s]*$/", message = "Por favor, especifica un apellido válido")
     * @ORM\Column(name="lastName", type="string", nullable=false)
     */
    private $lastName;
    /**
     * @var string $password
     * @Assert\NotBlank()
     * @ORM\Column(name="password", type="string", nullable=true)
     */
    private $password;
    /**
     * @var string $salt
     *
     * @ORM\Column(name="salt", type="string", nullable=true)
     */
    private $salt;
    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", nullable=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string $phone
     * @ORM\Column(name="phone", type="string", nullable=true)
     */
    private $phone;
    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Role", inversedBy="users")
     * @ORM\JoinColumn(name="role", referencedColumnName="id")
     */
    private $role;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Reply", mappedBy="user",cascade={"all"})
     */
    private $replies;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Voucher", mappedBy="user",cascade={"all"})
     */
    private $vouchers;


    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Recibo", mappedBy="usuario",cascade={"all"})
     */
    private $recibos;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Traspaso", mappedBy="user",cascade={"all"})
     */
    private $traspasos;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Extra", mappedBy="usuario",cascade={"all"})
     */
    private $extras;

    /**
     * @ORM\OneToMany(targetEntity="Belraysa\BackendBundle\Entity\Client", mappedBy="user",cascade={"all"})
     */
    private $customers;
    /**
     * @var string $photo
     *
     * @ORM\Column(name="photo", type="string", nullable=false)
     */
    private $photo;


    /**
     * @var string $letra
     * @Assert\NotBlank()
     * @ORM\Column(name="letra", type="string", nullable=false)
     * @Assert\Regex(pattern="/^[a-zA-Z\s]$/", message = "Por favor, especifica una letra válida")
     */
    private $letra;

    /**
     * @ORM\ManyToOne(targetEntity="Belraysa\BackendBundle\Entity\Workspace", inversedBy="users")
     * @ORM\JoinColumn(name="workspace", referencedColumnName="id")
     */
    private $workspace;

    /**
     * @ORM\ManyToMany(targetEntity="Belraysa\BackendBundle\Entity\Nomenclador", inversedBy="usuariosLectura")
     * @ORM\JoinTable(name="usuario_lectura")
     */
    private $nomencladoresLectura;

    /**
     * @ORM\ManyToMany(targetEntity="Belraysa\BackendBundle\Entity\Nomenclador", inversedBy="usuariosEscritura")
     * @ORM\JoinTable(name="usuario_escritura")
     */
    private $nomencladoresEscritura;

    /**
     * @var boolean $activo
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

    /**
     * @var string $firma
     *
     * @ORM\Column(name="firma", type="string", nullable=true)
     */
    private $firma;
    
    function __construct()
    {
        $this->recibos = new ArrayCollection();
        $this->extras = new ArrayCollection();
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
     * @return string
     */
    public function getLetra()
    {
        return $this->letra;
    }

    /**
     * @param string $letra
     */
    public function setLetra($letra)
    {
        $this->letra = $letra;
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
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
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
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
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
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }


    /**
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param string $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    //Implementation of UserInterface

    function equals(\Symfony\Component\Security\Core\User\UserInterface $user)
    {
        return $this->getUsername() == $user->getUsername();
    }

    function eraseCredentials()
    {
    }

    function getRoles()
    {
        return array($this->role->getName());
    }

    public function __toString()
    {
        return $this->getUsername();
    }

    //Uploading agencyLogo
    public function uploadPhoto($route)
    {
        if (null === $this->photo) {
            $nameFile = 'no-image4.png';
            $this->setPhoto($nameFile);

        } else {
            $nameFile = uniqid('photo_user-') . '-foto1.jpg';

            $this->photo->move($route, $nameFile);

            $this->setPhoto($nameFile);
        }
    }


 //Uploading agencyLogo
    public function uploadFirma($route)
    {
       
            $nameFile = uniqid('firma_user-') . '-firma1.jpg';

            $this->firma->move($route, $nameFile);

            $this->setFirma($nameFile);
        
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
    public function getFirma()
    {
        return $this->firma;
    }

    /**
     * @param string $firma
     */
    public function setFirma($firma)
    {
        $this->firma = $firma;
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
    public function getTraspasos()
    {
        return $this->traspasos;
    }

    /**
     * @param mixed $traspasos
     */
    public function setTraspasos($traspasos)
    {
        $this->traspasos = $traspasos;
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

    public function addRecibo(Recibo $recibo)
    {
        if (!$this->hasRecibo($recibo)) {
            $recibo->setUsuario($this);
            $this->recibos->add($recibo);
        }
    }

    public function removeRecibo(Recibo $recibo)
    {
        if ($this->hasRecibo($recibo)) {
            $this->recibos->removeElement($recibo);
            $recibo->setUsuario(null);
        }
    }

    public function hasRecibo(Recibo $recibo)
    {
        return $this->recibos->contains($recibo);
    }

    public function addExtra(Extra $extra)
    {
        if (!$this->hasExtra($extra)) {
            $extra->setUsuario($this);
            $this->extras->add($extra);
        }
    }

    public function removeExtra(Extra $extra)
    {
        if ($this->hasExtra($extra)) {
            $this->extras->removeElement($extra);
            $extra->setUsuario(null);
        }
    }

    public function hasExtra(Extra $extra)
    {
        return $this->extras->contains($extra);
    }

    /**
     * @return mixed
     */
    public function getNomencladoresLectura()
    {
        return $this->nomencladoresLectura;
    }

    /**
     * @param mixed $nomencladoresLectura
     */
    public function setNomencladoresLectura($nomencladoresLectura)
    {
        $this->nomencladoresLectura = $nomencladoresLectura;
    }

    /**
     * @return mixed
     */
    public function getNomencladoresEscritura()
    {
        return $this->nomencladoresEscritura;
    }

    /**
     * @param mixed $nomencladoresEscritura
     */
    public function setNomencladoresEscritura($nomencladoresEscritura)
    {
        $this->nomencladoresEscritura = $nomencladoresEscritura;
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

    public function isAccountNonExpired()
    {
        return $this->isActivo();
    }

    public function isAccountNonLocked()
    {
        return $this->isActivo();
    }

    public function isCredentialsNonExpired()
    {
        return $this->isActivo();
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return bool    true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled()
    {
        return $this->isActivo();
    }

    // serialize and unserialize must be updated - see below
    public function serialize()
    {
        return serialize(array(
            $this->id
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id
            ) = unserialize($serialized);
    }

    public function getRoleName()
    {
        return $this->role->getName();
    }



}