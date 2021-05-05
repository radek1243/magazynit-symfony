<?php
namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProtocolRepository")
 * @ORM\Table(name = "protokol")
 */
class Protocol
{
    
    /**
     * @ORM\Column(type = "integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy = "AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity = "Location")
     * @ORM\JoinColumn(name = "lok_id", referencedColumnName = "id")
     */
    private $location;
    
    /**
     * @ORM\ManyToOne(targetEntity = "User")
     * @ORM\JoinColumn(name = "uzytkownik_id", referencedColumnName = "id")
     */
    private $user;
    
    /**
     * @ORM\Column(name = "osoba", type = "string", length = 100)
     */
    private $person;
    
    /**
     * @ORM\Column(name = "poz_urz", type = "string", length = 255)
     */
    private $restDevices;
    
    /**
     * @ORM\Column(name = "data", type = "date")
     */
    private $date;
    
    /**
     * @ORM\Column(name = "wro", type = "boolean")
     */
    private $returned;
    
    /**
     * 
     * @ORM\Column(name = "zlecajacy", type = "string", length = 30)
     */
    private $principalOld;
    
    /**
     * @ORM\ManyToMany(targetEntity = "Device")
     * @ORM\JoinTable(name = "prot_urz",
     *      joinColumns={@ORM\JoinColumn(name = "protokol_id", referencedColumnName = "id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name = "urzadzenie_id", referencedColumnName = "id")}
     *      )
     */
    private $devices;
    
    /**
     * 
     * @ORM\Column(name = "typ", type = "string", length = 1)
     */
    private $type;
    
    /**
     * 
     * @ORM\ManyToOne(targetEntity = "Person")
     * @ORM\JoinColumn(name = "przekazujacy_id", referencedColumnName = "id")
     */
    private $sender;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity = "Person")
     * @ORM\JoinColumn(name = "zlecajacy_id", referencedColumnName = "id")
     */
    private $principal;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity = "Person")
     * @ORM\JoinColumn(name = "odbiorca_id", referencedColumnName = "id")
     */
    private $receiver;    

    public function __construct(){
        $this->devices = new ArrayCollection();
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
     * Set Id
     * @param integer
     */
    public function setId($id){
        $this->id = $id;
    }
    
    /**
     * Get Location
     * @return Location
     */
    public function getLocation(): Location
    {
        return $this->location;
    }

    /**
     * Set Location
     * @param Location $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * Get User
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Set User
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Get Person
     * @return string
     */
    public function getPerson(): ?string
    {
        return $this->person;
    }

    /**
     * Set Person
     * @param string $person
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }

    /**
     * Get Rest Devices
     * @return string
     */
    public function getRestDevices(): string
    {
        return $this->restDevices;
    }

    /**
     * Set Rest Devices
     * @param string $rest_devices
     */
    public function setRestDevices($restDevices)
    {
        $this->restDevices = $restDevices;
    }

    /**
     * Get Date
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * Set Date
     * @param DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Get Returned
     * @return bool
     */
    public function getReturned(): bool
    {
        return $this->returned;
    }

    /**
     * Set Returned
     * @param bool $returned
     */
    public function setReturned($returned)
    {
        $this->returned = $returned;
    }    
    
    /**
     * @return mixed
     */
    public function getPrincipalOld(): ?string
    {
        return $this->principalOld;
    }

    /**
     * @param mixed $principal
     */
    public function setPrincipalOld($principalOld)
    {
        $this->principalOld = $principalOld;
    }

    /**
     * 
     * @param ArrayCollection $devices
     */
    public function setDevices($devices){
        $this->devices = $devices;
    }
    
    /**
     * 
     * @return ArrayCollection
     */
    public function getDevices(): Collection{
        return $this->devices;
    }
    
    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
    
    /**
     * @return \App\Entity\Person
     */
    public function getProtPerson()
    {
        return $this->receiver;
    }
    
    /**
     * @param \App\Entity\Person $protPerson
     */
    public function setProtPerson($protPerson)
    {
        $this->receiver = $protPerson;
    }
    
    /**
     * @return \App\Entity\Person
     */
    public function getSender()
    {
        return $this->sender;
    }
    
    /**
     * @param \App\Entity\Person $protPerson
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }
    
    /**
     * @return \App\Entity\Person
     */
    public function getReceiver()
    {
        return $this->receiver;
    }
    
    /**
     * @param \App\Entity\Person $receiver
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;
    }
    
    /**
     * @return \App\Entity\Person
     */
    public function getPrincipal()
    {
        return $this->principal;
    }
    
    /**
     * @param \App\Entity\Person $principal
     */
    public function setPrincipal($principal)
    {
        $this->principal = $principal;
    }
    
}
