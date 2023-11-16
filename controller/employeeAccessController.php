<?php

require_once('../repository/employeeAccessRepository.php');
require_once('../model/employeeAccess.php');
require_once('../model/employee.php');
require_once('employeeController.php');

class EmployeeAccessController{

    private $employeeAccess;
    private $employeeAccessRepository;
    private $employeeController;
    private $mySql;

    public function __construct($mySql){

        $this->mySql = $mySql;
        $this->employeeAccessRepository = new EmployeeAccessRepository($this->mySql);
        $this->employeeController = new EmployeeController($this->mySql);
    }

    public function save($post, $action){

        try {

            $employeeAccess = new EmployeeAccess();
            $employeeAccess = $this->setFields($post, $employeeAccess);

            if($action == 'save') return $this->employeeAccessRepository->save($employeeAccess);  
            else return $this->employeeAccessRepository->update($employeeAccess); 
            
        } catch (Exception $e) {

            $description = $e->getMessage() . '- ' . $e->getTraceAsString();
            return new ErrorHandler('Erro ao salvar dados do acesso! - ', true, $description);
        }
    }
    
    public function findAll(){

        $result = $this->employeeAccessRepository->findAll();

        if($result->hasError) return $result;

        $data = $this->loadData($result->result);

        return new ErrorHandler($data, false, null);
    }

    public function findByNullEndDate(){

        $result = $this->employeeAccessRepository->findByNullEndDate();

        if($result->hasError) return $result;

        $data = $this->loadData($result->result);

        return new ErrorHandler($data, false, null);
    }

    public function findById($id){

        $result = $this->employeeAccessRepository->findById($id);

        if($result->hasError) return $result;

        $data = $this->loadData($result->result);

        if(count($data) > 0) return new ErrorHandler($data[0], false, null);
        else return new ErrorHandler(new EmployeeAccess(), false, null);
    }

    public function findByStartDateEndDateAndBusiness($startDate, $endDate, $business){

        $startDate = date("Y-m-d H:i", strtotime(str_replace('/', '-', $startDate.' 00:00' )));
        $endDate = date("Y-m-d H:i", strtotime(str_replace('/', '-', $endDate.' 23:59' )));

        if(is_null($business) ||  $business == 'all') $result = $this->employeeAccessRepository->findByStartDateAndEndDate($startDate, $endDate);
        else $result = $this->employeeAccessRepository->findByStartDateEndDateAndBusiness($startDate, $endDate, $business); 

        if($result->hasError) return $result;

        $data = $this->loadData($result->result);
        return new ErrorHandler($data, false, null);
    }

    public function delete($id){
        try {

            return $this->employeeAccessRepository->delete($id);

        } catch (Exception $e) {

            $description = $e->getMessage() . '- ' . $e->getTraceAsString();
            return new ErrorHandler('Erro ao deletar registro de acesso! - ', true, $description);
        }
    }
    
    public function setFields($post, $employeeAccess){

        if($post['accessId'] && $post['accessId'] != null) $employeeAccess->setId($post['accessId']);
        $employeeAccess->setStartDatetime(date("Y-m-d H:i", strtotime(str_replace('/', '-', $post['startDate'] ))));

        if(!is_null($post['endDate'])){
            $employeeAccess->setEndDatetime(date("Y-m-d H:i", strtotime(str_replace('/', '-', $post['endDate'] ))));
        }

        $employeeAccess->setEmployeeId($post['employeeId'] );
        $employeeAccess->setVehicle($post['vehicle']);
        $employeeAccess->setVehiclePlate($post['vehiclePlate']);
        $employeeAccess->setRotation($post['rotation']);

        return $employeeAccess;
    }

    public function loadData($records){

        $employeeAccessList = array();

        while ($data = $records->fetch_assoc()){ 
            $employeeAccess = new EmployeeAccess();
            $employeeAccess->setId($data['access_id']);
            $employeeAccess->setStartDatetime(date("d/m/Y H:i", strtotime($data['start_datetime'])));

            if(!is_null($data['end_datetime']) && !str_contains($data['end_datetime'], '0000')){
                $employeeAccess->setEndDatetime( date("d/m/Y H:i", strtotime($data['end_datetime'])));
            }

            $employeeAccess->setEmployeeId($data['employee_id']);
            $employeeAccess->setVehicle($data['employee_vehicle']);
            $employeeAccess->setVehiclePlate($data['employee_vehicle_plate']);
            $employeeAccess->setUserOutboundId($data['access_outbound_id']);
            $employeeAccess->setUserOutboundName($data['access_outbound_name']);
            $employeeAccess->setCreatedDate($data['access_created_date']);
            $employeeAccess->setModifiedDate($data['access_modified_date']);
            $employeeAccess->setCreatedBy($data['access_created_by']);
            $employeeAccess->setCreatedByName($data['access_created_by_name']);
            $employeeAccess->setModifiedBy($data['access_modified_by']);
            $employeeAccess->setRotation($data['rotation']);

            //popular colaborador
            $employee = new Employee();
            $employee->setId($data['employee_id']);
            $employee->setName($data['employee_name']);
            $employee->setRegistration($data['employee_registration']);
            $employee->setCpf($data['employee_cpf']);
            $employee->setVehicle($data['employee_vehemployee_vehicleicle_type']);
            $employee->setVehiclePlate($data['employee_vehicle_plate']);
            $employee->setBusinessId($data['business_id']);
            $employee->setBusinessName($data['business_name']);

            $employee = $this->employeeController->setImagePath($employee);

            $employeeAccess->setEmployee($employee);
            
            array_push($employeeAccessList, $employeeAccess);
        }

        return $employeeAccessList;
    }
}

?>