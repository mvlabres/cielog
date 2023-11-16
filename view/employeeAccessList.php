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
$business = null;

$employeeAccessController = new employeeAccessController($MySQLi);
$clientController = new ClientController($MySQLi);
$employeesAccessResult = null;

if(isset($_POST['startDate']) && $_POST['startDate'] != null){
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $business = $_POST['business'];
}

$employeesAccessResult = $employeeAccessController->findByStartDateEndDateAndBusiness($startDate, $endDate, $business);
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
                                <option value="all">Todas</option>
                                <?php

                                if(!$clientsResult->hasError){
                                    foreach ($clientsResult->result as $client) {

                                        $selected = '';
                                        if($business == $client->getId()) $selected = 'selected';
                                        echo '<option value="'.$client->getId().'" '.$selected.' >'.$client->getName().'</option>';

                                    }
                                }
                                    
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                        </div>
                    </div>
                </form>
                <div class="btn-functions-group">
                    <a href="../export/employeeAccessExport.php?startDate=<?=date("Y-m-d", strtotime(str_replace('/', '-', $startDate)))?>&endDate=<?=date("Y-m-d", strtotime(str_replace('/', '-', $endDate)))?>&business=<?=$business?>"><button type="button" class="btn btn-secondary"><i class="fa fa-file-excel-o"></i> Exportar</button></a>
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
                    <th scope="column" class="td-30">Finalizar</th>
                        <th scope="column" class="td-30">Detalhes</th>
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
                    </tr>
                </thead>
                <tbody>

                    <?php
                    if(!$employeesAccessResult->hasError){
                        foreach ($employeesAccessResult->result as $employeeAccess) {

                            $employee = $employeeAccess->getEmployee();

                            echo '<tr class="odd gradeX">';
                            echo '<td class="text-center clickble"><a href="index.php?content=newEmployeeAccess.php&employeeAccessId='.$employeeAccess->getId().'&action=edit"><span class="fa fa-hand-o-left text-primary"></span></a></td>';
                            echo '<td class="text-center clickble"><a href="index.php?content=newEmployeeAccess.php&employeeAccessId='.$employeeAccess->getId().'&action=view"><span class="fa fa-search text-primary"></span></a></td>';
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
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
