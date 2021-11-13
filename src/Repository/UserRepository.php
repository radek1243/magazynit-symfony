<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository{

    public function getFirstUser(){
        $query = $this->_em->createQuery("select u.id, u.login, u.pass from App\Entity\User u order by u.id asc");
        $query->setMaxResults(1);
        $array = $query->getResult();
        return $this->_em->find(User::class, $array[0]['id']);
    }
}