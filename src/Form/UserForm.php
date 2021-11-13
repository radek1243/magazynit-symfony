<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class UserForm{

    /**
     * @Assert\NotBlank(groups={"Default","edit_user"})
     * @Assert\Type(
     * type = "string",
     * groups={"Default","edit_user"}
     * )
     * @Assert\Length(
     * max = 20,
     * groups={"Default","edit_user"} 
     * )
     */
    private $login;

    /**
     * @Assert\NotBlank(groups={"Default"})
     * @Assert\Type(
     * type = "string",
     * groups={"Default","edit_user"}
     * )
     * @Assert\Length(
     * max = 30,
     * groups={"Default","edit_user"}
     * )
     */
    private $password;

    /**
     * @Assert\NotBlank(groups={"Default","edit_user"})
     * @Assert\Type(
     *      type = "Doctrine\Common\Collections\Collection",
     *      groups={"Default","edit_user"}
     * )
     * @Assert\Count(
     *      min = 1,
     *      groups = {"Default","edit_user"},
     *      minMessage = "Użytkownik musi posiadać co najmniej {{ limit }} rolę."
     * )
     * @Assert\All(
     *      constraints = {
     *          @Assert\NotNull,
     *          @Assert\Type("App\Entity\Role")
     *      },
     *      groups = {"Default","edit_user"}
     * )
     */    
    private $roles;

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
     * Get the value of password
     */ 
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */ 
    public function setPassword($password)
    {
        $this->password = $password;
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