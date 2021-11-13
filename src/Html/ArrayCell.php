<?php
namespace App\Html;

class ArrayCell
{
    private $indexNames;    //array
    private $tdClass;   //array or null
    private $dateFormat;    //string
    private $inputSpec;     //array or null
    private $tdValue;   //array or null
    
    public function __construct(array $indexNames, ?array $tdClass = null, ?InputSpec $input = null, ?string $dateFormat = null, ?array $tdValue = null){
        $this->indexNames = $indexNames;
        $this->tdClass = $tdClass;
        $this->inputSpec = $input;
        $this->dateFormat = $dateFormat;
        $this->tdValue = $tdValue;
    }
    
    /**
     * @return array
     */
    public function getIndexNames(): array
    {
        return $this->indexNames;
    }

    /**
     * @return array|NULL
     */
    public function getTdClass(): ?array
    {
        return $this->tdClass;
    }

    /**
     * @return InputSpec|NULL
     */
    public function getInputSpec(): ?InputSpec
    {
        return $this->inputSpec;
    }

    /**
     * @param array $indexNames
     */
    public function setIndexNames(array $indexNames)
    {
        $this->indexNames = $indexNames;
    }

    /**
     * @param array|NULL $tdClass
     */
    public function setTdClass(?array $tdClass)
    {
        $this->tdClass = $tdClass;
    }

    /**
     * @param InputSpec|NULL $inputSpec
     */
    public function setInput(?InputSpec $inputSpec)
    {
        $this->inputSpec = $inputSpec;
    }
    

    /**
     * @return string
     */ 
    public function getDateFormat(): ?string
    {
        return $this->dateFormat;
    }

    /**
     * @param string $dateFormat
     *    
     */ 
    public function setDateFormat(?string $dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }

    /**
     * @return ?array
     */ 
    public function getTdValue(): ?array
    {
        return $this->tdValue;
    }

    /**
     * Set the value of tdValue
     *
     * @param $tdValue ?array
     */ 
    public function setTdValue(?array $tdValue)
    {
        $this->tdValue = $tdValue;
    }
}

