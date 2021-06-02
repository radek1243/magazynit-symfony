<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TypeRepository")
 * @ORM\Table(name = "typ")
 */
class Type
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
    
    /**
     * @ORM\ManyToMany(targetEntity = "Model")
     * @ORM\JoinTable(name = "model_typ",
     *      joinColumns={@ORM\JoinColumn(name = "typ_id", referencedColumnName = "id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name = "model_id", referencedColumnName = "id")}
     *      )
     */
    private $models;
    
    public function __construct(){
        $models = new ArrayCollection();
    }
    
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
    
    public function getModels(): Collection{
        return $this->models;
    }
    
    public function setModels($models) {
        $this->models=$models;
    }
}

