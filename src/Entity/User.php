<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name = "uzytkownik")
 * 
 */
class User implements PasswordAuthenticatedUserInterface, UserInterface
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
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="rola_uzyt",
     *      joinColumns={@ORM\JoinColumn(name="uzytkownik_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="rola_id", referencedColumnName="id")}
     *      )
     */
    private $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function setId($id){
        $this->id = $id;
    }

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
    public function getPass(): ?string {
        return $this->pass;
    }

    public function getPassword(): ?string {
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
        $stringArray = new ArrayCollection();
        foreach($this->roles as $role){
            $stringArray->add($role->getName());
        }
        return $stringArray->toArray();
    }

    public function getUsername()
    {
        return $this->getLogin();
    }

    public function setRoles(Collection $roles){
        $this->roles = $roles;
    }

    public function getUserIdentifier()
    {
        return $this->login;
    }

    public function getCollectionRoles(){
        return $this->roles;
    }
}