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

    $standardQuery = 'SELECT em_a.id AS access_id,
                        em.id AS employee_id,
                        em.name AS employee_name,
                        start_datetime,
                        end_datetime,
                        em.cpf AS employee_cpf,
                        em.registration AS employee_registration,
                        em.cpf AS employee_cpf,
                        em.vehicle AS employee_vehicle,
                        em.vehicle_plate AS employee_vehicle_plate,
                        cl.id AS business_id,
                        cl.name AS business_name,
                        em_a.created_date AS access_created_date,
                        em_a.modified_date AS access_modified_date,
                        em_a.created_by AS access_created_by,
                        us_2.name AS access_created_by_name,
                        em_a.modified_by AS access_modified_by,
                        us_3.name AS access_modified_name,
                        us_1.id AS access_outbound_id,
                        us_1.name AS access_outbound_name
                    FROM employee_access AS em_a 
                    INNER JOIN employee AS em ON em.id = employee_id
                    INNER JOIN client AS cl ON cl.id = em.business_id
                    INNER JOIN user AS us_1 ON us_1.id = user_outbound_id
                    INNER JOIN user AS us_2 ON us_2.id = em_a.created_by
                    INNER JOIN user AS us_3 ON (us_3.id = em_a.modified_by OR ISNULL(em_a.modified_by )) ';

    if( !is_null($business) && $business != '' && $business != 'all'){
        if($openAccess == 'false') $sql = $standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$business.' AND end_datetime NOT LIKE "0000%" GROUP BY em_a.id ORDER BY start_datetime DESC';
        else $sql = $standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$business.' GROUP BY em_a.id ORDER BY start_datetime DESC';
    }else {
        if($openAccess == 'false') $sql = $standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND end_datetime NOT LIKE "0000%" GROUP BY em_a.id ORDER BY start_datetime DESC'; 
        else $sql = $standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" GROUP BY em_a.id ORDER BY start_datetime DESC'; 
    }

    $employeeAccess = $MySQLi->query($sql);
}

$file = '';
$file .= '<table><thead><tr>';        
    
$file .= '<th>Entrada</th>';
$file .= '<th>CPF</th>';
$file .= '<th>Nome</th>';
$file .= '<th>Empresa</th>';
$file .= '<th>'.utf8_decode("Matrícula").'</th>';
$file .= '<th>'.utf8_decode("Saída").'</th>';
$file .= '<th>'.utf8_decode("Veículo").'</th>';
$file .= '<th>'.utf8_decode("Placa veículo").'</th>';
$file .= '<th>'.utf8_decode("Usuário (entrada)").'</th>';
$file .= '<th>'.utf8_decode("Usuário (saída)").'</th>';

while ($data = $employeeAccess->fetch_assoc()){ 
    $file .= '<tr>';
    $file .= '<td>'.utf8_decode(date("d/m/Y H:i", strtotime($data['start_datetime']))).'</td>';
    $file .= '<td>'.utf8_decode($data['employee_cpf']).'</td>';
    $file .= '<td>'.utf8_decode($data['employee_name']).'</td>';
    $file .= '<td>'.utf8_decode($data['business_name']).'</td>';
    $file .= '<td>'.utf8_decode($data['employee_registration']).'</td>';
    $endDate = (strpos($data['end_datetime'], '0000') === false) ? date("d/m/Y H:i", strtotime($data['end_datetime'])) : ''; 
    $file .= '<td>'.utf8_decode( $endDate ).'</td>';
    $file .= '<td>'.utf8_decode( $data['employee_vehicle'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['employee_vehicle_plate'] ).'</td>';

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
header ("Content-Disposition: attachment; filename=Acessos de colaboradores-".date('Y-m-d').".xls" );
header ("Content-Description: PHP Generated Data" );

echo $file;

?>