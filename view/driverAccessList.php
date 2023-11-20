<?php

require_once('../conn.php');
require_once('../session.php');
require_once('../utils.php'); 
require_once('../controller/driverAccessController.php');
require_once('../controller/clientController.php');
require_once('../model/driverAccess.php');

date_default_timezone_set("America/Sao_Paulo");
$startDate = '01/'.date("m/Y");
$endDate = date("d/m/Y");
$business = getClientIfContains();

$filterChecked = 'checked';

$driverAccessController = new DriverAccessController($MySQLi);
$clientController = new ClientController($MySQLi);
$driversAccessResult = null;

if(isset($_POST['action']) && $_POST['action'] == 'delete'){
    $driverAccessId = $_POST['deleteId'];
    $result = $driverAccessController->delete($driverAccessId);

    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    else successAlert($result->result);
}

if(isset($_POST['startDate']) && $_POST['startDate'] != null){

    if(isset($_POST['with-open']) && $_POST['with-open'] == 'true'){
        $filterChecked = 'checked';
    }else{
        $filterChecked = '';
    }

    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $business = $_POST['business'];
}

$withOpen = ($filterChecked == 'checked') ? true : false;
$withOpenExport = ($withOpen) ? 'true': 'false';

$driversAccessResult = $driverAccessController->findByStartDateEndDateAndBusiness($startDate, $endDate, $business, $withOpen);
if($driversAccessResult->hasError) errorAlert($driversAccessResult->result.$driversAccessResult->errorMessage);

$clientsResult = $clientController->findAll();
if($clientsResult->hasError) errorAlert($clientsResult->result.$clientsResult->errorMessage);

?>
<body> 
<div class="row">
        <div class="col-lg-12">
            <div class="panel-title">
                <p>Filtro</p>
            </div>
            <div class="functions-group">
                <form method="post" id="panel-form" action="#">
                    <div class="row-element-group">
                        <div class="form-group">
                            <label>Data inicial</label>
                            <div class='input-group date' id='datetimepicker1'>
                                <input name="startDate" id="startDate" type='text' data-date-format="DD/MM/YYYY" class="form-control" value="<?=$startDate ?>" onblur="dateTimeHandleBlur(this)" required  minlength="19" maxlength="19" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Data final</label>
                            <div class='input-group date' id='datetimepicker1'>
                                <input name="endDate" id="endDate" type='text' data-date-format="DD/MM/YYYY" class="form-control" onblur="dateTimeHandleBlur(this)" value="<?=$endDate ?>" minlength="19" maxlength="19"  required/>
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Empresa visitada</label>
                            <select name="business" id="business" class="form-control">
                                <?php
                                if(checkUserPermission()){
                                    echo '<option value="all">Todas</option>';
                                }   
                                
                                if(!$clientsResult->hasError){
                                    foreach ($clientsResult->result as $client) {
                                        
                                        if(!checkClientDataPermission($client->getId())) continue;

                                        $selected = '';
                                        if($business == $client->getId()) $selected = 'selected';

                                        echo '<option value="'.$client->getId().'" '.$selected.' >'.$client->getName().'</option>';

                                    }
                                }
                                    
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" title="Sem data data/hora de saída" type="checkbox" value="true" name="with-open" id="openAccess" <?=$filterChecked ?>>
                                <label class="form-check-label" for="openAccess">
                                    Acessos em aberto
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                        </div>
                    </div>
                </form>
                <div class="btn-functions-group">
                    <a href="../export/driverAccessExport.php?startDate=<?=date("Y-m-d", strtotime(str_replace('/', '-', $startDate)))?>&endDate=<?=date("Y-m-d", strtotime(str_replace('/', '-', $endDate)))?>&business=<?=$business?>&open-access=<?=$withOpenExport?>"><button type="button" class="btn btn-secondary"><i class="fa fa-file-excel-o"></i> Exportar</button></a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel-title">
                <h3 class="display-2">Acessos de veículos</h3>
            </div>
            <table width="3450px" class="table table-striped table-bordered table-hover" id="dataTables-example">
                <thead>
                    <tr>
                    <?php
                        if($_SESSION['FUNCTION_ACCESS']['edit_access'] != 'hidden') {
                            echo '<th scope="column" class="td-30">Editar</th>';
                        }
                    ?>
                        <th scope="column" class="td-30">Detalhes</th>
                        <th scope="column" class="td-30">Turno</th>
                        <th scope="column" class="td-40">Entrada</th>
                        <th scope="column" class="td-40">CPF</th>
                        <th scope="column" class="td-100">Nome</th>
                        <th scope="column" class="td-70">Empresa visitada</th>
                        <th scope="column" class="td-30">CNH</th>
                        <th scope="column" class="td-70">Vencimento CNH</th>
                        <th scope="column" class="td-70">Transportadora</th>
                        <th scope="column" class="td-40">Saída</th>
                        <th scope="column" class="td-70">Tipo veículo</th>
                        <th scope="column" class="td-70">Placa veículo</th>
                        <th scope="column" class="td-70">Segunda placa</th>
                        <th scope="column" class="td-70">Terceira placa</th>
                        <th scope="column" class="td-70">Operação</th>
                        <th scope="column" class="td-70">NF entrada</th>
                        <th scope="column" class="td-70">NF saída</th>
                        <th scope="column" class="td-70">Usuário (entrada)</th>
                        <th scope="column" class="td-70">Usuário (saída)</th>
                        <?php
                            if($_SESSION['FUNCTION_ACCESS']['delete_access'] != 'hidden'){
                                echo '<th scope="column" class="td-40">Excluir</th>';
                            }
                        ?>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    if(!$driversAccessResult->hasError){
                        foreach ($driversAccessResult->result as $driverAccess) {

                            echo '<tr class="odd gradeX">';
                            if($_SESSION['FUNCTION_ACCESS']['edit_access'] != 'hidden') {
                                echo '<td class="text-center clickble"><a class="cell-action" href="index.php?content=newDriverAccess.php&driverAccessId='.$driverAccess->getId().'&action=edit-all"><span class="fa fa-edit text-primary"></span></a></td>';
                            }
                            echo '<td class="text-center clickble"><a class="cell-action" href="index.php?content=newDriverAccess.php&driverAccessId='.$driverAccess->getId().'&action=view"><span class="fa fa-search text-primary"></span></a></td>';
                            echo '<td>'.$driverAccess->getRotation().'</td>';
                            echo '<td>'.$driverAccess->getStartDatetime().'</td>';
                            echo '<td>'.$driverAccess->getCpf().'</td>';
                            echo '<td>'.$driverAccess->getDriverName().'</td>';
                            echo '<td>'.$driverAccess->getBusinessName().'</td>';
                            echo '<td>'.$driverAccess->getCnh().'</td>';
                            echo '<td>'.$driverAccess->getCnhExpiration().'</td>';
                            echo '<td>'.$driverAccess->getShippingCompany().'</td>';
                            echo '<td>'.$driverAccess->getEndDatetime().'</td>';
                            echo '<td>'.$driverAccess->getVehicleType().'</td>';
                            echo '<td>'.$driverAccess->getVehiclePlate().'</td>';
                            echo '<td>'.$driverAccess->getVehiclePlate2().'</td>';
                            echo '<td>'.$driverAccess->getVehiclePlate3().'</td>';
                            echo '<td>'.$driverAccess->getOperationType().'</td>';
                            echo '<td>'.$driverAccess->getInboundInvoice().'</td>';
                            echo '<td>'.$driverAccess->getOutboundInvoice().'</td>';
                            echo '<td>'.$driverAccess->getCreatedByName().'</td>';
                            if(!is_null( $driverAccess->getEndDatetime() )){
                                echo '<td>'.$driverAccess->getUserOutboundName().'</td>';
                            }else{
                                echo '<td></td>';
                            }

                            if($_SESSION['FUNCTION_ACCESS']['delete_access'] != 'hidden'){
                                echo '<td class="text-center clickble" data-toggle="modal" data-target="#'.$driverAccess->getId().'"><span class="fa fa-trash text-primary cell-action"></span></td>';
                            }
                            echo '</tr>';
                            echo '<div class="modal fade" id="'.$driverAccess->getId().'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form role="form-delete" method="post" action="#">
                                                <input type="hidden" name="deleteId" value="'.$driverAccess->getId().'">
                                                <input type="hidden" name="action" value="delete" >
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Excluir</h4>
                                                </div>
                                                <div class="modal-body">
                                                    Tem certeza que deseja deletar o acesso de '.$driverAccess->getDriverName().'?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Não</button>
                                                    <button type="submit" class="btn btn-primary" id="confirm">Sim</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
