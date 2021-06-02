<?php
namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DeviceRepository")
 * @ORM\Table(name = "urzadzenie")
 */
class Device
{
    
    public function __construct(){
        $this->service = false;
        //$this->reservation = false;
        $this->fv = false;
        $this->utilization = false;
    }
    
    /**
     * @ORM\Column(type = "integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy = "AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity = "Type")
     * @ORM\JoinColumn(name = "typ_id", referencedColumnName = "id")
     */
    private $type;
    
    /**
     * @ORM\ManyToOne(targetEntity = "Model")
     * @ORM\JoinColumn(name = "model_id", referencedColumnName = "id")
     */
    private $model;
    
    /**
     * @ORM\ManyToOne(targetEntity = "Location")
     * @ORM\JoinColumn(name = "lok_id", referencedColumnName = "id")
     */
    private $location;
    
    /**
     * 
     * @ORM\Column(type = "string", length = 30, unique = true)
     */
    private $sn;
    
    /**
     *
     * @ORM\Column(type = "string", length = 30, nullable = true)
     */
    private $sn2;
    
    /**
     * @ORM\Column(name = "stan", type = "string", length = 1)
     */
    private $state;
    
    /**
     * @ORM\Column(name = "serwis", type = "boolean")
     */
    private $service;
    
    /**
     * @ORM\Column(name = "opis", type = "string", length = 255, nullable = true)
     */
    private $desc;
    
    /*
     * @ORM\Column(name = "rez", type = "boolean")
     */
    //private $reservation;
    
    /**
     * @ORM\Column(type = "boolean")
     */
    private $fv;
    
    /**
     * @ORM\Column(name = "utyl", type = "boolean")
     */
    private $utilization;
    
    /**
     * @ORM\Column(name = "czas_op", type = "datetime", options = {"default" : "CURRENT_TIMESTAMP"})
     */
    private $operationTime;
    
    /**
     * @ORM\Column(name = "czy_pod_fak", type = "boolean")
     */
    private $invoicing;
    
    /**
     * @ORM\ManyToOne(targetEntity = "Person")
     * @ORM\JoinColumn(name = "osoba_id", referencedColumnName = "id")
     */
    private $person;
    
    /**
     * Get Id
     * 
     * @return integer
     */
    public function getId(): int{
        return $this->id;
    }
    
    /**
     * Set Type
     * 
     * @param Type $type
     */
    public function setType($type){
        $this->type = $type;
    }
    
    /**
     * Get Type
     * 
     * @return Type
     */
    public function getType(): ?Type{
        return $this->type;
    }
    
    /**
     * Set Model
     *
     * @param Model $model
     */
    public function setModel($model){
        $this->model=$model;
    }
    
    /**
    * Get Model
    *
    * @return Model
    */
    public function getModel(): Model{
        return $this->model;
    }
    
    /**
     * Set Location
     * 
     * @param Location $location
     */
    public function setLocation($location){
        $this->location=$location;
    }
    
    /**
     * Get Location
     * 
     * @return Location
     */
    public function getLocation(): Location{
        return $this->location;
    }
    
    /**
     * Set SN
     * 
     * @param string $sn
     */
    public function setSN($sn) {
        $this->sn = $sn;
    }
    
    /**
     * Get SN
     * 
     * @return string
     */
    public function getSN(): string {
        return $this->sn;
    }
    
    /**
     * Set SN2
     *
     * @param string $sn2
     */
    public function setSN2($sn2) {
        $this->sn2 = $sn2;
    }
    
    /**
     * Get SN2
     *
     * @return string
     */
    public function getSN2(): ?string {
        return $this->sn2;
    }
    
    /**
     * Set State
     *
     * @param string $state
     */
    public function setState($state) {
        $this->state = $state;
    }
    
    /**
     * Get State
     *
     * @return string
     */
    public function getState(): string {
        return $this->state;
    }
    
    /**
     * Set Service
     *
     * @param boolean $service
     */
    public function setService($service) {
        $this->service = $service;
    }
    
    /**
     * Get Service
     *
     * @return boolean
     */
    public function getService(): bool {
        return $this->service;
    }
    
    /**
     * Set Description
     *
     * @param string|null $desc
     */
    public function setDesc($desc) {
        $this->desc = $desc;
    }
    
    /**
     * Get Description
     *
     * @return string|null
     */
    public function getDesc(): ?string {
        return $this->desc;
    }
    
    /*
    * Set Reservation
    *
    * @param boolean $reservation
    */
    /*public function setReservation($reservation) {
        $this->reservation = $reservation;
    }*/
    
    /*
     * Get Reservation
     *
     * @return boolean
     */
    /*public function getReservation(): bool {
        return $this->reservation;
    }*/
    
    /**
    * Set FV
    *
    * @param boolean $fv
    */
    public function setFV($fv) {
        $this->fv = $fv;
    }
    
    /**
     * Get FV
     *
     * @return boolean
     */
    public function getFV(): bool {
        return $this->fv;
    }
    
    /**
    * Set Utilization
    *
    * @param boolean $utilization
    */
    public function setUtilization($utilization) {
        $this->utilization = $utilization;
    }
    
    /**
     * Get Utilization
     *
     * @return boolean
     */
    public function getUtilization(): bool {
        return $this->utilization;
    }
    
    /**
    * Set Operation Time
    *
    * @param DateTime $operation_time
    */
    public function setOperationTime($operation_time) {
        $this->operationTime = $operation_time;
    }
    
    /**
     * Get Operation Time
     *
     * @return DateTime
     */
    public function getOperationTime(): DateTime {
        return $this->operationTime;
    }
    
    /**
     * Set Invoicing
     *
     * @param boolean $invoicing
     */
    public function setInvoicing($invoicing) {
        $this->invoicing = $invoicing;
    }
    
    /**
     * Get Invoicing
     *
     * @return boolean
     */
    public function getInvoicing(): bool {
        return $this->invoicing;
    }
    
    public function getPerson(){
        return $this->person;
    }
    
    public function setPerson($person){
        $this->person = $person;
    }

    public function getModelName(){
        return $this->getModel()->getName();
    }

    public function getTypeName(){
        return $this->getType()->getName();
    }

    public function getLocationName(){
        return $this->getLocation()->getName();
    }

    public function getLocationShortName(){
        return $this->getLocation()->getShortName();
    }
}

