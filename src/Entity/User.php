<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name = "uzytkownik")
 */
class User implements UserInterface
{
    
    /**
     * @ORM\Column(type = "integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy = "AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type = "string", length = 20, unique = true)
     */
    private $login;
    
    /**
     * @ORM\Column(name = "haslo", type = "string", length = 64)
     */
    private $pass;
    
    /**
     * Get Id
     * 
     * @return integer
     */
    public function getId(): int {
        return $this->id;
    }
    
    /**
     * Set Login
     * 
     * @param string $login
     */
    public function setLogin($login) {
        $this->login=$login;
    }
    
    /**
     * Get Login
     * 
     * @return string
     */
    public function getLogin(): string {
        return $this->login;
    }
    
    /**
     * Set Password
     *
     * @param string $pass
     */
    public function setPass($pass) {
        $this->pass = $pass;
    }
    
    /**
     * Get Password
     *
     * @return string
     */
    public function getPass(): string {
        return $this->pass;
    }
    public function getPassword(): string
    {
        return $this->getPass();
    }

    public function eraseCredentials()
    {

    }

    public function getSalt()
    {
        return null;
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function getUsername()
    {
        return $this->getLogin();
    }


}

