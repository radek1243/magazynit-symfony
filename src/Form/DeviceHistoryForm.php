<?php
namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;


class DeviceHistoryForm
{
    /**
     * @Assert\NotBlank
     * @Assert\Length(max = 30)
     */
    private $sn;
    
    public function getSn(){
        return $this->sn;
    }
    
    public function setSn($sn){
        $this->sn = $sn;
    }
}

