<?php

require_once('../repository/driverAccessRepository.php');
require_once('../model/driverAccess.php');

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

        if($post['id'] && $post['id'] != null) $driverAccess->setId($post['id']);
        $driverAccess->setStartDatetime(date("Y-m-d H:i", strtotime(str_replace('/', '-', $post['startDate'] ))));
        $driverAccess->setEndDatetime(date("Y-m-d H:i", strtotime(str_replace('/', '-', $post['endDate'] ))));
        $driverAccess->setDriverId($post['driverId'] );
        $driverAccess->setVehicleType($post['vehicleType']);
        $driverAccess->setVehiclePlate($post['vehiclePlate']);
        $driverAccess->setVehiclePlate2($post['vehiclePlate2']);
        $driverAccess->setVehiclePlate3($post['vehiclePlate3']);
        $driverAccess->setBusinessId($post['blockReason']);
        $driverAccess->setInboundInvoice($post['inboundInvoice']);
        $driverAccess->setOutboundInvoice($post['outboundInvoice']);
        $driverAccess->setOperationType($post['operationType']);

        return $driver;
    }

    public function loadData($records){

        $driverAccessList = array();

        while ($data = $records->fetch_assoc()){ 
            $driverAccess = new DriverAccess();
            $driverAccess->setId($data['access_id']);
            $driverAccess->setStartDatetime(date("d/m/Y HH:mm", strtotime($data['start_datetime'])));
            $driverAccess->setEndDatetime( date("d/m/Y HH:mm", strtotime($data['end_datetime'])));
            $driverAccess->setDriverId($data['driver_id']);
            $driverAccess->setDriverName($data['driver_name']);
            $driverAccess->setCpf($data['driver_cpf']);
            $driverAccess->setCnh($data['driver_cnh']);
            $driverAccess->setCnhExpiration(date("d/m/Y", strtotime($data['driver_cnh_expiration'])));
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
            
            array_push($driverAccessList, $driverAccess);
        }

        return $driverAccessList;
    }
}

?>