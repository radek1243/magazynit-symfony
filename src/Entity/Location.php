<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name = "lokalizacja")
 */
class Location
{
    
    /**
     * @ORM\Column(type = "integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy = "AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(name = "nazwa", type = "string", length = 30)
     */
    private $name;
    
    /**
     * @ORM\Column(name = "skrot", type = "string", length = 10, unique = true) 
     */
    private $shortName;
    
    /**
     * @ORM\Column(name = "widoczna", type = "boolean")
     */
    private $visible;
    
    /**
     * @ORM\ManyToMany(targetEntity = "Person")
     * @ORM\JoinTable(name = "lok_oso_dyr",
     *      joinColumns={@ORM\JoinColumn(name = "lok_id", referencedColumnName = "id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name = "osoba_id", referencedColumnName = "id")}
     *      )
     */
    private $persons;

    /**
     * Get Id
     *
     * @return integer
     */
    public function getId(): int{
        return $this->id;
    }
    
    /**
     * Set Name
     *
     * @param string $name
     */
    public function setName($name){
        $this->name = $name;
    }
    
    /**
     * Get Name
     *
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }
    
    /**
     * Set Short Name
     *
     * @param string $shortName
     */
    public function setShortName($shortName){
        $this->shortName = $shortName;
    }
    
    /**
     * Get Short Name
     *
     * @return string
     */
    public function getShortName(): string {
        return $this->shortName;
    }
    
    /**
     * Set Visible
     *
     * @param boolean $visible
     */
    public function setVisible($visible){
        $this->visible = $visible;
    }
    
    /**
     * Get Visible
     *
     * @return boolean
     */
    public function getVisible(): bool {
        return $this->visible;
    }

    /**
     * 
     */ 
    public function getPersons(): ?Collection
    {
        return $this->persons;
    }

    /**
     * 
     *
     * @return  self
     */ 
    public function setPersons(?ArrayCollection $persons)
    {
        $this->persons = $persons;
    }
}

