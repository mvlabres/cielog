<?php

include_once('../model/errorHandler.php');

class EmployeeAccessRepository{

    private $mySql;
    private $standardQuery = 'SELECT em_a.id AS access_id,
                                    em.id AS employee_id,
                                    em.name AS employee_name,
                                    start_datetime,
                                    end_datetime,
                                    em.cpf AS employee_cpf,
                                    em.registration AS employee_registration,
                                    em.cpf AS employee_cpf,
                                    em.vehicle AS employee_vehicle,
                                    em.vehicle_plate AS employee_vehicle_plate,
                                    cl.id AS business_id,
                                    cl.name AS business_name,
                                    rotation,
                                    em_a.created_date AS access_created_date,
                                    em_a.modified_date AS access_modified_date,
                                    em_a.created_by AS access_created_by,
                                    us_2.name AS access_created_by_name,
                                    em_a.modified_by AS access_modified_by,
                                    us_3.name AS access_modified_name,
                                    us_1.id AS access_outbound_id,
                                    us_1.name AS access_outbound_name
                                FROM employee_access AS em_a 
                                INNER JOIN employee AS em ON em.id = employee_id
                                INNER JOIN client AS cl ON cl.id = em.business_id
                                INNER JOIN user AS us_1 ON us_1.id = user_outbound_id
                                INNER JOIN user AS us_2 ON us_2.id = em_a.created_by
                                INNER JOIN user AS us_3 ON (us_3.id = em_a.modified_by OR ISNULL(em_a.modified_by )) ';

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findAll(){

        try{
            $sql = $this->standardQuery . ' GROUP BY em_a.id ORDER BY start_datetime DESC';

            return new ErrorHandler($this->mySql->query($sql), false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function findByNullEndDate(){

        try{
            $sql = $this->standardQuery . " WHERE (ISNULL(end_datetime) OR end_datetime LIKE '0000-00%') GROUP BY em_a.id ORDER BY start_datetime DESC";

            return new ErrorHandler($this->mySql->query($sql), false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function findByStartDateEndDateAndBusiness($startDate, $endDate, $businessId){

        try{
            $sql = $this->standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$businessId.' GROUP BY em_a.id ORDER BY start_datetime DESC';
            return new ErrorHandler($this->mySql->query($sql), false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function findByStartDateAndEndDate($startDate, $endDate){

        try{
            $sql = $this->standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" GROUP BY em_a.id ORDER BY start_datetime DESC';
            return new ErrorHandler($this->mySql->query($sql), false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function findById($id){

        try{
            $sql = $this->standardQuery . 'WHERE em_a.id = '.$id;
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Erro ao buscar acesso - ', true, $ex->getMessage());
        }
    }

    public function save($employeeAccess){

        try {
            $sql = 'INSERT INTO employee_access (start_datetime,end_datetime,employee_id,vehicle,vehicle_plate,rotation,user_outbound_id,created_date,created_by)
            VALUES(
                "'.$employeeAccess->getStartDatetime().'", 
                "'.$employeeAccess->getEndDatetime().'", 
                '.$employeeAccess->getEmployeeId().', 
                "'.$employeeAccess->getVehicle().'", 
                "'.$employeeAccess->getVehiclePlate().'",  
                "'.$employeeAccess->getRotation().'",
                '.$_SESSION['id'].', 
                "'.date("Y-m-d").'", 
                '.$_SESSION['id'].' 
            )';

            $result = $this->mySql->query($sql);
            return new ErrorHandler('Acesso criado com sucesso!', false, null);

        } catch (Exception $ex) {
            return new ErrorHandler('Erro ao criar o acesso! - ', true, $ex->getMessage());
        } 
    }

    public function update($employeeAccess){
        try {
            $sql = 'UPDATE employee_access 
                        SET start_datetime = "'.$employeeAccess->getStartDatetime().'",
                        end_datetime = "'.$employeeAccess->getEndDatetime().'", 
                        employee_id = '.$employeeAccess->getEmployeeId().', 
                        vehicle = "'.$employeeAccess->getVehicle().'", 
                        vehicle_plate = "'.$employeeAccess->getVehiclePlate().'", 
                        rotation = "'.$employeeAccess->getRotation().'", 
                        user_outbound_id = '.$_SESSION['id'].',
                        modified_by = '.$_SESSION['id'].',
                        modified_date = "'.date("Y-m-d").'" 
                         WHERE id = '.$employeeAccess->getId();

            $result = $this->mySql->query($sql);
            return new ErrorHandler('Acesso atualizado com sucesso!', false, null);

        } catch (Exception $ex) {
            return new ErrorHandler('Erro ao atualizar acesso! - ', true, $ex->getMessage());
        } 
    }

    public function delete($id){

        try{
            $sql = 'DELETE FROM employee_access WHERE id = '.$id;
            $this->mySql->query($sql);
            return new ErrorHandler('Acesso excluido com sucesso!', false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao excluir acesso', true, $ex->getMessage());
        }
    }
}