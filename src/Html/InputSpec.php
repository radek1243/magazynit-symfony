<?php
namespace App\Html;

class InputSpec
{
    private $type;
    private $name;
    private $isArray;
    private $attr;
    
    public function __construct(string $type, string $name, bool $isArray, ?array $attr = null){
        $this->type = $type;
        $this->name = $name;
        $this->isArray = $isArray;
        $this->attr = $attr;
    }
    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isArray(): bool
    {
        return $this->isArray;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param bool $isArray
     */
    public function setIsArray(bool $isArray)
    {
        $this->isArray = $isArray;
    }

    /**
     * @return array|null
     */ 
    public function getAttr()
    {
        return $this->attr;
    }

    /**
     * @param array|null $attr
     *
     */ 
    public function setAttr($attr)
    {
        $this->attr = $attr;
    }
}

