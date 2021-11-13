<?php

namespace App\Form;

use App\Entity\Type;
use Symfony\Component\Validator\Constraints as Assert;

class TypeFormTypeValidator{

    /**
     * @Assert\NotBlank
     * @Assert\Type("App\Entity\Type")
     */
    private $type;

    

    /**
     * Get the value of type
     */ 
    public function getType(): ?Type
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     */ 
    public function setType(?Type $type)
    {
        $this->type = $type;
    }
}