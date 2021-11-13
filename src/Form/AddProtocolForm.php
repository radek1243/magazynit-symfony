<?php
namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class AddProtocolForm{

    /**
     * @Assert\NotBlank
     * @Assert\Type("App\Entity\Location")
     */
    private $destination_loc;

    /**
     * @Assert\NotBlank
     * @Assert\Type("App\Entity\Person")
     */
    private $principal;

    /**
     * @Assert\NotBlank
     * @Assert\Type("App\Entity\Person")
     */
    private $receiver;

    /**
     * @Assert\NotBlank
     * @Assert\Type("DateTime")
     */
    private $date;

    /**
     *@Assert\Type("string")
     *@Assert\Length(max = 255) 
     */
    private $rest_devices;

    /**
     * Get the value of destination_loc
     */ 
    public function getDestinationLoc()
    {
        return $this->destination_loc;
    }

    /**
     * Set the value of destination_loc
     *
     * @return  self
     */ 
    public function setDestinationLoc($destination_loc)
    {
        $this->destination_loc = $destination_loc;
    }

    /**
     * Get the value of principal
     */ 
    public function getPrincipal()
    {
        return $this->principal;
    }

    /**
     * Set the value of principal
     *
     * @return  self
     */ 
    public function setPrincipal($principal)
    {
        $this->principal = $principal;
    }

    /**
     * Get the value of receiver
     */ 
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * Set the value of receiver
     *
     * @return  self
     */ 
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;
    }

    /**
     * Get the value of date
     */ 
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the value of date
     *
     * @return  self
     */ 
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Get 
     */ 
    public function getRestDevices()
    {
        return $this->rest_devices;
    }

    /**
     * Set 
     *
     * @return  self
     */ 
    public function setRestDevices($rest_devices)
    {
        $this->rest_devices = $rest_devices;
    }
}