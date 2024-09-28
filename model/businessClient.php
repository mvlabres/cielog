<?php

class BusinessClient{

    private $id;
    private $name;
    private $clientId;
    private $clientName;
    private $createdDate;
    private $modifiedDate;
    private $createdBy;
    private $modifiedBy;
    private $createdByName;
    
    
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

    public function setCreatedByName($createdByName){
        $this->createdByName = $createdByName;
    }
    public function getCreatedByName(){
        return $this->createdByName;
    }

    public function setModifiedBy($modifiedBy){
        $this->modifiedBy = $modifiedBy;
    }
    public function getModifiedBy(){
        return $this->modifiedBy;
    }
}

?>