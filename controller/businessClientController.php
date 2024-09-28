<?php

require_once('../repository/businessClientRepository.php');
require_once('../model/businessClient.php');

class BusinessClientController{

    private $businessClient;
    private $businessClientRepository;
    private $mySql;

    public function __construct($mySql){

        $this->mySql = $mySql;
        $this->businessClientRepository = new BusinessClientRepository($this->mySql);
    }

    public function save($post, $action){

        try {

            $businessClient = new BusinessClient();
            
            $businessClient = $this->setFields($post, $businessClient);

            $result = $this->businessClientRepository->findByNameAndClient($businessClient->getName(), $businessClient->getClientId());
            if($result->result->num_rows > 0) return new ErrorHandler('Já existe um registro com esse Nome e cliente', true, '');

            if($action == 'save') return $this->businessClientRepository->save($businessClient);  
            else return $this->businessClientRepository->update($businessClient); 
            
        } catch (Exception $e) {

            $description = $e->getMessage() . '- ' . $e->getTraceAsString();
            return new ErrorHandler('Erro ao criar registro! - ', true, $description);
        }
    }
    
    public function findAll(){

        $result = $this->businessClientRepository->findAll();

        if($result->hasError) return $result;

        $data = $this->loadData($result->result);

        return new ErrorHandler($data, false, null);
    }

    public function delete($id){
        try {

            return $this->businessClientRepository->delete($id);

        } catch (Exception $e) {

            $description = $e->getMessage() . '- ' . $e->getTraceAsString();
            return new ErrorHandler('Erro ao deletar registro! - ', true, $description);
        }
    }

    public function findById($id){

        $result = $this->businessClientRepository->findById($id);

        if($result->hasError) return $result;

        $data = $this->loadData($result->result);

        if(count($data) > 0) return new ErrorHandler($data[0], false, null);
        else return new ErrorHandler(new BusinessClient(), false, null);
    }

    public function findByClientId($clientId){

        $result = $this->businessClientRepository->findByClientId($clientId);

        if($result->hasError) return $result;
        $data = $this->loadData($result->result);

        return new ErrorHandler($data, false, null);
    }
    
    public function setFields($post, $businessClient){

        if($post['id'] && $post['id'] != null) $businessClient->setId($post['id']);
        $businessClient->setName(mb_strtoupper($post['name']));
        $businessClient->setClientId($post['business']);
        
        return $businessClient;
    }

    public function loadData($records){

        $businessClients = array();

        while ($data = $records->fetch_assoc()){ 
            $businessClient = new BusinessClient();
            $businessClient->setId($data['business_client_id']);
            $businessClient->setName($data['client_business_name']);
            $businessClient->setClientId($data['business_client_client_id']);
            $businessClient->setClientName($data['client_name']);
            $businessClient->setCreatedDate(date("d/m/Y", strtotime($data['business_client_created_date'])));

            if( !is_null($data['business_client_modified_date'])){
                $businessClient->setModifiedDate(date("d/m/Y", strtotime($data['business_client_modified_date'])));
            }
            
            $businessClient->setCreatedBy( $data['user_name']);
            
            array_push($businessClients, $businessClient);
        }

        return $businessClients;
    }

    public function findByClientIdToAjax($clientId){

        $result = $this->businessClientRepository->findByClientId($clientId);

        if($result->hasError) return $result;
        $data = $this->loadAjaxData($result->result);

        return new ErrorHandler($data, false, null);
    }

    public function loadAjaxData($records){
        $businessClients = '';

        while ($data = $records->fetch_assoc()){ 
            $businessClients .= $data['business_client_id'];
            $businessClients .= '-' . $data['client_business_name'];

            $businessClients.= '|';
        }

        return rtrim($businessClients, '|');
    }
}

?>