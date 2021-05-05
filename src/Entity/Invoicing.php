<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name = "fakturowanie")
 */
class Invoicing
{
    
    /**
     * @ORM\Column(type = "integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy = "AUTO")
     */
    private $id;
    
    /**
     * @ORM\OneToOne(targetEntity = "Type")
     * @ORM\JoinColumn(name = "typ_id", referencedColumnName = "id")
     */
    private $type;
    
    /**
     * Get Id
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }
    
    /**
     * Get Type
     * @return Type
     */
    public function getType(): Type
    {
        return $this->type;
    }

    /**
     * @param Type $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

}

