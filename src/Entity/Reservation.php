<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * 
 * @ORM\Entity
 * @ORM\Table(name = "rezerwacja")
 *
 */
class Reservation
{
    /**
     * 
     * @ORM\Id
     * @ORM\OneToOne(targetEntity = "Device")
     * @ORM\JoinColumn(name = "urzadzenie_id", referencedColumnName = "id")
     */
    private $device;
    
    /**
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity = "User")
     * @ORM\JoinColumn(name = "uzytkownik_id", referencedColumnName = "id")
     */
    private $user;
    
    /**
     * @return mixed
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * @param mixed $device
     */
    public function setDevice($device)
    {
        $this->device = $device;
    }

    /**
     * @return \App\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \App\Entity\User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

}

