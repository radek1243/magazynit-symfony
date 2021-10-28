<?php

namespace App\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class ServiceTypeValidator{


    /**
     * @Assert\NotBlank()
     * @Assert\Type("App\Entity\Type")
     */
    private $type;

    /**
     * @Assert\Type("Doctrine\Common\Collections\ArrayCollection")
     * @Assert\Valid
     */
    private $devices;

    public function __construct()
    {
        $this->devices = new ArrayCollection();
    }


    
    /**
     * Get the value of devices
     */ 
    public function getDevices()
    {
        return $this->devices;
    }

    /**
     * Set the value of devices
     *
     * @return  self
     */ 
    public function setDevices($devices)
    {
        $this->devices = $devices;
    }

    /**
     * Get the value of type
     */ 
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @return  self
     */ 
    public function setType($type)
    {
        $this->type = $type;
    }
}