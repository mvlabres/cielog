<?php

require_once('../conn.php');
require_once('../session.php');
require_once('../utils.php'); 
require_once('../controller/employeeAccessController.php');
require_once('../controller/clientController.php');
require_once('../model/employeeAccess.php');

date_default_timezone_set("America/Sao_Paulo");
$startDate = '01/'.date("m/Y");
$endDate = date("d/m/Y");
$business = getClientIfContains();

$employeeAccessController = new employeeAccessController($MySQLi);
$clientController = new ClientController($MySQLi);
$employeesAccessResult = null;

$filterChecked = 'checked';

if(isset($_POST['action']) && $_POST['action'] == 'delete'){
    $employeeAccessId = $_POST['deleteId'];
    $result = $employeeAccessController->delete($employeeAccessId);

    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    else successAlert($result->result);
}

if(isset($_POST['startDate']) && $_POST['startDate'] != null){
    
    if(isset($_POST['with-open']) && $_POST['with-open'] = 'true'){
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

$employeesAccessResult = $employeeAccessController->findByStartDateEndDateAndBusiness($startDate, $endDate, $business, $withOpen);
if($employeesAccessResult->hasError) errorAlert($employeesAccessResult->result.$employeesAccessResult->errorMessage);

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
                                <input class="form-check-input" title="Registros em aberto e fechados" type="checkbox" value="true" name="with-open" id="openAccess" <?=$filterChecked ?>>
                                <label class="form-check-label" for="openAccess">
                                    Com acessos em aberto
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                        </div>
                    </div>
                </form>
                <div class="btn-functions-group">
                    <a href="../export/employeeAccessExport.php?startDate=<?=date("Y-m-d", strtotime(str_replace('/', '-', $startDate)))?>&endDate=<?=date("Y-m-d", strtotime(str_replace('/', '-', $endDate)))?>&business=<?=$business?>&open-access=<?=$withOpenExport?>"><button type="button" class="btn btn-secondary"><i class="fa fa-file-excel-o"></i> Exportar</button></a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel-title">
                <h3 class="display-2">Acessos de colaboradores</h3>
            </div>
            <table width="2200px" class="table table-striped table-bordered table-hover" id="dataTables-example">
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
                        <th scope="column" class="td-70">Empresa</th>
                        <th scope="column" class="td-30">Matrícula</th>
                        <th scope="column" class="td-40">Saída</th>
                        <th scope="column" class="td-70">Veículo</th>
                        <th scope="column" class="td-70">Placa veículo</th>
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
                    if(!$employeesAccessResult->hasError){
                        foreach ($employeesAccessResult->result as $employeeAccess) {

                            $employee = $employeeAccess->getEmployee();

                            echo '<tr class="odd gradeX">';
                            if($_SESSION['FUNCTION_ACCESS']['edit_access'] != 'hidden') {
                                echo '<td class="text-center clickble"><a class="cell-action" href="index.php?content=newEmployeeAccess.php&employeeAccessId='.$employeeAccess->getId().'&action=edit-all"><span class="fa fa-edit text-primary"></span></a></td>';
                            }
                            echo '<td class="text-center clickble"><a class="cell-action" href="index.php?content=newEmployeeAccess.php&employeeAccessId='.$employeeAccess->getId().'&action=view"><span class="fa fa-search text-primary"></span></a></td>';
                            echo '<td>'.$employeeAccess->getRotation().'</td>';
                            echo '<td>'.$employeeAccess->getStartDatetime().'</td>';
                            echo '<td>'.$employee->getCpf().'</td>';
                            echo '<td>'.$employee->getName().'</td>';
                            echo '<td>'.$employee->getBusinessName().'</td>';
                            echo '<td>'.$employee->getRegistration().'</td>';
                            echo '<td>'.$employeeAccess->getEndDatetime().'</td>';
                            echo '<td>'.$employeeAccess->getVehicle().'</td>';
                            echo '<td>'.$employeeAccess->getVehiclePlate().'</td>';
                            echo '<td>'.$employeeAccess->getCreatedByName().'</td>';
                            if(!is_null( $employeeAccess->getEndDatetime() )){
                                echo '<td>'.$employeeAccess->getUserOutboundName().'</td>';
                            }else{
                                echo '<td></td>';
                            }

                            if($_SESSION['FUNCTION_ACCESS']['delete_access'] != 'hidden'){
                                echo '<td class="text-center clickble" data-toggle="modal" data-target="#'.$employeeAccess->getId().'"><span class="fa fa-trash text-primary cell-action"></span></td>';
                            }
                            echo '</tr>';
                            echo '<div class="modal fade" id="'.$employeeAccess->getId().'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form role="form-delete" method="post" action="#">
                                                <input type="hidden" name="deleteId" value="'.$employeeAccess->getId().'">
                                                <input type="hidden" name="action" value="delete" >
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Excluir</h4>
                                                </div>
                                                <div class="modal-body">
                                                    Tem certeza que deseja deletar o acesso de '.$employee->getName().'?
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
