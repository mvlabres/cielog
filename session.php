<?php

define('ROOT_PATH', dirname(__FILE__));

function sec_session_start() {  
    error_reporting(0);
    date_default_timezone_set("America/Sao_Paulo");
    $session_name = 'logado'; 
    $secure=false;
    $httponly = true;

    if (ini_set('session.use_only_cookies', 1) === FALSE) {
       header('Location:index.php');
        exit();
    }
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"],
        $cookieParams["path"], 
        $cookieParams["domain"], 
        $secure,
        $httponly);
    
    session_name($session_name);
    session_start();            
    session_regenerate_id();    
    
}

$pagesNotClearPost = ['newUser.php', 'newEmployee.php'];

sec_session_start();

$contentPost = '';

if(isset($_GET['content']) && $_GET['content'] != null){
    $contentPost = $_GET['content'];
}

if( $_SERVER['REQUEST_METHOD'] =='POST' && !in_array($contentPost, $pagesNotClearPost)){
    
    $request = md5(implode( $_POST ) );

    if( isset( $_SESSION['last_request'] ) && $_SESSION['last_request'] == $request ) {
        unset($_POST);
        $_SESSION['last_request'] = '';
    }
    else {
        $_SESSION['last_request'] = $request;
    }
}

function login($username, $password, $mysqli) {

    $data = date('d/m/Y');
    $hora = date('h:i');

    if ($stmt = $mysqli->prepare("SELECT id, name, username, password, type, client_id FROM user  WHERE username = ? LIMIT 1")){  
        
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();

        $stmt->bind_result($id, $name, $base_username,$base_password, $type, $clientId);
        $stmt->fetch();

        if ($stmt->num_rows == 1){ 

            if ($password == $base_password){ 
                $_SESSION['id'] = $id;
                $_SESSION['name'] = $name;
                $_SESSION['username'] = $base_username;
                $_SESSION['type'] = $type;

                getAccess($mysqli);
                return true;

            } else return false;
            
        } else return false;
    
    } else return false;
}

function getAccess($mysqli){

    $functionAccess = [
        'users'=> 'hidden',
        'access'=> 'hidden',
        'access_new'=> 'hidden',
        'access_list'=> 'hidden',
        'register'=> 'hidden',
        'register_client'=> 'hidden',
        'register_employee'=> 'hidden',
        'register_shipping_company' => 'hidden',
        'register_vehicle_type' => 'hidden',
        'register_driver' => 'hidden',
        'new_employee' => 'hidden'
    ];

    $sql = "SELECT id, user_type, function_name
            FROM user_access 
            WHERE user_type = '".$_SESSION['type'] ."'";
               
    $result = $mysqli->query($sql);

    while ($data = $result->fetch_assoc()){ 
        $functionAccess[$data['function_name']] = '';

    }

    $_SESSION['FUNCTION_ACCESS'] = $functionAccess;
}

function login_check($mysqli) {

    if (isset($_SESSION['username'])){
        $username = $_SESSION['username'];
        if ($stmt = $mysqli->prepare("SELECT id,name, username, type, password, client_id FROM user  WHERE username = ? LIMIT 1")){
            $stmt->bind_param('i', $id);
            $stmt->execute();  
            $stmt->store_result();

            if($stmt->num_rows == 1) {
                if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > 7200) {
                    session_unset();     
                    session_destroy();  
                    return false;
                }
                else{
                    $_SESSION['LAST_ACTIVITY'] = time();
                    return true;
                }
            } else return false;
        } else return false;
    } else  return false;
}