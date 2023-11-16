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
}

?>