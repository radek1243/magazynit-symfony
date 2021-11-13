<?php
namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class ModelForm
{
    
    /**
     * @Assert\NotBlank
     * @Assert\Type("Doctrine\Common\Collections\ArrayCollection")
     */
    private $types;
    
    /**
     * 
     * @Assert\NotBlank
     * @Assert\Length(max = 30)
     */
    private $name;
    
    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getTypes(){
        return $this->types;
    }
    
    public function setTypes($types){
        $this->types = $types;
    }

}

