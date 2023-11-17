<?php

include_once('../model/errorHandler.php');

class driverRepository{

    private $mySql;
    private $standardQuery = 'SELECT id,name,cnh,cnh_expiration,cpf,shipping_company,vehicle_type,vehicle_plate,vehicle_plate2,vehicle_plate3,record_type,status,block_reason,created_date,modified_date,created_by,modified_by 
                              FROM driver ';

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findAll(){

        try{
            $sql = $this->standardQuery . ' ORDER BY name';
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar motoristas - ', true, $ex->getMessage());
        }
    }

    public function findById($id){

        try{
            $sql = $this->standardQuery . 'WHERE id = '.$id.' ORDER BY name';
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar motoristas - ', true, $ex->getMessage());
        }
    }

    public function findByCpf($cpf){

        try{
            $sql = $this->standardQuery . 'WHERE cpf = "'.$cpf.'" ';
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar motoristas - ', true, $ex->getMessage());
        }
    }

    public function save($driver){

        try {
            $sql = 'INSERT INTO driver (name,cnh,cnh_expiration,cpf,shipping_company,vehicle_type,vehicle_plate,vehicle_plate2,vehicle_plate3,record_type,status,block_reason,created_date,created_by)
            VALUES(
                "'.$driver->getName().'", 
                "'.$driver->getCnh().'", 
                "'.$driver->getCnhExpiration().'", 
                "'.$driver->getCpf().'", 
                "'.$driver->getShippingCompany().'", 
                "'.$driver->getVehicleType().'", 
                "'.$driver->getVehiclePlate().'", 
                "'.$driver->getVehiclePlate2().'", 
                "'.$driver->getVehiclePlate3().'", 
                "'.$driver->getRecordType().'", 
                "'.$driver->getStatus().'", 
                "'.$driver->getBlockReason().'", 
                "'.date("Y-m-d").'", 
                '.$_SESSION['id'].' 
            )';

            $result = $this->mySql->query($sql);
            return new ErrorHandler($this->mySql->insert_id, false, null);

        } catch (Exception $ex) {
            return new ErrorHandler('Erro ao criar motorista! - ', true, $ex->getMessage());
        } 
    }

    public function update($driver){

        try {
            $sql = 'UPDATE driver 
                        SET name = "'.$driver->getName().'",
                        cnh = "'.$driver->getCnh().'", 
                        cnh_expiration= "'.$driver->getCnhExpiration().'", 
                        cpf = "'.$driver->getCpf().'", 
                        shipping_company = "'.$driver->getShippingCompany().'", 
                        vehicle_type = "'.$driver->getVehicleType().'", 
                        vehicle_plate = "'.$driver->getVehiclePlate().'", 
                        vehicle_plate2 = "'.$driver->getVehiclePlate2().'", 
                        vehicle_plate3 = "'.$driver->getVehiclePlate3().'", 
                        record_type = "'.$driver->getRecordType().'", 
                        status = "'.$driver->getStatus().'", 
                        block_reason = "'.$driver->getBlockReason().'",
                        modified_by = '.$_SESSION['id'].',
                        modified_date = "'.date("Y-m-d").'" 
                         WHERE id = '.$driver->getId();

            $result = $this->mySql->query($sql);
            return new ErrorHandler($driver->getId(), false, null);

        } catch (Exception $ex) {
            return new ErrorHandler('Erro ao atualizar motorista! - ', true, $ex->getMessage());
        } 
    }

    public function delete($id){

        try{
            $sql = 'DELETE FROM driver WHERE id = '.$id;
            $this->mySql->query($sql);
            return new ErrorHandler('Motorista excluido com sucesso!', false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao excluir motorista', true, $ex->getMessage());
        }
    }
}