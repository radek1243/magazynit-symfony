<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class DeviceOperationTypeValidator extends DeviceByTypeTypeValidator{
    
    /**
     * @Assert\NotBlank
     * @Assert\Type("App\Entity\Location")
     */
    protected $currentloc;

    /**
     * @Assert\NotBlank
     * @Assert\Type("App\Entity\Location")
     */
    protected $destloc;

    /**
     * @Assert\Type("string")
     */
    protected $newdesc;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    protected $order;

    /**
     * Get the value of currentloc
     */ 
    public function getCurrentloc()
    {
        return $this->currentloc;
    }

    /**
     * Set the value of currentloc
     *
     * @return  self
     */ 
    public function setCurrentloc($currentloc)
    {
        $this->currentloc = $currentloc;
    }

    /**
     * Get the value of destloc
     */ 
    public function getDestloc()
    {
        return $this->destloc;
    }

    /**
     * Set the value of destloc
     *
     * @return  self
     */ 
    public function setDestloc($destloc)
    {
        $this->destloc = $destloc;
    }

    /**
     * Get the value of newdesc
     */ 
    public function getNewdesc()
    {
        return $this->newdesc;
    }

    /**
     * Set the value of newdesc
     *
     * @return  self
     */ 
    public function setNewdesc($newdesc)
    {
        $this->newdesc = $newdesc;
    }

    /**
     * Get the value of order
     */ 
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set the value of order
     *
     * @return  self
     */ 
    public function setOrder($order)
    {
        $this->order = $order;
    }
}