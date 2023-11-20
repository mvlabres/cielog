<?php

require_once('../repository/shippingCompanyRepository.php');
require_once('../model/shippingCompany.php');

class shippingCompanyController{

    private $shippingCompany;
    private $shippingCompanyRepository;
    private $mySql;

    public function __construct($mySql){

        $this->mySql = $mySql;
        $this->shippingCompanyRepository = new ShippingCompanyRepository($this->mySql);
    }

    public function save($post, $action){

        try {

            $shippingCompany = new ShippingCompany();
            
            $shippingCompany = $this->setFields($post, $shippingCompany);

            $result = $this->shippingCompanyRepository->findByName($shippingCompany->getName());
            if($result->result->num_rows > 0) return new ErrorHandler('Já existe uma transportadora com esse nome!', true, null);

            if($action == 'save') return $this->shippingCompanyRepository->save($shippingCompany);  
            else return $this->shippingCompanyRepository->update($shippingCompany); 
            
        } catch (Exception $e) {

            $description = $e->getMessage() . '- ' . $e->getTraceAsString();
            $description = str_replace('\'', '"', $description);

            return new ErrorHandler('Erro ao criar transportadora! - ', true, $description);
        }
    }
    
    public function findAll(){

        $result = $this->shippingCompanyRepository->findAll();

        if($result->hasError) return $result;

        $data = $this->loadData($result->result);

        return new ErrorHandler($data, false, null);
    }

    public function delete($id){
        try {

            return $this->shippingCompanyRepository->delete($id);

        } catch (Exception $e) {

            $description = $e->getMessage() . '- ' . $e->getTraceAsString();
            return new ErrorHandler('Erro ao deletar transportadora! - ', true, $description);
        }
    }

    public function findById($id){

        $result = $this->shippingCompanyRepository->findById($id);

        if($result->hasError) return $result;

        $data = $this->loadData($result->result);

        if(count($data) > 0) return new ErrorHandler($data[0], false, null);
        else return new ErrorHandler(new Employee(), false, null);
    }

    //to ajax
    public function findLastCreated(){

        $result = $this->shippingCompanyRepository->findLastCreated();

        if($result->hasError) return $result->result;

        $data = $this->loadData($result->result);

        if(count($data) > 0) return new ErrorHandler($data[0], false, null);
    }
    
    public function setFields($post, $shippingCompany){

        if($post['id'] && $post['id'] != null) $shippingCompany->setId($post['id']);
        $shippingCompany->setName(mb_strtoupper($post['name']));
        
        return $shippingCompany;
    }

    public function loadData($records){

        $shippingCompanys = array();

        while ($data = $records->fetch_assoc()){ 
            $shippingCompany = new ShippingCompany();
            $shippingCompany->setId($data['id']);
            $shippingCompany->setName($data['name']);
            $shippingCompany->setCreatedDate(date("d/m/Y", strtotime($data['created_date'])));

            if(!is_null($data['modified_date'])){
                $shippingCompany->setModifiedDate(date("d/m/Y", strtotime($data['modified_date'])));
            }
            $shippingCompany->setCreatedBy( $data['created_by']);
            $shippingCompany->setModifiedBy($data['modified_by']);
            
            array_push($shippingCompanys, $shippingCompany);
        }

        return $shippingCompanys;
    }
}

?>