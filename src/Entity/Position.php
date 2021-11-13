<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * 
 * @ORM\Entity
 * @ORM\Table(name = "stanowisko")
 *
 */
class Position
{
    /**
     * @ORM\Id
     * @ORM\Column(type = "integer")
     * @ORM\GeneratedValue(strategy = "AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(name = "nazwa", type = "string", length = 30, unique = true)
     */
    private $name;

    /**
     * @ORM\Column(name = "czy_przyp_lok", type = "boolean")
     */
    private $isSetLoc;

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
    public function getId()
    {
        return $this->id;
    }


    /**
     * Get the value of isSetLoc
     */ 
    public function isSetLoc(): bool
    {
        return $this->isSetLoc;
    }

    /**
     * Set the value of isSetLoc
     *
     */ 
    public function setIsSetLoc(bool $isSetLoc)
    {
        $this->isSetLoc = $isSetLoc;
    }
}

