<?php

require_once('conn.php');
require_once('session.php');
require_once('utils.php');

$username =  $_POST['username'];
$password =  $_POST['password'];


if (login($username, $password, $MySQLi) == true){        			   
	echo "<script>window.location='view/index.php'</script>";	
}else	{
	echo "<script>window.location='index.php?error=true'</script>";
} 
?>
