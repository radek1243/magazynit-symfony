<?php

namespace App\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

class BaseDeviceTypeValidator{

    /**
     * @Assert\Type("Doctrine\Common\Collections\ArrayCollection")
     * @Assert\Valid
     */
    protected $devices;

    public function __construct()
    {
        $this->devices = new ArrayCollection();
    }

    /**
     * Get the value of devices
     */ 
    public function getDevices(): Collection
    {
        return $this->devices;
    }

    /**
     * Set the value of devices
     *
     * @return  self
     */ 
    public function setDevices(ArrayCollection $devices)
    {
        $this->devices = $devices;
    }
}