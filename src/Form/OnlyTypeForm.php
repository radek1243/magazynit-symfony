<?php
namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class OnlyTypeForm{

    /**
     * @Assert\NotBlank
     * @Assert\Type("App\Entity\Type")
     */
    private $type;


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