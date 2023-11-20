<?php

require('../conn.php');
require('../session.php');
require('../controller/shippingCompanyController.php');

$controller = new shippingCompanyController($MySQLi);

$name = $_GET['name'];
$forcePost = ['name'=> $name];

$result = $controller->save($forcePost, 'save'); 

if($result->hasError){
    echo 'ERROR-'.$result->result;
}else{
    $result = $controller->findLastCreated(); 
    echo $result->result->getName();
}
?>