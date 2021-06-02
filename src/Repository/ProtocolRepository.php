<?php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class ProtocolRepository extends EntityRepository
{
    
    public function protocolList(){
        $query = $this->_em->createQuery(
            "select p.id, p.date, l.name, l.shortName, p.person, r.name as rname, r.surname, u.login, p.type, p.returned from App\Entity\Protocol p ".
            "JOIN p.location l ".
            "JOIN p.user u ".
            "JOIN p.receiver r ".
            "order by p.id desc"
            );
        return $query->getResult();
    }
    
    public function getPersonProtocols($person){
        $queryBuilder = $this->_em->createQueryBuilder()->select('p')->from('App\Entity\Protocol', 'p')->where('p.receiver = :receiver or p.sender = :sender')->orderBy('p.date','desc');
        $queryBuilder->setParameter('receiver', $person);
        $queryBuilder->setParameter('sender', $person);
        return $queryBuilder->getQuery()->getResult();
    }
}

