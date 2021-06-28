<?php
namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class AddProtocolZForm extends AddProtocolForm{

    /**
     * @Assert\NotBlank
     * @Assert\Type("App\Entity\Person")
     */
    private $sender;

    

    /**
     * Get the value of sender
     */ 
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set the value of sender
     *
     * @return  self
     */ 
    public function setSender($sender)
    {
        $this->sender = $sender;
    }
}