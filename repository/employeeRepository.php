<?php

include_once('../model/errorHandler.php');

class EmployeeRepository{

    private $mySql;
    private $standardQuery = 'SELECT employee.id,employee.name,registration,cpf,vehicle,vehicle_plate,employee.created_date,employee.modified_date,employee.created_by,employee.modified_by,business_id,client.name AS business_name
                              FROM employee
                              INNER JOIN client ON (business_id = client.id || isnull(business_id) )';

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findAll(){

        try{
            $sql = $this->standardQuery . ' GROUP BY employee.id ORDER BY employee.name';
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar colaboradores - ', true, $ex->getMessage());
        }
    }

    public function findByCpf($cpf){

        try{
            $sql = $this->standardQuery . 'WHERE cpf = "'.$cpf.'" ';
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar colaborador - ', true, $ex->getMessage());
        }
    }

    public function findAllToSearch(){

        try{
            $sql = 'SELECT id,name,cpf FROM employee ORDER BY name';
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar colaboradores - ', true, $ex->getMessage());
        }
    }

    public function findById($id){

        try{
            $sql = $this->standardQuery . 'WHERE employee.id = '.$id.' ORDER BY employee.name';
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar colaboradores - ', true, $ex->getMessage());
        }
    }

    public function findByBusinessId($businessId){

        try{
            $sql = $this->standardQuery . 'WHERE business_id = '.$businessId.' ORDER BY employee.name';
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar colaboradores - ', true, $ex->getMessage());
        }
    }

    public function save($employee){

        try {
            $sql = 'INSERT INTO employee (name,registration,cpf,vehicle,vehicle_plate,business_id,created_date,created_by)
            VALUES(
                "'.$employee->getName().'", 
                "'.$employee->getRegistration().'", 
                "'.$employee->getCpf().'", 
                "'.$employee->getVehicle().'", 
                "'.$employee->getVehiclePlate().'", 
                '.$employee->getBusinessId().',
                "'.date("Y-m-d").'", 
                '.$_SESSION['id'].' 
            )';

            $result = $this->mySql->query($sql);
            return new ErrorHandler($this->mySql->insert_id, false, null);

        } catch (Exception $ex) {
            throw $ex;
        } 
    }

    public function update($employee){

        try {
            $sql = 'UPDATE employee 
                    SET name = "'.$employee->getName().'",
                        registration = "'.$employee->getRegistration().'",
                        cpf = "'.$employee->getCpf().'", 
                        vehicle = "'.$employee->getVehicle().'", 
                        vehicle_plate = "'.$employee->getVehiclePlate().'", 
                        business_id = '.$employee->getBusinessId().', 
                        modified_by = '.$_SESSION['id'].',
                        modified_date = "'.date("Y-m-d").'" 
                         WHERE id = '.$employee->getId();

            $result = $this->mySql->query($sql);
            return new ErrorHandler($employee->getId(), false, null);

        } catch (Exception $ex) {
            return new ErrorHandler('Erro ao atualizar Colaborador! - ', true, $ex->getMessage());
        } 
    }

    public function delete($id){

        try{
            $sql = 'DELETE FROM employee WHERE id = '.$id;
            $this->mySql->query($sql);
            return new ErrorHandler('Colaborador excluido com sucesso!', false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao excluir colaborador', true, $ex->getMessage());
        }
    }
}