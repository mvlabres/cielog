<?php

include_once('../model/errorHandler.php');

class shippingCompanyRepository{

    private $mySql;
    private $standardQuery = 'SELECT id,name,created_date,modified_date,created_by,modified_by 
                              FROM shipping_company ';

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findAll(){

        try{
            $sql = $this->standardQuery . ' ORDER BY name';
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar transportadoras - ', true, $ex->getMessage());
        }
    }

    public function findById($id){

        try{
            $sql = $this->standardQuery . 'WHERE id = '.$id.' ORDER BY name';
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar transportadoras - ', true, $ex->getMessage());
        }
    }

    public function save($shippingCompany){

        try {
            $sql = 'INSERT INTO shipping_company (name,created_date,created_by)
            VALUES(
                "'.$shippingCompany->getName().'", 
                "'.date("Y-m-d").'", 
                '.$_SESSION['id'].' 
            )';

            $result = $this->mySql->query($sql);
            return new ErrorHandler('Transportadora criada com sucesso!', false, null);

        } catch (Exception $ex) {
            return new ErrorHandler('Erro ao criar transportadora! - ', true, $ex->getMessage());
        } 
    }

    public function update($shippingCompany){

        try {
            $sql = 'UPDATE shipping_company 
                    SET name = "'.$shippingCompany->getName().'",
                        modified_by = '.$_SESSION['id'].',
                        modified_date = "'.date("Y-m-d").'" 
                         WHERE id = '.$shippingCompany->getId();

            $result = $this->mySql->query($sql);
            return new ErrorHandler('Transportadora atualizado com sucesso!', false, null);

        } catch (Exception $ex) {
            return new ErrorHandler('Erro ao atualizar Transportadora! - ', true, $ex->getMessage());
        } 
    }

    public function delete($id){

        try{
            $sql = 'DELETE FROM shipping_company WHERE id = '.$id;
            $this->mySql->query($sql);
            return new ErrorHandler('Transportadora excluido com sucesso!', false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao excluir Transportadora', true, $ex->getMessage());
        }
    }
}