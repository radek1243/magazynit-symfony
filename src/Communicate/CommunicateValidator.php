<?php

namespace App\Communicate;

class CommunicateValidator{

    public function isValid(Communicate $communicate){
        if($communicate->getCommunicateText()===null && $communicate->getErrorText()===null){
            return false;
        }
        elseif($communicate->getCommunicateText()!==null && $communicate->getErrorText()!==null){
            return false;
        }
        return true;
    }
}