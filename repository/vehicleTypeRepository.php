<?php

include_once('../model/errorHandler.php');

class vehicleTypeRepository{

    private $mySql;
    private $standardQuery = 'SELECT id,name,created_date,modified_date,created_by,modified_by 
                              FROM vehicle_type ';

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findAll(){

        try{
            $sql = $this->standardQuery . ' ORDER BY name';
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Erro ao buscar tipos de veículos - ', true, $ex->getMessage());
        }
    }

    public function findById($id){

        try{
            $sql = $this->standardQuery . 'WHERE id = '.$id.' ORDER BY name';
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Erro ao buscar transportadoras - ', true, $ex->getMessage());
        }
    }

    public function save($vehicleType){

        try {
            $sql = 'INSERT INTO vehicle_type (name,created_date,created_by)
            VALUES(
                "'.$vehicleType->getName().'", 
                "'.date("Y-m-d").'", 
                '.$_SESSION['id'].' 
            )';

            $result = $this->mySql->query($sql);
            return new ErrorHandler('Tipo de veículo criada com sucesso!', false, null);

        } catch (Exception $ex) {
            return new ErrorHandler('Erro ao criar tipo de veículo! - ', true, $ex->getMessage());
        } 
    }

    public function update($vehicleType){

        try {
            $sql = 'UPDATE vehicle_type 
                    SET name = "'.$vehicleType->getName().'",
                        modified_by = '.$_SESSION['id'].',
                        modified_date = "'.date("Y-m-d").'" 
                         WHERE id = '.$vehicleType->getId();

            $result = $this->mySql->query($sql);
            return new ErrorHandler('Tipo de veículo atualizado com sucesso!', false, null);

        } catch (Exception $ex) {
            return new ErrorHandler('Erro ao atualizar Tipo de veículo! - ', true, $ex->getMessage());
        } 
    }

    public function delete($id){

        try{
            $sql = 'DELETE FROM vehicle_type WHERE id = '.$id;
            $this->mySql->query($sql);
            return new ErrorHandler('Tipo de veículo excluido com sucesso!', false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao excluir Tipo de veículo', true, $ex->getMessage());
        }
    }
}