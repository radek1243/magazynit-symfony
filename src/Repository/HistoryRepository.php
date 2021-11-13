<?php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class HistoryRepository extends EntityRepository
{
         
    
    public function getHistoryByDate($type_id, $operation_time){
        $rsmp = new ResultSetMapping();
        $rsmp->addScalarResult('typ_nazwa', 'type_name');
        $rsmp->addScalarResult('model_nazwa', 'model_name');
        $rsmp->addScalarResult('sn', 'sn');
        $rsmp->addScalarResult('sn2', 'sn2');
        $rsmp->addScalarResult('opis', 'desc');
        $rsmp->addScalarResult('lok_nazwa', 'location_name');
        $rsmp->addScalarResult('serwis', 'service');
        $rsmp->addScalarResult('stan', 'state');
        $rsmp->addScalarResult('fv', 'fv');
        $rsmp->addScalarResult('utyl', 'utilization');
        $rsmp->addScalarResult('czas_op', 'operation_time');
        $query = $this->_em->createNativeQuery("select typ.nazwa as typ_nazwa, model.nazwa as model_nazwa, urzadzenie.sn, urzadzenie.sn2, urzadzenie.opis, "
            ."lokalizacja.nazwa as lok_nazwa, urzadzenie.serwis, urzadzenie.stan, urzadzenie.fv, urzadzenie.utyl, urzadzenie.czas_op from urzadzenie "
            . "join typ on typ.id=urzadzenie.typ_id "
            . "join model on model.id=urzadzenie.model_id "
            . "join lokalizacja on lokalizacja.id=urzadzenie.lok_id "
            . "where urzadzenie.czas_op >= :opTime and urzadzenie.typ_id= :typeId union "
            ."select typ.nazwa as typ_nazwa, model.nazwa as model_nazwa, hist.sn, hist.sn2, hist.opis, "
            ."lokalizacja.nazwa as lok_nazwa, hist.serwis, hist.stan, hist.fv, hist.utyl, hist.czas_op from hist "
            . "join typ on typ.id=hist.typ_id "
            . "join model on model.id=hist.model_id "
            . "join lokalizacja on lokalizacja.id=hist.lok_id "
            . "where hist.czas_op >= :opTime and hist.typ_id= :typeId order by czas_op desc;", $rsmp);        
        return $query->execute(array('typeId' => $type_id, 'opTime' => $operation_time));
    }
}

