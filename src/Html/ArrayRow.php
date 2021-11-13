<?php
namespace App\Html;

use ReflectionClass;

class ArrayRow
{
    private $cells;
    private $hasDoctrineObjects;
    private $GET = "get";
    
    public function __construct(array $cells, bool $hasDoctrineObjects){
        $this->cells = $cells;
        $this->hasDoctrineObjects = $hasDoctrineObjects;
    }
     
    public function createRow($dataRow, ?string $trClass): string{
        $html = "<tr";
        if($trClass!=null){
            $html .= " class='".$trClass."'";
        }
        $html .= ">";
        foreach($this->cells as $cell){
            $html .= "<td";
            $value = $this->generateValue($dataRow, $cell->getIndexNames(), $cell->getDateFormat(), $cell->getTdValue());
            if($cell->getTdClass()!=null){
                $html .= " class='".$cell->getTdClass()[$value]."'";
            }
            $html .= ">";            
            if($cell->getInputSpec()!=null){
                $html .= "<input type='".$cell->getInputSpec()->getType()."' name='".$cell->getInputSpec()->getName();
                if($cell->getInputSpec()->isArray()){
                    $html .= "[".$value."]";
                }
                $html .= "' value='".$value."'";
                if($cell->getInputSpec()->getAttr()!=null){
                    foreach($cell->getInputSpec()->getAttr() as $key => $val){
                        $html .= " ".$key."='".$value."'";
                    }
                }
                $html .= ">";
            }
            else{
                $html .= $value;
            }
            $html .= "</td>";
        }
        $html .= "</tr>";
        return $html;        
    }

    private function generateValue($dataRow, array $indexNames, ?string $dateFormat = null, ?array $tdValue = null): string{
        $html = null;
        $temp = null;
        if($this->hasDoctrineObjects==true){
            $reflector = new ReflectionClass(get_class($dataRow));
            for($i=0;$i<sizeof($indexNames);$i++){
                $temp = $reflector->getMethod($this->GET.ucfirst($indexNames[$i]))->invoke($dataRow);
                if($dateFormat!=null){
                    $temp = $temp->format($dateFormat);
                }
                if($tdValue!=null){
                    $temp = $tdValue[$temp];
                }
                $html .= $temp;
                if(($i+1)<sizeof($indexNames)){
                    $html .= " ";
                }
            }
        }
        else{
            for($i=0;$i<sizeof($indexNames);$i++){
                if($dateFormat!=null){
                    $temp = $dataRow[$indexNames[$i]]->format($dateFormat);
                }
                else{
                    $temp = $dataRow[$indexNames[$i]];   
                }
                if($tdValue!=null){
                    $temp = $tdValue[$temp];
                }
                $html .= $temp;
                if(($i+1)<sizeof($indexNames)){
                    $html .= " ";
                }
            }
        }
        return $html;
    }
}

