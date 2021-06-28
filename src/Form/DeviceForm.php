<?php
namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class DeviceForm{

    /**
     * @Assert\NotBlank
     * @Assert\Type("App\Entity\Type")
     */
    private $type;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Length(max = 30)
     */
    private $sn;

    /**
     * @Assert\Type("string")
     * @Assert\Length(max = 30)
     */
    private $sn2;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Length(max = 1)
     */
    private $state;

    /**
     * @Assert\NotBlank
     * @Assert\Type("boolean")
     */
    private $invoicing;

    /**
     * @Assert\Type("string")
     * @Assert\Length(max = 255)
     */
    private $desc;

    


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

    /**
     * Get the value of sn
     */ 
    public function getSn()
    {
        return $this->sn;
    }

    /**
     * Set the value of sn
     *
     * @return  self
     */ 
    public function setSn($sn)
    {
        $this->sn = $sn;
    }

    /**
     * Get the value of sn2
     */ 
    public function getSn2()
    {
        return $this->sn2;
    }

    /**
     * Set the value of sn2
     *
     * @return  self
     */ 
    public function setSn2($sn2)
    {
        $this->sn2 = $sn2;
    }

    /**
     * Get the value of state
     */ 
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set the value of state
     *
     * @return  self
     */ 
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * Get the value of invoicing
     */ 
    public function getInvoicing()
    {
        return $this->invoicing;
    }

    /**
     * Set the value of invoicing
     *
     * @return  self
     */ 
    public function setInvoicing($invoicing)
    {
        $this->invoicing = $invoicing;
    }

    /**
     * Get the value of desc
     */ 
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * Set the value of desc
     *
     * @return  self
     */ 
    public function setDesc($desc)
    {
        $this->desc = $desc;
    }
}