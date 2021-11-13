<?php

namespace App\Html;

class DoctrineCell{

    public function __construct(string $methodName, string $type, ?array $cssClass)
    {
        $this->methodName = $methodName;
        $this->type = $type;
        $this->cssClass = $cssClass;
    }

    private $methodName;    //string
    private $type;      //string
    private $cssClass;  //array|null

    /**
     * Get the value of methodName
     */ 
    public function getMethodName(): string
    {
        return $this->methodName;
    }

    /**
     * Set the value of methodName
     *
     * @return  self
     */ 
    public function setMethodName(string $methodName)
    {
        $this->methodName = $methodName;
    }

    /**
     * Get the value of type
     */ 
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @return  self
     */ 
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * Get the value of cssClass
     */ 
    public function getCssClass(): ?array
    {
        return $this->cssClass;
    }

    /**
     * Set the value of cssClass
     *
     * @return  self
     */ 
    public function setCssClass(?array $cssClass)
    {
        $this->cssClass = $cssClass;
    }
}