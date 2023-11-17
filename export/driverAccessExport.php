<?php

require_once('../conn.php');
require_once('../session.php');

$startDate = null;
$endDate = null;
$business = null;

if((isset($_GET['startDate']) && $_GET['startDate'] != null) && (isset($_GET['endDate']) && $_GET['endDate'] != null)){

    $startDate = $_GET['startDate'];
    $endDate = $_GET['endDate'];
    $business = $_GET['business'];
    $openAccess = $_GET['open-access'];

    $startDate = date("Y-m-d H:i", strtotime(str_replace('/', '-', $startDate.' 00:00' )));
    $endDate = date("Y-m-d H:i", strtotime(str_replace('/', '-', $endDate.' 23:59' )));

    $standardQuery = 'SELECT dr_a.id AS access_id,
                            dr.id AS driver_id,
                            dr.name AS driver_name,
                            start_datetime,
                            end_datetime,
                            dr.cnh AS driver_cnh,
                            dr.cnh_expiration AS driver_cnh_expiration,
                            dr.cpf AS driver_cpf,
                            dr.shipping_company AS driver_shipping_company,
                            dr_a.vehicle_type AS driver_vehicle_type,
                            dr_a.vehicle_plate AS driver_vehicle_plate,
                            dr_a.vehicle_plate2 AS driver_vehicle_plate2,
                            dr_a.vehicle_plate3 AS driver_vehicle_plate3,
                            cl.id AS business_id,
                            cl.name AS business_name,
                            inbound_invoice,
                            outbound_invoice,
                            operation_type,
                            dr.status AS driver_access_status,
                            dr.block_reason AS driver_block_reason,
                            dr_a.created_date AS access_created_date,
                            dr_a.modified_date AS access_modified_date,
                            dr_a.created_by AS access_created_by,
                            us_2.name AS access_created_by_name,
                            dr_a.modified_by AS access_modified_by,
                            us_3.name AS access_modified_name,
                            us_1.id AS access_outbound_id,
                            us_1.name AS access_outbound_name
                        FROM driver_access AS dr_a 
                        INNER JOIN driver AS dr ON dr.id = driver_id
                        INNER JOIN client AS cl ON cl.id = business_id
                        INNER JOIN user AS us_1 ON us_1.id = user_outbound_id
                        INNER JOIN user AS us_2 ON us_2.id = dr_a.created_by
                        INNER JOIN user AS us_3 ON (us_3.id = dr_a.modified_by OR ISNULL(dr_a.modified_by )) ';

    if( !is_null($business) && $business != '' && $business != 'all' ){
        if( $openAccess == 'false') $sql = $standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$business.' AND end_datetime NOT LIKE "0000%" GROUP BY dr_a.id ORDER BY start_datetime DESC';
        else $sql = $standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$business.' GROUP BY dr_a.id ORDER BY start_datetime DESC';

    }else {
        if( $openAccess == 'false') $sql = $standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND end_datetime NOT LIKE "0000%" GROUP BY dr_a.id ORDER BY start_datetime DESC'; 
        else $sql = $standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" GROUP BY dr_a.id ORDER BY start_datetime DESC'; 
    }

    $driverAccess = $MySQLi->query($sql);
}

$file = '';
$file .= '<table><thead><tr>';        
    
$file .= '<th>Entrada</th>';
$file .= '<th>CPF</th>';
$file .= '<th>Nome</th>';
$file .= '<th>Empresa visitada</th>';
$file .= '<th>CNH</th>';
$file .= '<th>Vencimento CNH</th>';
$file .= '<th>Transportadora</th>';
$file .= '<th>'.utf8_decode("Saída").'</th>';
$file .= '<th>'.utf8_decode("Tipo veículo").'</th>';
$file .= '<th>'.utf8_decode("Placa veículo").'</th>';
$file .= '<th>Segunda placa</th>';
$file .= '<th>Terceira placa</th>';
$file .= '<th>'.utf8_decode("Operação").'</th>';
$file .= '<th>NF entrada</th>';
$file .= '<th>'.utf8_decode("NF saída").'</th>';
$file .= '<th>'.utf8_decode("Usuário (entrada)").'</th>';
$file .= '<th>'.utf8_decode("Usuário (saída)").'</th>';

while ($data = $driverAccess->fetch_assoc()){ 
    $file .= '<tr>';
    $file .= '<td>'.utf8_decode(date("d/m/Y H:i", strtotime($data['start_datetime']))).'</td>';
    $file .= '<td>'.utf8_decode($data['driver_cpf']).'</td>';
    $file .= '<td>'.utf8_decode($data['driver_name']).'</td>';
    $file .= '<td>'.utf8_decode($data['business_name']).'</td>';
    $file .= '<td>'.utf8_decode($data['driver_cnh']).'</td>';
    if(!strpos($data['driver_cnh_expiration'], '0000') && !is_null($data['driver_cnh_expiration'])){
        $file .= '<td>'.utf8_decode(date("d/m/Y", strtotime( $data['driver_cnh_expiration']))).'</td>';
    }else{
        $file .= '<td></td>';
    }
    
    $file .= '<td>'.utf8_decode($data['driver_shipping_company']).'</td>';
    
    $endDate = (strpos($data['end_datetime'], '0000') === false) ? date("d/m/Y H:i", strtotime($data['end_datetime'])) : ''; 
    $file .= '<td>'.utf8_decode( $endDate ).'</td>';
    $file .= '<td>'.utf8_decode( $data['driver_vehicle_type'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['driver_vehicle_plate'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['driver_vehicle_plate2'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['driver_vehicle_plate3'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['operation_type'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['inbound_invoice'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['outbound_invoice'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['access_created_by_name'] ).'</td>';
    if(strpos($data['end_datetime'], '0000') === false){
        $file .= '<td>'.utf8_decode( $data['access_outbound_name'] ).'</td>';
    }else{
        $file .= '<td></td>';
    }
    $file .= '</tr>';
}

$file .= '</tbody></table>';

header ("Expires: Mon, 29 Out 2015 15:00:00 GMT");
header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header ("Content-type: application/x-msexcel"); 
header ("Content-Disposition: attachment; filename=Acessos de veiculos-".date('Y-m-d').".xls" );
header ("Content-Description: PHP Generated Data" );

echo $file;

?>