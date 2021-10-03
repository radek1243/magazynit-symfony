<?php

namespace App\Form;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;


class UserListValidator{
    
    /**
     * @Assert\NotBlank
     * @Assert\Type("Doctrine\Common\Collections\ArrayCollection")
     * @Assert\Valid
     */
    private $users;


    public function getUsers(): Collection{
        return $this->users;
    }

    public function setUsers($users){
        $this->users = $users;
    }
    
}