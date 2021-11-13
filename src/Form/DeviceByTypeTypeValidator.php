<?php

namespace App\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class DeviceByTypeTypeValidator extends BaseDeviceTypeValidator{


    /**
     * @Assert\NotBlank()
     * @Assert\Type("App\Entity\Type")
     */
    protected $type;

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