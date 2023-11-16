<?php

class EmployeeAccess{

    private $id;
    private $startDatetime;
    private $endDatetime;
    private $employee;
    private $employeeId;
    private $userOutboundId;
    private $userOutboundName;
    private $vehicle;
    private $vehicle_plate;
    private $rotation;
    private $createdDate;
    private $modifiedDate;
    private $createdBy;
    private $createdByName;
    private $modifiedBy;

    public function setId($id){
        $this->id = $id;
    }
    public function getId(){
        return $this->id;
    }

    public function setStartDatetime($startDatetime){
        $this->startDatetime = $startDatetime;
    }
    public function getStartDatetime(){
        return $this->startDatetime;
    }

    public function setEndDatetime($endDatetime){
        $this->endDatetime = $endDatetime;
    }
    public function getEndDatetime(){
        return $this->endDatetime;
    }

    public function setEmployee($employee){
        $this->employee = $employee;
    }
    public function getEmployee(){
        return $this->employee;
    }

    public function setEmployeeId($employeeId){
        $this->employeeId = $employeeId;
    }
    public function getEmployeeId(){
        return $this->employeeId;
    }

    public function setUserOutboundId($userOutboundId){
        $this->userOutboundId = $userOutboundId;
    }
    public function getUserOutboundId(){
        return $this->userOutboundId;
    }

    public function setUserOutboundName($userOutboundName){
        $this->userOutboundName = $userOutboundName;
    }
    public function getUserOutboundName(){
        return $this->userOutboundName;
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

    public function setRotation($rotation){
        $this->rotation = $rotation;
    }
    public function getRotation(){
        return $this->rotation;
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

    public function setCreatedByName($createdByName){
        $this->createdByName = $createdByName;
    }
    public function getCreatedByName(){
        return $this->createdByName;
    }

    public function setModifiedBy($modifiedBy){
        $this->modifiedBy = $modifiedBy;
    }
    public function getModifiedBy(){
        return $this->modifiedBy;
    }
}

?>