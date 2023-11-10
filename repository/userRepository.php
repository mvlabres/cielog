<?php

include_once('../model/errorHandler.php');

class UserRepository{

    private $mySql;
    private $standardQuery = 'SELECT user.id,user.name,username,password,type,user.created_date,user.modified_date,user.created_by,user.modified_by,client_id,client.name AS client_name
                              FROM user
                              INNER JOIN client ON (client_id = client.id || isnull(client_id) )';

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findAll(){

        try{
            $sql = $this->standardQuery . ' GROUP BY user.id ORDER BY user.name';
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar usuários - ', true, $ex->getMessage());
        }
    }

    public function findById($id){

        try{
            $sql = $this->standardQuery . 'WHERE user.id = '.$id;
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar usuário - ', true, $ex->getMessage());
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
                '.$_SESSION['id'].', 
                '.$user->getClientId().'
            )';

            $result = $this->mySql->query($sql);
            return new ErrorHandler('Usuário criado com sucesso!', false, null);

        } catch (Exception $ex) {
            return new ErrorHandler('Erro ao criar usuário! - ', true, $ex->getMessage());
        } 
    }

    public function saveWithoutClient($user){

        try {
            $sql = 'INSERT INTO user (name,username,password,type,created_date,created_by)
            VALUES(
                "'.$user->getName().'", 
                "'.$user->getUserName().'", 
                "'.$user->getPassword().'", 
                "'.$user->getType().'", 
                "'.date("Y-m-d").'", 
                '.$_SESSION['id'].' 
            )';

            $result = $this->mySql->query($sql);
            return new ErrorHandler('Usuário criado com sucesso! - ', false, null);

        } catch (Exception $ex) {
            return new ErrorHandler('Erro ao criar usuário! - ', true, $ex->getMessage());
        } 
    }

    public function update($user){

        try {
            $sql = 'UPDATE user 
                    SET name = "'.$user->getName().'",
                        username = "'.$user->getUserName().'",
                        password = "'.$user->getPassword().'", 
                        type = "'.$user->getType().'", 
                        modified_by = '.$_SESSION['id'].',
                        modified_date = "'.date("Y-m-d").'", 
                        client_id = '.$user->getClientId().' 
                        WHERE id = '.$user->getId();

            $result = $this->mySql->query($sql);
            return new ErrorHandler('Usuário atualizado com sucesso!', false, null);

        } catch (Exception $ex) {
            return new ErrorHandler('Erro ao atualizar usuário! - ', true, $ex->getMessage());
        } 
    }

    public function updateWithoutClient($user){

        try {
            $sql = 'UPDATE user 
                    SET name = "'.$user->getName().'",
                        username = "'.$user->getUserName().'",
                        password = "'.$user->getPassword().'", 
                        type = "'.$user->getType().'", 
                        modified_by = '.$_SESSION['id'].',
                        modified_date = "'.date("Y-m-d").'"
                        WHERE id = '.$user->getId();

            $result = $this->mySql->query($sql);
            return new ErrorHandler('Usuário atualizado com sucesso!', false, null);

        } catch (Exception $ex) {
            return new ErrorHandler('Erro ao atualizar usuário!', true, $ex->getMessage());
        } 
    }

    public function delete($id){

        try{
            $sql = 'DELETE FROM user WHERE id = '.$id;
            $this->mySql->query($sql);
            return new ErrorHandler('Usuário excluido com sucesso!', false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao excluir usuário', true, $ex->getMessage());
        }
    }
}