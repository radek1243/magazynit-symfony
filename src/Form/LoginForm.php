<?php
namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class LoginForm
{
    /**
     * 
     * @Assert\NotBlank
     * @Assert\Length(max = 20)
     */
    private $login;
    
    /**
     * 
     * @Assert\NotBlank
     * @Assert\Length(max = 20)
     */
    private $pass;
    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $user
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return mixed
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * @param mixed $pass
     */
    public function setPass($pass)
    {
        $this->pass = $pass;
    }

    
}

