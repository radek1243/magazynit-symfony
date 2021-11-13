<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * 
 * @ORM\Entity(repositoryClass = "App\Repository\HistoryRepository")
 * @ORM\Table(name = "hist")
 *
 */
class History
{
    /**
     * @ORM\Id
     * @ORM\Column(type = "integer")
     * @ORM\GeneratedValue(strategy = "AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(name = "urz_id", type = "integer")
     */
    private $devId;
    
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
     * @ORM\Column(name = "czas_op", type = "datetime")
     */
    private $operation_time;
    
    /**
     * @ORM\Column(name = "sn", type = "string", length = 30)
     */
    private $serialNumber;
    
    /**
     * @ORM\ManyToOne(targetEntity = "Person")
     * @ORM\JoinColumn(name = "osoba_id", referencedColumnName = "id")
     */
    private $person;
    
    /**
     * @return mixed
     */
    public function getDevId()
    {
        return $this->devId;
    }

    /**
     * @param mixed $devId
     */
    public function setDevId($devId)
    {
        $this->devId = $devId;
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
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $modelId
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $locId
     */
    public function setLocation($loc)
    {
        $this->location = $loc;
    }

    /**
     * @return mixed
     */
    public function getSn2()
    {
        return $this->sn2;
    }

    /**
     * @param mixed $sn2
     */
    public function setSn2($sn2)
    {
        $this->sn2 = $sn2;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param mixed $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    /**
     * @return mixed
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * @param mixed $desc
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;
    }

    /*
     * @return mixed
     */
    /*public function getReservation()
    {
        return $this->reservation;
    }*/

    /*
     * @param mixed $reservation
     */
    /*public function setReservation($reservation)
    {
        $this->reservation = $reservation;
    }*/

    /**
     * @return mixed
     */
    public function getFv()
    {
        return $this->fv;
    }

    /**
     * @param mixed $fv
     */
    public function setFv($fv)
    {
        $this->fv = $fv;
    }

    /**
     * @return mixed
     */
    public function getUtilization()
    {
        return $this->utilization;
    }

    /**
     * @param mixed $utilization
     */
    public function setUtilization($utilization)
    {
        $this->utilization = $utilization;
    }

    /**
     * @return mixed
     */
    public function getOperation_time()
    {
        return $this->operation_time;
    }

    /**
     * @param mixed $operation_time
     */
    public function setOperation_time($operation_time)
    {
        $this->operation_time = $operation_time;
    }

    /**
     * @return mixed
     */
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    /**
     * @param mixed $serialNumber
     */
    public function setSerialNumber($serialNumber)
    {
        $this->serialNumber = $serialNumber;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function getPerson(){
        return $this->person;
    }
    
    public function setPerson($person){
        $this->person = $person;
    }
    
}

