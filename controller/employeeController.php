<?php

require_once('../repository/employeeRepository.php');
require_once('../model/employee.php');

class EmployeeController{

    private $employee;
    private $employeeRepository;
    private $mySql;

    public function __construct($mySql){

        $this->mySql = $mySql;
        $this->employeeRepository = new EmployeeRepository($this->mySql);
    }

    public function save($post, $action){

        try {

            $employee = new Employee();
            
            $employee = $this->setFields($post, $employee);

            if($action == 'save') return $this->employeeRepository->save($employee);  
            else return $this->employeeRepository->update($employee); 
            
        } catch (Exception $e) {

            $description = $e->getMessage() . '- ' . $e->getTraceAsString();
            $description = str_replace('\'', '"', $description);

            return new ErrorHandler('Erro ao criar colaborador! - ', true, $description);
        }
    }
    
    public function findAll(){

        $result = $this->employeeRepository->findAll();

        if($result->hasError) return $result;

        $data = $this->loadData($result->result);

        return new ErrorHandler($data, false, null);
    }

    public function delete($id){
        try {

            return $this->employeeRepository->delete($id);

        } catch (Exception $e) {

            $description = $e->getMessage() . '- ' . $e->getTraceAsString();
            return new ErrorHandler('Erro ao deletar o colaborador!', true, $description);
        }
    }

    public function findById($id){

        $result = $this->employeeRepository->findById($id);

        if($result->hasError) return $result;

        $data = $this->loadData($result->result);

        if(count($data) > 0) return new ErrorHandler($data[0], false, null);
        else return new ErrorHandler(new Employee(), false, null);
    }

    public function findByBusinessId($businessId){

        $result = $this->employeeRepository->findByBusinessId($businessId);

        if($result->hasError) return $result;

        $data = $this->loadData($result->result);
        return new ErrorHandler($data, false, null);
    }

    
    public function setFields($post, $employee){

        if($post['id'] && $post['id'] != null) $employee->setId($post['id']);
        $employee->setName($post['name']);
        $employee->setRegistration($post['registration']);
        $employee->setCpf($post['cpf']);
        $employee->setVehicle($post['vehicle']);
        $employee->setVehiclePlate(strtoupper($post['vehiclePlate']));
        $employee->setBusinessId($post['business']);
        
        return $employee;
    }

    public function loadData($records){

        $employees = array();

        while ($data = $records->fetch_assoc()){ 
            $employee = new Employee();
            $employee->setId($data['id']);
            $employee->setName($data['name']);
            $employee->setRegistration($data['registration']);
            $employee->setCpf($data['cpf']);
            $employee->setVehicle($data['vehicle']);
            $employee->setVehiclePlate($data['vehicle_plate']);
            $employee->setBusinessId($data['business_id']);
            $employee->setBusinessName($data['business_name']);
            $employee->setCreatedDate(date("d/m/Y", strtotime($data['created_date'])));
            $employee->setModifiedDate(date("d/m/Y", strtotime($data['modified_date'])));
            $employee->setCreatedBy( $data['created_by']);
            $employee->setModifiedBy($data['modified_by']);
            
            array_push($employees, $employee);
        }

        return $employees;
    }
}

?>