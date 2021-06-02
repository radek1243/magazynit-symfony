<?php
namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @ORM\ManyToMany(targetEntity = "Type")
     * @ORM\JoinTable(name = "model_typ",
     *      joinColumns={@ORM\JoinColumn(name = "model_id", referencedColumnName = "id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name = "typ_id", referencedColumnName = "id")}
     *      )
     */
    private $types;
    
    public function __construct(){
        $this->types = new ArrayCollection();
    }
    
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
    
    public function getTypes(): Collection{
        return $this->types;
    }
    
    public function setTypes($types){
        $this->types = $types;
    }
    
}

