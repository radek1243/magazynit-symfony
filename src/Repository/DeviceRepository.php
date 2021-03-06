<?php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class DeviceRepository extends EntityRepository
{

    public function getDeviceByTypeFromLoc($type_id, $lok_id){       
        $query = $this->_em->createQuery("select m.name, d.state, d.sn, d.sn2, d.desc, d.id from App\Entity\Device d "
                        . "JOIN d.model m "
                        . "where d.location = :loc and d.type = :type and d.service = false and d.id not in (select dev.id from App\Entity\Reservation r join r.device dev) "
                        . "and d.utilization = false order by d.state, m.name asc");
        $query->setParameter('loc', $lok_id);
        $query->setParameter('type', $type_id);
        return $query->getResult();
    }
    
    public function getDevBySN($sn){
        $query = $this->_em->createQuery("select t.name type_name, m.name model_name, d.sn, d.sn2, d.desc, "
                        ."l.name location_name, d.service, d.state, d.utilization, d.id from App\Entity\Device d "
                        . "join d.type t "
                        . "join d.model m "
                        . "join d.location l "
                        . "where (d.sn like :sn or d.sn2 like :sn)");
        $query->setParameter('sn', $sn);
        return $query->getResult();
    }
    
    public function getEfficientDevices($type_id){
        $query = $this->_em->createQuery("select m.name, d.state, d.sn, d.sn2, d.desc, d.id from App\Entity\Device d "
            . "JOIN d.model m "
            . "where d.location = 1 and d.type = :type and d.service = false and d.state = 'S' and d.id not in (select dev.id from App\Entity\Reservation r join r.device dev) "
            . "and d.utilization = false order by d.state, m.name asc");
        $query->setParameter('type', $type_id);
        return $query->getResult();
    }
}

