<?php

require_once('../repository/clientRepository.php');
require_once('../model/client.php');
require_once('../model/errorHandler.php');

class ClientController{

    private $client;
    private $clientRepository;
    private $mySql;

    public function __construct($mySql){

        $this->mySql = $mySql;
        $this->clientRepository = new ClientRepository($this->mySql);
    }

    public function save($post, $action){

        try {

            $client = new Client();
            
            $client = $this->setFields($post, $client);

            $result = $this->clientRepository->findByName($client->getName());

            if($result->hasError) return new ErrorHandler('Erro ao verificar se a empresa já existe na base', true, ' - '. $result->errorMessage);

            if($result->result->num_rows > 0) return new ErrorHandler('Já existe um registro com esse nome', true, '');

            if($action == 'save'){
                return $this->clientRepository->save($client); 
            }else{
                return $this->clientRepository->update($client); 
            }
            
        } catch (Exception $e) {

            $description = $e->getMessage() . '- ' . $e->getTraceAsString();
            $description = str_replace('\'', '"', $description);

            return new ErrorHandler('Erro ao criar empresa!', true, $description);
        }
    }

    public function delete($id){

        try {

            return $this->clientRepository->delete($id);

        } catch (Exception $e) {

            $description = $e->getMessage() . '- ' . $e->getTraceAsString();
            $description = str_replace('\'', '"', $description);

            return new ErrorHandler('Erro ao deletar empresa!', true, $description);
        }
    }

    public function findAll(){

        $result = $this->clientRepository->findAll();

        if($result->hasError) return new ErrorHandler('Erro ao buscar clientes', true, ' - '. $result->errorMessage);

        $data = $this->loadData($result->result);

        return new ErrorHandler($data, false, null);
    }

    public function setFields($post, $client){

        if($post['id'] && $post['id'] != null) $client->setId($post['id']);
        $client->setName(strtoupper($post['name']));

        return $client;
    }

    public function loadData($records){

        $clients = array();

        while ($data = $records->fetch_assoc()){ 
            $client = new Client();
            $client->setId($data['id']);
            $client->setName($data['name']);
            $client->setCreatedDate($data['created_date']);
            $client->setModifiedDate($data['modified_date']);
            $client->setCreatedBy($data['created_by']);
            $client->setModifiedBy($data['modified_by']);
    
            array_push($clients, $client);
        }

        return $clients;
    }
}

?>