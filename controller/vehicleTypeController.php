<?php

require_once('../repository/vehicleTypeRepository.php');
require_once('../model/vehicleType.php');

class VehicleTypeController{

    private $vehicleType;
    private $vehicleTypeRepository;
    private $mySql;

    public function __construct($mySql){

        $this->mySql = $mySql;
        $this->vehicleTypeRepository = new VehicleTypeRepository($this->mySql);
    }

    public function save($post, $action){

        try {

            $vehicleType = new VehicleType();
            
            $vehicleType = $this->setFields($post, $vehicleType);

            if($action == 'save') return $this->vehicleTypeRepository->save($vehicleType);  
            else return $this->vehicleTypeRepository->update($vehicleType); 
            
        } catch (Exception $e) {

            $description = $e->getMessage() . '- ' . $e->getTraceAsString();
            return new ErrorHandler('Erro ao criar tipo de veículo! - ', true, $description);
        }
    }
    
    public function findAll(){

        $result = $this->vehicleTypeRepository->findAll();

        if($result->hasError) return $result;

        $data = $this->loadData($result->result);

        return new ErrorHandler($data, false, null);
    }

    public function delete($id){
        try {

            return $this->vehicleTypeRepository->delete($id);

        } catch (Exception $e) {

            $description = $e->getMessage() . '- ' . $e->getTraceAsString();
            return new ErrorHandler('Erro ao deletar tipo de veículo! - ', true, $description);
        }
    }

    public function findById($id){

        $result = $this->vehicleTypeRepository->findById($id);

        if($result->hasError) return $result;

        $data = $this->loadData($result->result);

        if(count($data) > 0) return new ErrorHandler($data[0], false, null);
        else return new ErrorHandler(new Employee(), false, null);
    }
    
    public function setFields($post, $vehicleType){

        if($post['id'] && $post['id'] != null) $vehicleType->setId($post['id']);
        $vehicleType->setName(strtoupper($post['name']));
        
        return $vehicleType;
    }

    public function loadData($records){

        $vehicleTypes = array();

        while ($data = $records->fetch_assoc()){ 
            $vehicleType = new VehicleType();
            $vehicleType->setId($data['id']);
            $vehicleType->setName($data['name']);
            $vehicleType->setCreatedDate(date("d/m/Y", strtotime($data['created_date'])));

            if( !is_null($data['modified_date'])){
                $vehicleType->setModifiedDate(date("d/m/Y", strtotime($data['modified_date'])));
            }
            
            $vehicleType->setCreatedBy( $data['created_by']);
            $vehicleType->setModifiedBy($data['modified_by']);
            
            array_push($vehicleTypes, $vehicleType);
        }

        return $vehicleTypes;
    }
}

?>