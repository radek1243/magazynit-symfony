<?php
namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;


class DeviceHistoryForm
{
    /**
     * @Assert\NotBlank
     * @Assert\Length(max = 30)
     * @Assert\Regex(
     *   pattern = "/^[a-zA-Z0-9]+$/",
     *   htmlPattern = "[a-zA-Z0-9]+",
     *   message = "Numer seryjny może zawierać tylko litery i cyfry!"
     * )
     */
    private $sn;
    
    public function getSn(){
        return $this->sn;
    }
    
    public function setSn($sn){
        $this->sn = $sn;
    }
}

