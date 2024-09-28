<?php

include_once('../model/errorHandler.php');

class BusinessClientRepository{

    private $mySql;
    private $standardQuery = 'SELECT business_client.id AS business_client_id,business_client.name AS client_business_name,business_client.client_id AS business_client_client_id,client.Name AS client_name,business_client.created_date AS business_client_created_date,business_client.modified_date AS business_client_modified_date,business_client.created_by AS business_client_created_by,business_client.modified_by AS business_client_modified_by, user.Name AS user_name
                              FROM business_client
                              INNER JOIN client ON (business_client.client_id = client.id || isnull(business_client.client_id) )
                              INNER JOIN user ON (business_client.created_by = user.id || isnull(business_client.created_by) )';

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findAll(){

        try{
            $sql = $this->standardQuery . ' ORDER BY business_client.name';
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar registros - ', true, $ex->getMessage());
        }
    }

    public function findById($id){

        try{
            $sql = $this->standardQuery . 'WHERE business_client.id = '.$id;
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar registros - ', true, $ex->getMessage());
        }
    }

    public function findByNameAndClient($name, $clientId){

        try{
            $sql = $this->standardQuery . ' WHERE business_client.Name = "'.$name.'" AND business_client.client_id = '.$clientId;
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar registros - ', true, $ex->getMessage());
        }
    }

    public function findByClientId($clientId){

        try{
            $sql = $this->standardQuery . ' WHERE  business_client.client_id = '.$clientId;
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar registros - ', true, $ex->getMessage());
        }
    }

    public function save($businessClient){

        try {
            $sql = 'INSERT INTO business_client (name,client_id,created_date,created_by)
            VALUES(
                "'.$businessClient->getName().'", 
                '.$businessClient->getClientId().', 
                "'.date("Y-m-d").'", 
                '.$_SESSION['id'].'
            )';

            $result = $this->mySql->query($sql);
            return new ErrorHandler('Registro criado com sucesso!', false, null);

        } catch (Exception $ex) {
            return new ErrorHandler('Erro ao criar registro de cliente visitante! - ', true, $ex->getMessage());
        } 
    }

    public function update($businessClient){

        try {
            $sql = 'UPDATE business_client 
                    SET name = "'.$businessClient->getName().'",
                        modified_by = '.$_SESSION['id'].',
                        modified_date = "'.date("Y-m-d").'" 
                        WHERE id = '.$businessClient->getId();

            $result = $this->mySql->query($sql);
            return new ErrorHandler('Usuário atualizado com sucesso!', false, null);

        } catch (Exception $ex) {
            return new ErrorHandler('Erro ao atualizar usuário! - ', true, $ex->getMessage());
        } 
    }

    public function delete($id){

        try{
            $sql = 'DELETE FROM business_client WHERE id = '.$id;
            $this->mySql->query($sql);
            return new ErrorHandler('Registro excluido com sucesso!', false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao excluir registro', true, $ex->getMessage());
        }
    }
}