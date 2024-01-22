<?php

class Driver{

    private $id;
    private $name;
    private $cnh;
    private $cnhExpiration;
    private $cpf;
    private $shippingCompany;
    private $vehicleType;
    private $vehiclePlate;
    private $vehiclePlate2;
    private $vehiclePlate3;
    private $recordType;
    private $status;
    private $blockReason;
    private $imageProfilePath;
    private $phone;
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

    public function setCnh($cnh){
        $this->cnh = $cnh;
    }
    public function getCnh(){
        return $this->cnh;
    }

    public function setCnhExpiration($cnhExpiration){
        $this->cnhExpiration = $cnhExpiration;
    }
    public function getCnhExpiration(){
        return $this->cnhExpiration;
    }

    public function setCpf($cpf){
        $this->cpf = $cpf;
    }
    public function getCpf(){
        return $this->cpf;
    }

    public function setShippingCompany($shippingCompany){
        $this->shippingCompany = $shippingCompany;
    }
    public function getShippingCompany(){
        return $this->shippingCompany;
    }

    public function setVehicleType($vehicleType){
        $this->vehicleType = $vehicleType;
    }
    public function getVehicleType(){
        return $this->vehicleType;
    }

    public function setVehiclePlate($vehiclePlate){
        $this->vehiclePlate = $vehiclePlate;
    }
    public function getVehiclePlate(){
        return $this->vehiclePlate;
    }

    public function setVehiclePlate2($vehiclePlate2){
        $this->vehiclePlate2 = $vehiclePlate2;
    }
    public function getVehiclePlate2(){
        return $this->vehiclePlate2;
    }

    public function setVehiclePlate3($vehiclePlate3){
        $this->vehiclePlate3 = $vehiclePlate3;
    }
    public function getVehiclePlate3(){
        return $this->vehiclePlate3;
    }

    public function setRecordType($recordType){
        $this->recordType = $recordType;
    }
    public function getRecordType(){
        return $this->recordType;
    }

    public function setStatus($status){
        $this->status = $status;
    }
    public function getStatus(){
        return $this->status;
    }

    public function setBlockReason($blockReason){
        $this->blockReason = $blockReason;
    }
    public function getBlockReason(){
        return $this->blockReason;
    }

    public function setCreatedDate($createdDate){
        $this->createdDate = $createdDate;
    }
    public function getCreatedDate(){
        return $this->createdDate;
    }

    public function setImageProfilePath($imageProfilePath){
        $this->imageProfilePath = $imageProfilePath;
    }
    public function getImageProfilePath(){
        return ($this->imageProfilePath != null) ? $this->imageProfilePath : '../images/profile.jpg';
    }

    public function setPhone($phone){
        $this->phone = $phone;
    }
    public function getPhone(){
        return $this->phone;
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