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
 * Belraysa\BackendBundle\Entity\Envio
 * @ORM\Entity(repositoryClass="Belraysa\BackendBundle\Repository\EnvioRepository")
 * @ORM\Table()
 */
class Envio extends Concepto
{

    /**
     * @var string $remitenteNombre
     *
     * @ORM\Column(name="remitenteNombre", type="string", nullable=false)
     */
    private $remitenteNombre;

    /**
     * @var string $remitenteCedula
     *
     * @ORM\Column(name="remitenteCedula", type="string", nullable=false)
     */
    private $remitenteCedula;

    /**
     * @return string
     */
    public function getRemitenteCedula()
    {
        return $this->remitenteCedula;
    }

    /**
     * @param string $remitenteCedula
     */
    public function setRemitenteCedula($remitenteCedula)
    {
        $this->remitenteCedula = $remitenteCedula;
    }

    /**
     * @return string
     */
    public function getRemitenteNombre()
    {
        return $this->remitenteNombre;
    }

    /**
     * @param string $remitenteNombre
     */
    public function setRemitenteNombre($remitenteNombre)
    {
        $this->remitenteNombre = $remitenteNombre;
    }


} 