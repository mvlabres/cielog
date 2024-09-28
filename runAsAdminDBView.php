<?php

include_once 'runAsAdminDB.php';


date_default_timezone_set("America/Sao_Paulo");

if(isset($_POST['runAs']) && $_POST['runAs'] == 'executar'){
    try {
        $sql = 'ALTER TABLE `driver_access` ADD CONSTRAINT `fk_ea_bci2` FOREIGN KEY (business_client_id) REFERENCES business_client(id)';
        $MySQLi->query($sql);
    } catch(Exception $ex){
        echo 'Error: '.$ex->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>CIELOG</title>

    </head>
    <body class="login-body">
        <div class="login-container">
            
            <div class="login-box-info">
                <h3>ADMINISTRADOR.</h3>
            </div>
            
            <form role="form" action="#" method="post" >
                <input type="text" name="runAs">
                <button type="submit" class="login-btn btn">Entre</button>
            </form>
                
            
        </div>
    </body>
</html>