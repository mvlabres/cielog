<?php

require('../conn.php');
require('../session.php');
require('../controller/employeeController.php');

$controller = new EmployeeController($MySQLi);

$result = $controller->findAllToSearch(); 

echo $result->result;

?>