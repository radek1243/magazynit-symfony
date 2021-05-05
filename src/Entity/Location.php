<?php
namespace App\Entity;

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
}

