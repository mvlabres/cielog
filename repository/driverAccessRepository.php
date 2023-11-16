<?php

include_once('../model/errorHandler.php');

class DriverAccessRepository{

    private $mySql;
    private $standardQuery = 'SELECT dr_a.id AS access_id,
                                    dr.id AS driver_id,
                                    dr.name AS driver_name,
                                    start_datetime,
                                    end_datetime,
                                    dr.cnh AS driver_cnh,
                                    dr.cnh_expiration AS driver_cnh_expiration,
                                    dr.cpf AS driver_cpf,
                                    dr.shipping_company AS driver_shipping_company,
                                    dr_a.vehicle_type AS driver_vehicle_type,
                                    dr_a.vehicle_plate AS driver_vehicle_plate,
                                    dr_a.vehicle_plate2 AS driver_vehicle_plate2,
                                    dr_a.vehicle_plate3 AS driver_vehicle_plate3,
                                    cl.id AS business_id,
                                    cl.name AS business_name,
                                    inbound_invoice,
                                    outbound_invoice,
                                    operation_type,
                                    rotation,
                                    dr.status AS driver_access_status,
                                    dr.block_reason AS driver_block_reason,
                                    dr_a.created_date AS access_created_date,
                                    dr_a.modified_date AS access_modified_date,
                                    dr_a.created_by AS access_created_by,
                                    us_2.name AS access_created_by_name,
                                    dr_a.modified_by AS access_modified_by,
                                    us_3.name AS access_modified_name,
                                    us_1.id AS access_outbound_id,
                                    us_1.name AS access_outbound_name
                              FROM driver_access AS dr_a 
                              INNER JOIN driver AS dr ON dr.id = driver_id
                              INNER JOIN client AS cl ON cl.id = business_id
                              INNER JOIN user AS us_1 ON us_1.id = user_outbound_id
                              INNER JOIN user AS us_2 ON us_2.id = dr_a.created_by
                              INNER JOIN user AS us_3 ON (us_3.id = dr_a.modified_by OR ISNULL(dr_a.modified_by )) ';

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findAll(){

        try{
            $sql = $this->standardQuery . ' GROUP BY dr_a.id ORDER BY start_datetime DESC';

            return new ErrorHandler($this->mySql->query($sql), false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function findByNullEndDate(){

        try{
            $sql = $this->standardQuery . " WHERE (ISNULL(end_datetime) OR end_datetime LIKE '0000-00%') GROUP BY dr_a.id ORDER BY start_datetime DESC";

            return new ErrorHandler($this->mySql->query($sql), false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function findByStartDateEndDateAndBusiness($startDate, $endDate, $businessId){

        try{
            $sql = $this->standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$businessId.' GROUP BY dr_a.id ORDER BY start_datetime DESC';
            return new ErrorHandler($this->mySql->query($sql), false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function findByStartDateAndEndDate($startDate, $endDate){

        try{
            $sql = $this->standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" GROUP BY dr_a.id ORDER BY start_datetime DESC';
            return new ErrorHandler($this->mySql->query($sql), false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function findById($id){

        try{
            $sql = $this->standardQuery . 'WHERE dr_a.id = '.$id;
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Erro ao buscar acesso - ', true, $ex->getMessage());
        }
    }

    public function save($driverAccess){

        try {
            $sql = 'INSERT INTO driver_access (start_datetime,end_datetime,driver_id,business_id,vehicle_type,vehicle_plate,vehicle_plate2,vehicle_plate3,inbound_invoice,outbound_invoice,operation_type,rotation,user_outbound_id,created_date,created_by)
            VALUES(
                "'.$driverAccess->getStartDatetime().'", 
                "'.$driverAccess->getEndDatetime().'", 
                '.$driverAccess->getDriverId().', 
                '.$driverAccess->getBusinessId().',
                "'.$driverAccess->getVehicleType().'", 
                "'.$driverAccess->getVehiclePlate().'", 
                "'.$driverAccess->getVehiclePlate2().'", 
                "'.$driverAccess->getVehiclePlate3().'", 
                "'.$driverAccess->getInboundInvoice().'", 
                "'.$driverAccess->getOutboundInvoice().'", 
                "'.$driverAccess->getOperationType().'", 
                "'.$driverAccess->getRotation().'",
                '.$_SESSION['id'].', 
                "'.date("Y-m-d").'", 
                '.$_SESSION['id'].' 
            )';

            echo $sql;

            $result = $this->mySql->query($sql);
            return new ErrorHandler('Acesso criado com sucesso!', false, null);

        } catch (Exception $ex) {
            return new ErrorHandler('Erro ao criar o acesso! - ', true, $ex->getMessage());
        } 
    }

    public function update($driverAccess){
        try {
            $sql = 'UPDATE driver_access 
                        SET start_datetime = "'.$driverAccess->getStartDatetime().'",
                        end_datetime = "'.$driverAccess->getEndDatetime().'", 
                        driver_id = '.$driverAccess->getDriverId().', 
                        business_id = '.$driverAccess->getBusinessId().', 
                        vehicle_type = "'.$driverAccess->getVehicleType().'", 
                        vehicle_plate = "'.$driverAccess->getVehiclePlate().'", 
                        vehicle_plate2 = "'.$driverAccess->getVehiclePlate2().'", 
                        vehicle_plate3 = "'.$driverAccess->getVehiclePlate3().'", 
                        inbound_invoice = "'.$driverAccess->getInboundInvoice().'", 
                        outbound_invoice = "'.$driverAccess->getOutboundInvoice().'", 
                        operation_type = "'.$driverAccess->getOperationType().'",
                        rotation = "'.$driverAccess->getRotation().'",
                        user_outbound_id = '.$_SESSION['id'].',
                        modified_by = '.$_SESSION['id'].',
                        modified_date = "'.date("Y-m-d").'" 
                         WHERE id = '.$driverAccess->getId();
                         
            $result = $this->mySql->query($sql);
            return new ErrorHandler('Acesso atualizado com sucesso!', false, null);

        } catch (Exception $ex) {
            return new ErrorHandler('Erro ao atualizar acesso! - ', true, $ex->getMessage());
        } 
    }

    public function delete($id){

        try{
            $sql = 'DELETE FROM driver_access WHERE id = '.$id;
            $this->mySql->query($sql);
            return new ErrorHandler('Acesso excluido com sucesso!', false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao excluir acesso', true, $ex->getMessage());
        }
    }
}