<?php
namespace App\Html;

use ReflectionObject;

class HtmlBuilder
{
    
    private function startTable(){
        return "<table class='col-12'>";
    }
    
    private function endTable(){
        return "</table>";
    }
    
    private function createTableHeaders(array $headers){
        $html = "<tr class='tr-back'>";     //tworzenie wiersza z nagłówkami tabeli
        foreach($headers as $header){
            $html .= "<td>".$header."</td>";
        }
        $html  .= "</tr>";
        return $html;
    }

    public function startTableRow(?string $class){
        return $class!==null ? "<tr class=".$class.">" : "<tr>";
    }

    public function endTableRow(){
        return "</tr>";
    }
    
    public function createCellsFromDoctrine(array $doctrineCells, object $object){       
        $reflection = new ReflectionObject($object);
        $html = null;
        foreach($doctrineCells as $cell){
            $data = $reflection->getMethod("get".$cell->getMethodName())->invoke($object);
            $html .= $this->startCell($cell->getCssClass()!==null ? $cell->getCssClass()[$data] : null);
            if($cell->getType()==='date'){
                $html .= $data->format('d-m-Y H:i:s');
            }
            else if($cell->getType()==='bool'){
                if($data){
                    $html .= "Tak";
                }
                else{
                    $html .= "Nie";
                }
            }
            else{
                $html .= $data;
            }
            $html .= $this->endCell();
        }      
        return $html;
    }

    private function startCell(?string $class){
        return $class!==null ? "<td class=".$class.">" : "<td>";
    }

    private function endCell(){
        return "</td>";
    }
    
    public function createSelectTagFromArray($label, $selectId, $selectName, array $data, $indexValue, $indexName, $selectedValue = null){
        $html = "<label>".$label." </label><select id='".$selectId."' name='".$selectName."'>";
        foreach($data as $row){
            if($selectedValue!=null && $selectedValue===$row[$indexValue]){
                $html .= "<option value='".$row[$indexValue]."' selected>".$row[$indexName]."</option>";
            }
            else {
                $html .= "<option value='".$row[$indexValue]."'>".$row[$indexName]."</option>";
            }
        }
        $html .= "</select>";
        return $html;        
    }

    public function createTable(array $headers, array $tableCells, array $data, bool $hasDoctrineObjects){
        $html = $this->startTable();    //rozpoczecie tabeli
        $html .= $this->createTableHeaders($headers);   //stworzenie naglowkow tabeli
        $counter = 0;       //licznik wierszy potrzebny do teł wierszy
        $tableRow = new ArrayRow($tableCells, $hasDoctrineObjects);
        foreach($data as $row){        //petla przechodzaca przez wiersze danych
            $trClass = null;
            if($counter % 2 == 0){
                $trClass = "tr-back";
           } 
           $html .= $tableRow->createRow($row, $trClass); 
           $counter++;
        }   
        $html .= $this->endTable();     //zakonczenie tabeli html
        return $html;
    }
}