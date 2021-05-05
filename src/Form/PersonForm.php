<?php
namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class PersonForm
{
    /**
     * 
     * @Assert\NotBlank
     * @Assert\Length(max = 20)
     */
    private $name;
    
    /**
     *
     * @Assert\NotBlank
     * @Assert\Length(max = 40)
     */
    private $surname;
    
    /**
     *
     * @Assert\NotBlank
     * @Assert\Email
     * @Assert\Length(max = 50)
     */
    private $email;
    
    /**
     * 
     * @Assert\Type("App\Entity\Position")
     */
    private $position;
    
    /**
    *
    * @Assert\Type("App\Entity\Location")
    */
    private $location;      //Doctrine\Common\Collections\ArrayCollection
    
    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

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
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param mixed $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }   
    

}

