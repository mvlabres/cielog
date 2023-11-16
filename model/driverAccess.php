<?php

class DriverAccess{

    private $id;
    private $startDatetime;
    private $endDatetime;
    private $driver; 
    private $driverId;
    private $driverName;
    private $cpf;
    private $cnh;
    private $cnhExpiration;
    private $shippingCompany;
    private $businessId;
    private $businessMame;
    private $vehicleType;
    private $vehiclePlate;
    private $vehiclePlate2;
    private $vehiclePlate3;
    private $inboundInvoice;
    private $outboundInvoice;
    private $operationType;
    private $userOutboundId;
    private $userOutboundName;
    private $driverStatus;
    private $driverBlockReason;
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

    public function setDriver($driver){
        $this->driver = $driver;
    }
    public function getDriver(){
        return $this->driver;
    }

    public function setDriverId($driverId){
        $this->driverId = $driverId;
    }
    public function getDriverId(){
        return $this->driverId;
    }

    public function setDriverName($driverName){
        $this->driverName = $driverName;
    }
    public function getDriverName(){
        return $this->driverName;
    }

    public function setCpf($cpf){
        $this->cpf = $cpf;
    }
    public function getCpf(){
        return $this->cpf;
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

    public function setInboundInvoice($inboundInvoice){
        $this->inboundInvoice = $inboundInvoice;
    }
    public function getInboundInvoice(){
        return $this->inboundInvoice;
    }

    public function setOutboundInvoice($outboundInvoice){
        $this->outboundInvoice = $outboundInvoice;
    }
    public function getOutboundInvoice(){
        return $this->outboundInvoice;
    }

    public function setOperationType($operationType){
        $this->operationType = $operationType;
    }
    public function getOperationType(){
        return $this->operationType;
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

    public function setDriverStatus($driverStatus){
        $this->driverStatus = $driverStatus;
    }
    public function getDriverStatus(){
        return $this->driverStatus;
    }

    public function setDriverBlockReason($driverBlockReason){
        $this->driverBlockReason = $driverBlockReason;
    }
    public function getDriverBlockReason(){
        return $this->driverBlockReason;
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