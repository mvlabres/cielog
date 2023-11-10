<?php

require_once('../repository/userRepository.php');
require_once('../model/user.php');

class UserController{

    private $user;
    private $userRepository;
    private $mySql;

    public function __construct($mySql){

        $this->mySql = $mySql;
        $this->userRepository = new UserRepository($this->mySql);
    }

    public function save($post, $action){

        try {

            $user = new User();
            
            $user = $this->setFields($post, $user);

            if($action == 'save'){
                if(is_null($user->getClientId())) return $this->userRepository->saveWithoutClient($user);
                else return $this->userRepository->save($user); 
                
            }else{
                if(is_null($user->getClientId())) return $this->userRepository->updateWithoutClient($user); 
                else return $this->userRepository->update($user); 
            }
            
        } catch (Exception $e) {

            $description = $e->getMessage() . '- ' . $e->getTraceAsString();
            $description = str_replace('\'', '"', $description);

            return new ErrorHandler('Erro ao criar empresa!', true, $description);
        }
    }
    
    public function findAll(){

        $result = $this->userRepository->findAll();

        if($result->hasError) return $result;

        $data = $this->loadData($result->result);

        return new ErrorHandler($data, false, null);
    }

    public function delete($id){
        try {

            return $this->userRepository->delete($id);

        } catch (Exception $e) {

            $description = $e->getMessage() . '- ' . $e->getTraceAsString();
            return new ErrorHandler('Erro ao deletar o cliente!', true, $description);
        }
    }

    public function findById($id){

        $result = $this->userRepository->findById($id);

        if($result->hasError) return $result;

        $data = $this->loadData($result->result);

        if(count($data) > 0) return new ErrorHandler($data[0], false, null);
        else return new ErrorHandler(new User(), false, null);
    }

    // public function saveFiles($scheduleId, $action){

    //     $countfiles = count($_FILES['file']['name']);

    //     try {
    //         for($i=0;$i<$countfiles;$i++){
    
    //             $fileName =  $_FILES['file']['name'][$i];
    
    //             $scheduleDirectory = 'files/schedule_'.$scheduleId.'/';
    
    //             if (!file_exists($scheduleDirectory)) mkdir($scheduleDirectory, 0755);
                
    //             $tempName = $_FILES['file']['tmp_name'][$i];
    //             $pathFile = $scheduleDirectory.$fileName;

    //             if (!file_exists($pathFile)) {
    //                 move_uploaded_file($tempName,$pathFile);
    //                 $this->attachmentRepository->save($scheduleId, $pathFile);
    //             }
    //         }

    //         return $action;

    //     } catch (Exception $e) {
    //         return 'SAVE_ERROR';
    //     }
    // }

    // public function savePreferences($columnsDefault, $post){

    //     $columnsPreference = $post['column'];

    //     $id = $post['preferenceId'];

    //     $columnsToSave = array();
    //     $cont = 0;

    //     foreach ($columnsDefault as $key => $value) {
            
    //         $value['show'] = false;
    //         $value['order'] = $cont + 200;

    //         $columnsToSave[$key] = $value;
    //         $cont++;
    //     }

    //     $cont = 0;
    //     foreach ($columnsPreference as $value) {
            
    //         $column = $columnsToSave[$value];

    //         $column['show']  = true;
    //         $column['order'] = $cont;

    //         $columnsToSave[$value] = $column;
    //         $cont++;
    //     }

    //     $columnsPreference = new ColumnsPreference();
    //     $columnsPreferencesRepository = new ColumnsPreferencesRepository($this->mySql);

    //     $columnsPreference->setUserId($_SESSION['id']);
    //     $columnsPreference->setPreference( json_encode($columnsToSave, JSON_UNESCAPED_UNICODE));

    //     if($id == null){
    //         return $columnsPreferencesRepository->save($columnsPreference);
    //     }

    //     return $columnsPreferencesRepository->updateById($columnsPreference, $id);

    // }

    // public function findPreferenceByUser(){

    //     $columnsPreferencesRepository = new ColumnsPreferencesRepository($this->mySql);
    //     $result = $columnsPreferencesRepository->findByUser($_SESSION['id']);

    //     if($result->num_rows == 0) return new ColumnsPreference();

    //     return $this->loadPreferenceData($result);
    // }

    // public function sortArray($columns){

    //     $ordenedColumns = array();

    //     $cont = 0;
    //     foreach ($columns as $key => $value){

    //         $ordenedColumns[$cont] = $value['order'];
    //         $cont++; 
    //     }

    //     array_multisort($ordenedColumns, SORT_ASC, $columns);

    //     return $columns;
    // }

    // public function findAttByScheduleId($schedule){

    //     $result = $this->attachmentRepository->findByScheduleId($schedule->getId());

    //     $paths = array();

    //     while ($data = $result->fetch_assoc()){ 

    //         $paths[$data['id']] = $data['path'];
    //     }
        
    //     $schedule->setFilesPath($paths);
    //     return $schedule;
    // }

    public function setFields($post, $user){

        if($post['id'] && $post['id'] != null) $user->setId($post['id']);
        $user->setName($post['name']);
        $user->setUsername($post['username']);
        $user->setPassword($post['password']);
        $user->setType($post['type']);

        if($post['type'] == 'client'){
            $user->setClientId($post['business']);
        }

        return $user;
    }

    // public function loadPreferenceData($records){

    //     while ($data = $records->fetch_assoc()){ 
    //         $columnsPreference = new ColumnsPreference();
    //         $columnsPreference->setId($data['id']);
    //         $columnsPreference->setPreference($data['preference']);
    //         $columnsPreference->setUserId($data['userId']);
    //     }

    //     return $columnsPreference;
    // }

    // public function loadDataByNameValue($records){

    //     $schedules = array();

    //     while ($data = $records->fetch_assoc()){ 


    //         $schedule['getId'] = $data['id'];
    //         $schedule['getTransportadora'] = $data['transportadora'];
    //         $schedule['getTipoVeiculo'] = $data['tipoVeiculo'];
    //         $schedule['getPlacaCavalo'] = $data['placa_cavalo'];
    //         $schedule['getOperacao'] = $data['operacao'];
    //         $schedule['getNf'] = $data['nf'];
    //         $schedule['getHoraChegada'] = date("d/m/Y H:i:s", strtotime($data['horaChegada']));
    //         if(empty($data['horaChegada'])) $schedule['getHoraChegada'] = '';

    //         $schedule['getInicioOperacao'] = date("d/m/Y H:i:s", strtotime($data['inicio_operacao']));
    //         if(empty($data['inicio_operacao'])) $schedule['getInicioOperacao'] = '';

    //         $schedule['getFimOperacao'] = date("d/m/Y H:i:s", strtotime($data['fim_operacao']));
    //         if(empty($data['fim_operacao'])) $schedule['getFimOperacao'] = '';

    //         $schedule['getNomeUsuario'] = $data['usuario'];
    //         $schedule['getDataInclusao'] = date("d/m/Y H:i:s", strtotime($data['dataInclusao']));
    //         $schedule['getPeso'] = $data['peso'];
    //         $schedule['getDataAgendamento'] = date("d/m/Y H:i:s", strtotime($data['data_agendamento']));
    //         $schedule['getSaida'] = date("d/m/Y H:i:s", strtotime($data['saida']));
    //         if(empty($data['saida'])) $schedule['getSaida'] = '';

    //         $schedule['getSeparacao'] = $data['separacao'];
    //         $schedule['getShipmentId'] = $data['shipment_id'];
    //         $schedule['getDoca'] = $data['doca'];
    //         $schedule['getDo_s'] = $data['do_s'];
    //         $schedule['getCidade'] = $data['cidade'];
    //         $schedule['getCargaQtde'] = $data['carga_qtde'];
    //         $schedule['getObservacao'] = $data['observacao'];
    //         $schedule['getDadosGerais'] = $data['dados_gerais'];
    //         $schedule['getCliente'] = $data['cliente'];
    //         $schedule['getStatus'] = $data['status'];
    //         $schedule['getNomeMotorista'] = $data['nome_motorista']; 
    //         $schedule['getPlacaCarreta2'] = $data['placa_carreta2'];
    //         $schedule['getDocumentoMotorista'] = $data['documento_motorista'];
    //         $schedule['getPlacaCarreta'] = $data['placa_carreta'];
    //         $schedule['getOperationId'] = $data['operation_type_id'];
            
    //         array_push($schedules, $schedule);
    //     }

    //     return $schedules;
    // }

    public function loadData($records){

        $users = array();

        while ($data = $records->fetch_assoc()){ 
            $user = new User();
            $user->setId($data['id']);
            $user->setName($data['name']);
            $user->setUsername($data['username']);
            $user->setPassword($data['password']);
            $user->setType($data['type']);
            $user->setCreatedDate(date("d/m/Y", strtotime($data['created_date'])));
            $user->setModifiedDate(date("d/m/Y", strtotime($data['modified_date'])));
            $user->setCreatedBy( $data['created_by']);
            $user->setModifiedBy($data['modified_by']);
            $user->setClientId($data['client_id']);

            if(!is_null($data['client_id'])){
                $user->setClientName($data['client_name']);
            }
        
            array_push($users, $user);
        }

        return $users;
    }

    // public function getIdLastError(){
    
    //     $result = $this->scheduleRepository->getLastError();
    //     $id = '';

    //     while ($data = $result->fetch_assoc()){ 
    //        $id = $data['id'];
    //     }

    //     return $id;
    // }
}

?>