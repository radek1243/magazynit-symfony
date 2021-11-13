<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class UserListSubformValidator{

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private $id;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Length(max = 20)
     */
    private $login;

    /**
     * @Assert\Type("array")
     */
    private $roles;

    


    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the value of login
     */ 
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set the value of login
     *
     * @return  self
     */ 
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * Get the value of roles
     */ 
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set the value of roles
     *
     * @return  self
     */ 
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }
}