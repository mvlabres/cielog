<?php

include_once('../model/errorHandler.php');

class UserRepository{

    private $mySql;
    private $standardQuery = 'SELECT id,name,username,password,type,created_date,modified_date,created_by,modified_by,client_id,client.name AS client_name
                              FROM user
                              INNER JOIN client ON client_id = client.id ';

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findById($id){

        try{
            $sql = $standardQuery . 'WHERE user.id = '.$id;
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar usuário', true, $ex->getMessage());
        }
    }

    public function save($user){

        try {
            $sql = 'INSERT INTO user (name,username,password,type,created_date,created_by,client_id)
            VALUES(
            "'.$user->getName().'", 
            "'.$user->getUserName().'", 
            "'.$user->getPassword().'", 
            "'.$user->getType().'", 
            "'.date("Y-m-d").'", 
            '.$user->getCreatedBy().', 
            '.$user->getClientId().'
            )';

            $result = $this->mySql->query($sql);
            return new ErrorHandler('Usuário criado com sucesso!', false, null);

        } catch (Exception $ex) {
            return new ErrorHandler('Erro ao criar usuário!', true, $ex->getMessage());
        } 
    }
}