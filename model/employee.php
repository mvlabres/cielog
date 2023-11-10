<?php

class Employee{

    private $id;
    private $name;
    private $registration;
    private $cpf;
    private $businessId;
    private $businessName;
    private $vehicle;
    private $vehiclePlate;
    private $createdDate;
    private $modifiedDate;
    private $createdBy;
    private $modifiedBy;
    
    public function setId($id){
        $this->id = $id;
    }
    public function getId(){
        return $this->id;
    }

    public function setName($name){
        $this->name = $name;
    }
    public function getName(){
        return $this->name;
    }

    public function setRegistration($registration){
        $this->registration = $registration;
    }
    public function getRegistration(){
        return $this->registration;
    }

    public function setCpf($cpf){
        $this->cpf = $cpf;
    }
    public function getCpf(){
        return $this->cpf;
    }

    public function setBusinessId($businessId){
        $this->businessId = $businessId;
    }
    public function getBusinessId(){
        return $this->businessId;
    }

    public function setBusinessName($businessName){
        $this->businessName = $businessName;
    }
    public function getBusinessName(){
        return $this->businessName;
    }

    public function setVehicle($vehicle){
        $this->vehicle = $vehicle;
    }
    public function getVehicle(){
        return $this->vehicle;
    }

    public function setVehiclePlate($vehiclePlate){
        $this->vehiclePlate = $vehiclePlate;
    }
    public function getVehiclePlate(){
        return $this->vehiclePlate;
    }

    public function setCreatedDate($createdDate){
        $this->createdDate = $createdDate;
    }
    public function getCreatedDate(){
        return $this->createdDate;
    }

    public function setModifiedDate($modifiedDate){
        $this->modifiedDate = $modifiedDate;
    }
    public function getModifiedDate(){
        return $this->modifiedDate;
    }

    public function setCreatedBy($createdBy){
        $this->createdBy = $createdBy;
    }
    public function getCreatedBy(){
        return $this->createdBy;
    }

    public function setModifiedBy($modifiedBy){
        $this->modifiedBy = $modifiedBy;
    }
    public function getModifiedBy(){
        return $this->modifiedBy;
    }
}

?>