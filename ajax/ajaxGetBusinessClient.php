<?php

require('../conn.php');
require('../session.php');
require('../controller/businessClientController.php');

$controller = new BusinessClientController($MySQLi);
$clientId = $_GET['clientId'];

$result = $controller->findByClientIdToAjax($clientId);
echo $result->result;

?>