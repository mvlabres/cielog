<?php
header('Content-Type: text/html; charset=utf-8');

$isProduction = false;

$productionConn = array(
    'servidor' => 'localhost',  
    'usuario' => 'labsoftt_marcos',   
    'senha' => 'getnis2018',       
    'banco' => 'labsoftt_cielog'  
);


$MySQL = $productionConn;

$MySQLi = new MySQLi($MySQL['servidor'], $MySQL['usuario'], $MySQL['senha'], $MySQL['banco']);
$MySQLi->set_charset("utf8");




?>