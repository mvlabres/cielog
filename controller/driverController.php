<?php

require_once('../repository/driverRepository.php');
require_once('../model/driver.php');

class DriverController{

    private $driver;
    private $driverRepository;
    private $mySql;
    public $defaultImagePath = '../image-profile/driver/id-00';

    public function __construct($mySql){

        $this->mySql = $mySql;
        $this->driverRepository = new DriverRepository($this->mySql);
    }

    public function save($post, $action){

        $recordId = null;

        try {

            $driver = new Driver();
            $driver = $this->setFields($post, $driver);

            
            if($action == 'save') {
                $result = $this->driverRepository->findByCpf($driver->getCpf());
    
                if($result->hasError) return new ErrorHandler('Erro ao verificar se a empresa já existe na base', true, ' - '. $result->errorMessage);
    
                if($result->result->num_rows > 0) return new ErrorHandler('Já existe um registro com esse CPF', true, '');

                if($post['redirect'] == 'redirect') {
                    $result = $this->driverRepository->save($driver);
                    $recordId = $result->result; 
                }else {
                    $result = $this->driverRepository->save($driver);
                    $recordId = $result->result;
                    $result->result = 'Registro salvo com sucesso!';
                }
            }  
            else {
                if($post['redirect'] == 'redirect') {
                    $result = $this->driverRepository->update($driver);
                    $recordId = $result->result;

                }else {
                    $result = $this->driverRepository->update($driver);
                    $recordId = $result->result;
                    $result->result = 'Registro atualizado com sucesso!';
                }
            }

            if($_POST['image-profile'] != null){

                try {
                    $this->saveBase64Image($_POST['image-profile'], $recordId);
                } catch (Exception $ex) {
                    throw $ex;
                }  
            }

            if($post['redirect'] == 'redirect') echo '<script>window.location="index.php?content=newDriverAccess.php&driverId='.$result->result.'"</script>';
            else return $result;
            
        } catch (Exception $e) {

            $description = $e->getMessage() . '- ' . $e->getTraceAsString();
            return new ErrorHandler('Erro ao salvar dados do motorista! - ', true, $description);
        }
    }
    
    public function findAll(){

        $result = $this->driverRepository->findAll();

        if($result->hasError) return $result;

        $data = $this->loadData($result->result, false);

        return new ErrorHandler($data, false, null);
    }

    public function findAllToSearch(){

        $result = $this->driverRepository->findAllToSearch();

        if($result->hasError) return $result;

        $data = $this->loadDataToSerch($result->result, false);

        return new ErrorHandler($data, false, null);
    }

    public function delete($id){
        try {

            return $this->driverRepository->delete($id);

        } catch (Exception $e) {

            $description = $e->getMessage() . '- ' . $e->getTraceAsString();
            return new ErrorHandler('Erro ao deletar registro de motorista! - ', true, $description);
        }
    }

    public function findById($id){

        $result = $this->driverRepository->findById($id);

        if($result->hasError) return $result;

        $data = $this->loadData($result->result, true);

        if(count($data) > 0) return new ErrorHandler($data[0], false, null);
        else return new ErrorHandler(new Employee(), false, null);
    }
    
    public function setFields($post, $driver){

        if($post['id'] && $post['id'] != null) $driver->setId($post['id']);
        $driver->setName($post['name']);
        $driver->setCnh($post['cnh']);
        if(!is_null($driver->getCnh())){
            $driver->setCnhExpiration(date("Y-m-d", strtotime(str_replace('/', '-', $post['cnhExpiration'] ))));
        }
        $driver->setCpf($post['cpf']);
        $driver->setShippingCompany($post['shippingCompany']);
        $driver->setVehicleType($post['vehicleType']);
        $driver->setVehiclePlate($post['vehiclePlate']);
        $driver->setVehiclePlate2($post['vehiclePlate2']);
        $driver->setVehiclePlate3($post['vehiclePlate3']);
        $driver->setRecordType($post['recordType']);
        $driver->setStatus($post['status']);
        $driver->setBlockReason($post['blockReason']);

        return $driver;
    }

    public function loadData($records, $withFile){

        $drivers = array();

        while ($data = $records->fetch_assoc()){ 
            $driver = new Driver();
            $driver->setId($data['id']);
            $driver->setName($data['name']);
            $driver->setCnh($data['cnh']);
            if(!is_null($driver->getCnh())){
                $driver->setCnhExpiration(date("d/m/Y", strtotime($data['cnh_expiration'])));
            }
            $driver->setCpf($data['cpf']);
            $driver->setShippingCompany($data['shipping_company']);
            $driver->setVehicleType($data['vehicle_type']);
            $driver->setVehiclePlate($data['vehicle_plate']);
            $driver->setVehiclePlate2($data['vehicle_plate2']);
            $driver->setVehiclePlate3($data['vehicle_plate3']);
            $driver->setRecordType($data['record_type']);
            $driver->setStatus($data['status']);
            $driver->setBlockReason($data['block_reason']);
            $driver->setCreatedDate(date("d/m/Y", strtotime($data['created_date'])));
            
            if(!is_null($data['modified_date'])){
                $driver->setModifiedDate(date("d/m/Y", strtotime($data['modified_date'])));
            }
            
            $driver->setCreatedBy( $data['created_by']);
            $driver->setModifiedBy($data['modified_by']);

            if($withFile) $driver = $this->setImagePath($driver);
            
            array_push($drivers, $driver);
        }

        return $drivers;
    }

    public function loadDataToSerch($records, $withFile){

        $driver = '';

        while ($data = $records->fetch_assoc()){ 
            $driver .= $data['id'];
            $driver .= ';' . $data['name'];
            $driver .= ';' .$data['cpf'];

            $driver.= '|';
        }

        $driver.= '|';

        return rtrim($driver, '|');
    }

    public function setImagePath($driver){
        
        $filename = $this->defaultImagePath.$driver->getId().'.png';

        if (file_exists($filename)) $driver->setImageProfilePath($filename);
        else $driver->setImageProfilePath('../images/profile.jpg');

        return $driver;
    }

    public function saveBase64Image($base64, $recordId){
        $base64 = str_replace('data:image/png;base64,', '', $base64);
        $base64 = str_replace(' ', '+', $base64);
        $data = base64_decode($base64);
        file_put_contents($this->defaultImagePath.$recordId.'.png', $data);
    }
}

?>