<?php
namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class HistoryByDateForm{

    /**
     * @Assert\NotBlank
     * @Assert\Type("App\Entity\Type")
     */
    private $type;

    /**
     * @Assert\NotBlank
     * @Assert\Type("DateTime")
     */
    private $date;
    

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
     * Get the value of dateTime
     */ 
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the value of dateTime
     *
     * @return  self
     */ 
    public function setDate($date)
    {
        $this->date = $date;
    }
}