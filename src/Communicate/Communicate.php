<?php

namespace App\Communicate;

class Communicate{

    private $communicateText;
    private $errorText;

    public function __construct(?string $communicateText = null, ?string $errorText = null){
        $this->communicateText = $communicateText;
        $this->errorText = $errorText;
    }

    public function getCommunicateText(): ?string{
        return $this->communicateText;
    }

    public function setCommunicateText(?string $communicateText){
        $this->communicateText = $communicateText;
    }

    public function getErrorText(): ?string
    {
        return $this->errorText;
    }

    public function setErrorText(?string $errorText){
        $this->errorText = $errorText;
    }
}