<?php
namespace App\Form;

use App\Entity\Location;
use App\Entity\Type;
use Symfony\Component\Validator\Constraints as Assert;

class FindOperationForm extends BaseOperationForm{

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Length(max = 30)
     */
    private $current_sn;

    public function setCurrentSn($sn){
        $this->current_sn = $sn;
    }

    public function getCurrentSn(): ?string{
        return $this->current_sn;
    }

    protected function reset(){

    }
}