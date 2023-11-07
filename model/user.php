<?php

class User{

    private $id;
    private $name;
    private $username;
    private $password;
    private $type;
    private $createdDate;
    private $modifiedDate;
    private $createdBy;
    private $modifiedBy;
    private $clientId;
    private $clientName;
    
    public function setId($id){
        $this->id = $id;
    }
    public function getId(){
        return $this->id;
    }

    public function setName($name){
        $this->name = $name;
    }
    public function getName(){
        return $this->name;
    }

    public function setUsername($username){
        $this->username = $username;
    }
    public function getUsername(){
        return $this->username;
    }

    public function setPassword($password){
        $this->password = $password;
    }
    public function getPassword(){
        return $this->password;
    }

    public function setType($type){
        $this->type = $type;
    }
    public function getType(){
        return $this->type;
    }

    public function setCreatedDate($createdDate){
        $this->createdDate = $createdDate;
    }
    public function getCreatedDate(){
        return $this->createdDate;
    }

    public function setModifiedDate($modifiedDate){
        $this->modifiedDate = $modifiedDate;
    }
    public function getModifiedDate(){
        return $this->modifiedDate;
    }

    public function setCreatedBy($createdBy){
        $this->createdBy = $createdBy;
    }
    public function getCreatedBy(){
        return $this->createdBy;
    }

    public function setModifiedBy($modifiedBy){
        $this->modifiedBy = $modifiedBy;
    }
    public function getModifiedBy(){
        return $this->modifiedBy;
    }
    
    public function setClientId($clientId){
        $this->clientId = $clientId;
    }
    public function getClientId(){
        return $this->clientId;
    }

    public function setClientName($clientName){
        $this->clientName = $clientName;
    }
    public function getClientName(){
        return $this->clientName;
    }
}

?>