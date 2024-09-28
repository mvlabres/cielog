<?php

require('../conn.php');
require('../session.php');
require('../controller/businessClientController.php');

$controller = new BusinessClientController($MySQLi);

$name = $_GET['name'];
$clientId = $_GET['clientId'];
$forcePost = ['name'=> $name, 'business' => $clientId];

$result = $controller->save($forcePost, 'save'); 
echo 'sucess';
?>