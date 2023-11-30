<?php

require('../conn.php');
require('../session.php');
require('../controller/driverController.php');

$controller = new DriverController($MySQLi);

$result = $controller->findAllToSearch(); 

echo $result->result;

?>