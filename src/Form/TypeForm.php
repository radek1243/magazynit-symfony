<?php
namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class TypeForm
{
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

}

