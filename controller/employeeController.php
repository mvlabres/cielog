<?php

require_once('../repository/employeeRepository.php');
require_once('../model/employee.php');

class EmployeeController{

    private $employee;
    private $employeeRepository;
    private $mySql;
    public $defaultImagePath = '../image-profile/employee/id-00';

    public function __construct($mySql){

        $this->mySql = $mySql;
        $this->employeeRepository = new EmployeeRepository($this->mySql);
    }

    public function save($post, $action){

        $recordId = null;

        try {

            $employee = new Employee();
            
            $employee = $this->setFields($post, $employee);

            if($action == 'save') {

                $result = $this->employeeRepository->findByCpf($employee->getCpf());

                if($result->result->num_rows > 0) return new ErrorHandler('Já existe um registro com esse CPF', true, '');
        
                if($post['redirect'] == 'redirect') {
                    $result = $this->employeeRepository->save($employee); 
                    $recordId = $result->result;  
                }else{
                    $result = $this->employeeRepository->save($employee);
                    $recordId = $result->result;
                    $result->result = 'Registro salvo com sucesso!';
                }
            }
            else {
                if($post['redirect'] == 'redirect') {
                    $result = $this->employeeRepository->update($employee);
                    $recordId = $result->result;

                }else {
                    $result = $this->employeeRepository->update($employee);
                    $recordId = $result->result;
                    $result->result = 'Registro atualizado com sucesso!';
                }
            }

            if($post['image-profile'] != null){

                try {
                    $this->saveBase64Image($post['image-profile'], $recordId);
                } catch (Exception $ex) {
                    throw $ex;
                }  
            }

            if($post['redirect'] == 'redirect') echo '<script>window.location="index.php?content=newEmployeeAccess.php&employeeId='.$result->result.'"</script>';
            else return $result;
            
        } catch (Exception $e) {

            $description = $e->getMessage() . '- ' . $e->getTraceAsString();
            $description = str_replace('\'', '"', $description);

            return new ErrorHandler('Erro ao criar colaborador! - ', true, $description);
        }
    }
    
    public function findAll(){

        $result = $this->employeeRepository->findAll();

        if($result->hasError) return $result;

        $data = $this->loadData($result->result, false);

        return new ErrorHandler($data, false, null);
    }

    public function findAllToSearch(){

        $result = $this->employeeRepository->findAllToSearch();

        if($result->hasError) return $result;

        $data = $this->loadDataToSerch($result->result);

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

        $data = $this->loadData($result->result, true);

        if(count($data) > 0) return new ErrorHandler($data[0], false, null);
        else return new ErrorHandler(new Employee(), false, null);
    }

    public function findByBusinessId($businessId){

        $result = $this->employeeRepository->findByBusinessId($businessId);

        if($result->hasError) return $result;

        $data = $this->loadData($result->result, false);
        return new ErrorHandler($data, false, null);
    }

    
    public function setFields($post, $employee){

        if($post['id'] && $post['id'] != null) $employee->setId($post['id']);
        $employee->setName($post['name']);
        $employee->setRegistration($post['registration']);
        $employee->setCpf($post['cpf']);
        $employee->setVehicle($post['vehicle']);
        $employee->setVehiclePlate(mb_strtoupper($post['vehiclePlate']));
        $employee->setBusinessId($post['business']);
        
        return $employee;
    }

    public function loadData($records, $withFile){

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

            if($withFile) $employee = $this->setImagePath($employee);
            
            array_push($employees, $employee);
        }

        return $employees;
    }

    public function loadDataToSerch($records){

        $employee = '';

        while ($data = $records->fetch_assoc()){ 
            $employee .= $data['id'];
            $employee .= ';' . $data['name'];
            $employee .= ';' .$data['cpf'];

            $employee.= '|';
        }

        $employee.= '|';

        return rtrim($employee, '|');
    }

    
    public function setImagePath($employee){
        
        $filename = $this->defaultImagePath.$employee->getId().'.png';

        if (file_exists($filename)) $employee->setImageProfilePath($filename);
        else $employee->setImageProfilePath('../images/profile.jpg');

        return $employee;
    }

    public function saveBase64Image($base64, $recordId){
        $base64 = str_replace('data:image/png;base64,', '', $base64);
        $base64 = str_replace(' ', '+', $base64);
        $data = base64_decode($base64);
        file_put_contents($this->defaultImagePath.$recordId.'.png', $data);
    }
}

?>