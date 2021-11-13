<?php
namespace App\Form;

use App\Entity\Location;
use App\Entity\Type;
use Symfony\Component\Validator\Constraints as Assert;

class MainOperationForm extends BaseOperationForm{

    /**
     * @Assert\Type("App\Entity\Location")
     */
    private $current_loc;

    /**
     * @Assert\Type("App\Entity\Type")
     */
    private $current_type;

    public function getCurrentLoc(): ?Location{
        return $this->current_loc;
    }

    public function setCurrentLoc(?Location $currentLoc){
        $this->current_loc = $currentLoc;
    }

    public function getCurrentType(): ?Type{
        return $this->current_type;
    }

    public function setCurrentType(?Type $currentType){
        $this->current_type = $currentType;
    }

    protected function reset(){

    }
}