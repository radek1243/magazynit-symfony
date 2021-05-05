<?php
namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;


class LocationForm
{
    /**
     * 
     * @Assert\NotBlank
     * @Assert\Length(max = 40)
     */   
    private $name;
    
    /**
     * 
     * @Assert\NotBlank
     * @Assert\Length(max = 10)
     */
    private $shortName;
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

    /**
     * @return mixed
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * @param mixed $shortName
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;
    }

    
    
    
}

