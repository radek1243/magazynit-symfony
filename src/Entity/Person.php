<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


/**
 * 
 * @ORM\Entity(repositoryClass="App\Repository\PersonRepository")
 * @ORM\Table(name = "osoba")
 *
 */
class Person
{    
    
    public function __construct()
    {
        $this->princLocations = new ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\Column(type = "integer")
     * @ORM\GeneratedValue(strategy = "AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(name = "imie", type = "string", length = 20)
     */
    private $name;
    
    /**
     * @ORM\Column(name = "nazwisko", type = "string", length = 40)
     */
    private $surname;
    
    /**
     * @ORM\Column(name = "email", type = "string", length = 50, unique = true)
     */
    private $email;
    
    /**
     * @ORM\Column(name = "czy_pracuje", type = "boolean", options = {"default" : "true"})
     */
    private $isWorking;
    
    /**
     * @ORM\ManyToOne(targetEntity = "Position")
     * @ORM\JoinColumn(name = "stanowisko_id", referencedColumnName = "id")
     */
    private $position;
    
    /**
     * @ORM\ManyToOne(targetEntity = "Location")
     * @ORM\JoinColumn(name = "lokalizacja_id", referencedColumnName = "id")
     */
    private $location;
     
    /**
     * @ORM\ManyToMany(targetEntity = "Location")
     * @ORM\JoinTable(name = "lok_oso_dyr",
     *      joinColumns={@ORM\JoinColumn(name = "osoba_id", referencedColumnName = "id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name = "lok_id", referencedColumnName = "id")}
     *      )
     */
    private $princLocations;

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

    /**
     * @return mixed
     */
    public function isWorking()
    {
        return $this->isWorking;
    }

    /**
     * @param mixed $isWorking
     */
    public function setIsWorking($isWorking)
    {
        $this->isWorking = $isWorking;
    }

    /**
     * @return \App\Entity\Position
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param \App\Entity\Position $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return \App\Entity\Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param \App\Entity\Location $person
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Get the value of princLocations
     */ 
    public function getPrincLocations(): ?Collection
    {
        return $this->princLocations;
    }

    /**
     * Set the value of princLocations
     *
     */ 
    public function setPrincLocations(?ArrayCollection $princLocations)
    {
        $this->princLocations = $princLocations;
    }
}

