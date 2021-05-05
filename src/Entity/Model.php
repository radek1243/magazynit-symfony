<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name = "model")
 */
class Model
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
}

