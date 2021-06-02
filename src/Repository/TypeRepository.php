<?php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class TypeRepository extends EntityRepository
{
    public function getSortedModelsByType($type){
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('nazwa', 'name');
        $query = $this->_em->createNativeQuery("select model.id, model.nazwa from model_typ join model on model.id=model_typ.model_id where model_typ.typ_id= :type order by model.nazwa asc", $rsm);
        return $query->execute(array('type' => $type));
    }
}

