<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class PersonRepository extends EntityRepository{

    public function getPersonByPosition(){
        $query = $this->_em->createQuery(
            "select p, s from App\Entity\Person "
            . "join p.position s "
            . "where s.isSetLoc = true"
        );
        return $query->getResult();
    }
}