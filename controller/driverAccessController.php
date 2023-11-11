<?php

require_once('../repository/driverAccessRepository.php');
require_once('../model/driverAccess.php');
require_once('../model/driver.php');

class DriverAccessController{

    private $driverAccess;
    private $driverAccessRepository;
    private $mySql;

    public function __construct($mySql){

        $this->mySql = $mySql;
        $this->driverAccessRepository = new DriverAccessRepository($this->mySql);
    }

    public function save($post, $action){

        try {

            $driverAccess = new DriverAccess();
            
            $driverAccess = $this->setFields($post, $driverAccess);

            if($action == 'save') return $this->driverAccessRepository->save($driverAccess);  
            else return $this->driverAccessRepository->update($driverAccess); 
            
        } catch (Exception $e) {

            $description = $e->getMessage() . '- ' . $e->getTraceAsString();
            return new ErrorHandler('Erro ao salvar dados do acesso! - ', true, $description);
        }
    }
    
    public function findAll(){

        $result = $this->driverAccessRepository->findAll();

        if($result->hasError) return $result;

        $data = $this->loadData($result->result);

        return new ErrorHandler($data, false, null);
    }

    public function delete($id){
        try {

            return $this->driverAccessRepository->delete($id);

        } catch (Exception $e) {

            $description = $e->getMessage() . '- ' . $e->getTraceAsString();
            return new ErrorHandler('Erro ao deletar registro de acesso! - ', true, $description);
        }
    }

    public function findById($id){

        $result = $this->driverAccessRepository->findById($id);

        if($result->hasError) return $result;

        $data = $this->loadData($result->result);

        if(count($data) > 0) return new ErrorHandler($data[0], false, null);
        else return new ErrorHandler(new DriverAccess(), false, null);
    }
    
    public function setFields($post, $driverAccess){

        if($post['accessId'] && $post['accessId'] != null) $driverAccess->setId($post['accessId']);
        $driverAccess->setStartDatetime(date("Y-m-d H:i", strtotime(str_replace('/', '-', $post['startDate'] ))));

        if(!is_null($post['endDate'])){
            $driverAccess->setEndDatetime(date("Y-m-d H:i", strtotime(str_replace('/', '-', $post['endDate'] ))));
        }

        $driverAccess->setDriverId($post['driverId'] );
        $driverAccess->setVehicleType($post['vehicleType']);
        $driverAccess->setVehiclePlate($post['vehiclePlate']);
        $driverAccess->setVehiclePlate2($post['vehiclePlate2']);
        $driverAccess->setVehiclePlate3($post['vehiclePlate3']);
        $driverAccess->setBusinessId($post['business']);
        $driverAccess->setInboundInvoice($post['inboundInvoice']);
        $driverAccess->setOutboundInvoice($post['outboundInvoice']);
        $driverAccess->setOperationType($post['operationType']);

        return $driverAccess;
    }

    public function loadData($records){

        $driverAccessList = array();

        while ($data = $records->fetch_assoc()){ 
            $driverAccess = new DriverAccess();
            $driverAccess->setId($data['access_id']);
            $driverAccess->setStartDatetime(date("d/m/Y H:i", strtotime($data['start_datetime'])));

            if(!is_null($data['end_datetime']) && !str_contains($data['end_datetime'], '0000')){
                $driverAccess->setEndDatetime( date("d/m/Y H:i", strtotime($data['end_datetime'])));
            }

            $driverAccess->setDriverId($data['driver_id']);
            $driverAccess->setDriverName($data['driver_name']);
            $driverAccess->setCpf($data['driver_cpf']);
            $driverAccess->setCnh($data['driver_cnh']);

            if(!is_null($data['driver_cnh_expiration']) && !str_contains($data['driver_cnh_expiration'], '0000')){
                $driverAccess->setCnhExpiration(date("d/m/Y", strtotime($data['driver_cnh_expiration'])));
            }

            $driverAccess->setShippingCompany($data['driver_shipping_company']);
            $driverAccess->setVehicleType($data['driver_vehicle_type']);
            $driverAccess->setVehiclePlate($data['driver_vehicle_plate']);
            $driverAccess->setVehiclePlate2($data['driver_vehicle_plate2']);
            $driverAccess->setVehiclePlate3($data['driver_vehicle_plate3']);
            $driverAccess->setBusinessId($data['business_id']);
            $driverAccess->setBusinessName($data['business_name']);
            $driverAccess->setInboundInvoice($data['inbound_invoice']);
            $driverAccess->setOutboundInvoice($data['outbound_invoice']);
            $driverAccess->setOperationType($data['operation_type']);
            $driverAccess->setUserOutboundId($data['access_outbound_id']);
            $driverAccess->setUserOutboundName($data['access_outbound_name']);
            $driverAccess->setDriverStatus($data['driver_access_status']);
            $driverAccess->setDriverBlockReason($data['driver_block_reason']);
            $driverAccess->setCreatedDate($data['access_created_date']);
            $driverAccess->setModifiedDate($data['access_modified_date']);
            $driverAccess->setCreatedBy($data['access_created_by']);
            $driverAccess->setCreatedByName($data['blockReason']);
            $driverAccess->setModifiedBy($data['access_modified_by']);

            //popular motorista
            $driver = new Driver();
            $driver->setId($data['driver_id']);
            $driver->setName($data['driver_name']);
            $driver->setCnh($data['driver_cnh']);
            if(!is_null($driver->getCnh())){
                $driver->setCnhExpiration(date("d/m/Y", strtotime($data['driver_cnh_expiration'])));
            }
            $driver->setCpf($data['driver_cpf']);
            $driver->setShippingCompany($data['driver_shipping_company']);
            $driver->setVehicleType($data['driver_vehicle_type']);
            $driver->setVehiclePlate($data['driver_vehicle_plate']);
            $driver->setVehiclePlate2($data['driver_vehicle_plate2']);
            $driver->setVehiclePlate3($data['driver_vehicle_plate3']);
            $driver->setStatus($data['driver_access_status']);
            $driver->setBlockReason($data['driver_block_reason']);

            $driverAccess->setDriver($driver);
            
            array_push($driverAccessList, $driverAccess);
        }

        return $driverAccessList;
    }
}

?>