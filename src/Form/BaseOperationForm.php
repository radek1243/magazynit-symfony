<?php
namespace App\Form;

use App\Entity\Location;
use App\Entity\Type;
use Symfony\Component\Validator\Constraints as Assert;

abstract class BaseOperationForm{
    
    /**
     * @Assert\Type("App\Entity\Location")
     */
    protected $dest_loc;
    
    /**
     * @Assert\Type("string")
     */
    protected $newdesc;

    public function getDestLoc(): ?Location{
        return $this->dest_loc;
    }

    public function setDestLoc(?Location $destLoc){
        $this->dest_loc = $destLoc;
    }

    public function getNewDesc(): ?String{
        return $this->newdesc;
    }

    public function setNewDesc(?String $newDesc){
        $this->newdesc = $newDesc;
    }

    protected abstract function reset();
}