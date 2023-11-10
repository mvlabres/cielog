<?php

include_once('../model/errorHandler.php');

class ClientRepository{

    private $mySql;
    private $standardQuery = 'SELECT id,name,created_date,modified_date,created_by,modified_by 
                              FROM client';

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findAll(){

        try{
            $sql = $this->standardQuery;
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar empresas', true, $ex->getMessage());
        }
    }

    public function findById($id){

        try{
            $sql = $this->standardQuery . 'WHERE id = '.$id;
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar empresa', true, $ex->getMessage());
        }
    }

    public function findByName($name){

        try{
            $sql = $this->standardQuery . ' WHERE name = "'.$name.'"';
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar empresa', true, $ex->getMessage());
        }
    }

    public function save($client){

        try {
            $sql = 'INSERT INTO client (name,created_date,created_by)
                    VALUES("'.$client->getName().'", "'.date("Y-m-d").'", '.$_SESSION['id'].')';

            $result = $this->mySql->query($sql);
            return new ErrorHandler('Empresa criada com sucesso!', false, null);

        } catch (Exception $ex) {
            return new ErrorHandler('Erro ao criar empresa!', true, $ex->getMessage());
        } 
    }

    public function update($client){

        try {
            $sql = 'UPDATE client
                    SET name = "'.$client->getName().'"  
                    WHERE id = '.$client->getId();

            $result = $this->mySql->query($sql);
            return new ErrorHandler('Empresa atualizada com sucesso!', false, null);

        } catch (Exception $ex) {
            return new ErrorHandler('Erro ao atualizar empresa!', true, $ex->getMessage());
        } 
    }

    public function delete($id){

        try{
            $sql = 'DELETE FROM client WHERE id = '.$id;
            $this->mySql->query($sql);
            return new ErrorHandler('Empresa excluida com sucesso!', false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao excluir empresa', true, $ex->getMessage());
        }
    }
}