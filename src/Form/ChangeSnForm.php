<?php
namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;


class ChangeSnForm
{
    /**
     * @Assert\NotBlank
     * @Assert\Length(max = 30)
     * @Assert\Regex(
     *   pattern = "/^[a-zA-Z0-9]+$/",
     *   htmlPattern = "[a-zA-Z0-9]+"
     * )
     */
    private $serialnumber;
    
    /**
     * 
     * @Assert\NotBlank
     */
    private $dev_id;
    
    public function getSerialNumber(){
        return $this->serialnumber;
    }
    
    public function setSerialNumber($sn){
        $this->serialnumber = $sn;
    }
    /**
     * @return mixed
     */
    public function getDevId()
    {
        return $this->dev_id;
    }

    /**
     * @param mixed $dev_id
     */
    public function setDevId($dev_id)
    {
        $this->dev_id = $dev_id;
    }

}

