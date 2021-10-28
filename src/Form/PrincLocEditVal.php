<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class PrincLocEditVal{

    /**
     * @Assert\Type("App\Entity\Person")
     */
    private $principal;

    /**
     * @Assert\Type("Doctrine\Common\Collections\Collection")
     */
    private $locations;

    /**
     * @Assert\Type("Doctrine\Common\Collections\Collection")
     */
    private $mislocations;

    /**
     * Get the value of principal
     */ 
    public function getPrincipal()
    {
        return $this->principal;
    }

    /**
     * Set the value of principal
     *
     * @return  self
     */ 
    public function setPrincipal($principal)
    {
        $this->principal = $principal;
    }

    /**
     * Get the value of locations
     */ 
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * Set the value of locations
     *
     * @return  self
     */ 
    public function setLocations($locations)
    {
        $this->locations = $locations;
    }

    /**
     * Get the value of mis_locations
     */ 
    public function getMislocations()
    {
        return $this->mislocations;
    }

    /**
     * Set the value of mis_locations
     *
     * @return  self
     */ 
    public function setMislocations($mislocations)
    {
        $this->mislocations = $mislocations;
    }
}