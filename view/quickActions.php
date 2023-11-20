<?php

require_once('../conn.php');
require_once('../session.php');
require_once('../utils.php'); 
require_once('../controller/driverAccessController.php');
require_once('../model/driverAccess.php');
require_once('../controller/employeeAccessController.php');
require_once('../model/employeeAccess.php');

$driverAccessController = new DriverAccessController($MySQLi);
$employeeAccessController = new EmployeeAccessController($MySQLi);

$listType = 'checked';

$businessId = getClientIfContains();
$business = (is_null($businessId)) ? 'all': $businessId;

if($_GET['list-type'] && $_GET['list-type'] != null){
    if($_GET['list-type'] == 'employee') {
        $listType = null;
    }
}

if(isset($_POST['action']) && $_POST['action'] == 'driver-delete'){
    $driverAccessId = $_POST['driverAccessId'];
    $result = $driverAccessController->delete($driverAccessId);

    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    else successAlert($result->result);
}

if(isset($_POST['action']) && $_POST['action'] == 'employee-delete'){
    $employeeAccessId = $_POST['employeeAccessId'];
    $result = $employeeAccessController->delete($employeeAccessId);

    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    else successAlert($result->result);
}

$driversAccessResult = $driverAccessController->findByNullEndDate();
if($driversAccessResult->hasError) errorAlert($driversAccessResult->result.$driversAccessResult->errorMessage);

$employeesAccessResult = $employeeAccessController->findByNullEndDate();
if($employeesAccessResult->hasError) errorAlert($employeesAccessResult->result.$employeesAccessResult->errorMessage);

$hiddenComponents = '';
$componentsClass = 'row quick-action-toogle-margin';

if($_SESSION['FUNCTION_ACCESS']['edit_access'] == 'hidden') {
    $hiddenComponents = 'hidden';
    $componentsClass = 'row quick-action-toogle-top';
}

?>
<body class="table-quick-actions" >
    <div class="quick-actions" <?=$hiddenComponents  ?> >
        <div class="col-lg-12">
            <div class="panel-title">
                <h3 class="display-2">Ações rápidas</h3>
            </div>
        </div>
        <div class="panel-home">
            <a href="index.php?content=driverList.php" class="quick-action-box box-orange0">
                <div class="box-home-header">
                    <p>Acessos veículos</p>
                </div>
            </a>
            <a href="index.php?content=employeeList.php" class="quick-action-box box-orange1" >
                <div class="box-home-header">
                    <p>Acesso colaborador</p>
                </div>
            </a>
            <a href="index.php?content=newDriver.php" class="quick-action-box box-orange2">
                <div class="box-home-header">
                    <p>Novo motorista</p>
                </div>
            </a>
            <a href="index.php?content=newEmployee.php" class="quick-action-box box-orange3">
                <div class="box-home-header">
                    <p>Novo colaborador</p>
                </div>
            </a>
        </div>
    </div>

    <div class="<?=$componentsClass ?>" >
        <div class="panel-progress">
            <progress id="panel-progress" value="30000" max="30000"></progress>
        </div>
        <div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="automatedTimeSwitch" onchange="HandleChangeAutomatedTimeSwitch()" checked>
                <label class="form-check-label" for="openAccess">
                    Ativar atualização automática
                </label>
            </div>
        </div>
    </div>
    <div class="functions-group">
        <div>
            <input type="checkbox" id="access-type-toogle" <?=$listType ?> data-toggle="toggle" data-on="Veículos" data-off="Colaboradores" data-onstyle="success" data-offstyle="primary" onchange="manageListAccess()">
        </div>
        
        
        <div class="btn-functions-group" >
            <a id="driverExport" href="../export/driverOpenAccessExport.php?business=<?=$business?>"><button type="button" class="btn btn-secondary" ><i class="fa fa-file-excel-o"></i> Exportar</button></a>
            <a id="employeeExport" href="../export/employeeOpenAccessExport.php?business=<?=$business?>"><button type="button" class="btn btn-secondary"><i class="fa fa-file-excel-o"></i> Exportar</button></a>
        </div>
    </div>

    <div class="row table-quick-actions">
        <div class="col-lg-12">
            <div class="panel-title">
                <h3 class="display-2">Acessos de <b id="access-type-label">VEÍCULOS</b> em aberto</h3>
            </div>
            <table width="3500px" class="table table-striped table-bordered table-hover" id="dataTables-example">
                <thead class="vehicle-access">
                    <tr>
                        <?php
                            if($hiddenComponents != 'hidden'){
                                echo '<th scope="column" class="td-40">Finalizar</th>';
                                echo '<th scope="column" class="td-30">Editar</th>';
                            }
                        ?>
                        <th scope="column" class="td-40">Detalhes</th>
                        <th scope="column" class="td-70">Tempo Total</th>
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
                        <?php
                            if($_SESSION['FUNCTION_ACCESS']['delete_access'] != 'hidden'){
                                echo '<th scope="column" class="td-40">Excluir</th>';
                            }
                        ?>

                    </tr>
                </thead>
                <thead class="employee-access" hidden>
                    <tr>
                        <?php
                            if($hiddenComponents != 'hidden'){
                                echo '<th scope="column" class="td-40">Finalizar</th>';
                                echo '<th scope="column" class="td-30">Editar</th>';
                            }
                        ?>
                        <th scope="column" class="td-40">Detalhes</th>
                        <th scope="column" class="td-30">Tempo Total</th>
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
                        <?php
                            if($_SESSION['FUNCTION_ACCESS']['delete_access'] != 'hidden'){
                                echo '<th scope="column" class="td-40">Excluir</th>';
                            }
                        ?>

                    </tr>
                </thead>
                <tbody class="vehicle-access">

                    <?php
                    if(!$driversAccessResult->hasError){
                        foreach ($driversAccessResult->result as $driverAccess) {

                            if(!checkClientDataPermission($driverAccess->getBusinessId())) continue;

                            $now = new DateTime();
                            $startTime = new DateTime(date("Y-m-d H:i", strtotime(str_replace('/', '-', $driverAccess->getStartDatetime() ))));

                            $total = ($now->getTimestamp() - $startTime->getTimestamp()) /60;
                            $hours = floor($total/60);
                            $minutes = round($total - ($hours*60));

                            $time = $hours.':'.$minutes.' hr';

                            echo '<tr class="odd gradeX">';

                            if($hiddenComponents != 'hidden'){
                                echo '<td class="text-center clickble"><a class="cell-action" href="index.php?content=newDriverAccess.php&driverAccessId='.$driverAccess->getId().'&action=close"><span class="fa fa-hand-o-left text-primary"></span></a></td>';
                                echo '<td class="text-center clickble"><a class="cell-action" href="index.php?content=newDriverAccess.php&driverAccessId='.$driverAccess->getId().'&action=edit"><span class="fa fa-edit text-primary"></span></a></td>';
                            }
                            echo '<td class="text-center clickble"><a class="cell-action" href="index.php?content=newDriverAccess.php&driverAccessId='.$driverAccess->getId().'&action=view"><span class="fa fa-search text-primary"></span></a></td>';
                            echo '<td>'.$time.'</td>';
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
                            if($_SESSION['FUNCTION_ACCESS']['delete_access'] != 'hidden'){
                                echo '<td class="text-center clickble" data-toggle="modal" data-target="#driver-'.$driverAccess->getId().'"><span class="fa fa-trash text-primary cell-action"></span></td>';
                            }
                            echo '</tr>';
                            echo '<div class="modal fade" id="driver-'.$driverAccess->getId().'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form role="form-delete" method="post" action="#">
                                                <input type="hidden" name="driverAccessId" value="'.$driverAccess->getId().'">
                                                <input type="hidden" name="action" value="driver-delete" >
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Excluir</h4>
                                                </div>
                                                <div class="modal-body">
                                                    Tem certeza que deseja deletar o acesso em aberto de '.$driverAccess->getDriverName().'?
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
                <tbody class="employee-access" hidden>

                    <?php
                    if(!$employeesAccessResult->hasError){
                        foreach ($employeesAccessResult->result as $employeeAccess) {

                            $employee = $employeeAccess->getEmployee();
                            
                            if(!checkClientDataPermission($employee->getBusinessId())) continue;

                            $now = new DateTime();
                            $startTime = new DateTime(date("Y-m-d H:i", strtotime(str_replace('/', '-', $employeeAccess->getStartDatetime() ))));

                            $total = ($now->getTimestamp() - $startTime->getTimestamp()) /60;
                            $hours = floor($total/60);
                            $minutes = round($total - ($hours*60));

                            $time = $hours.':'.$minutes.' hr';

                            echo '<tr class="odd gradeX">';
                            if($hiddenComponents != 'hidden'){
                                echo '<td class="text-center clickble"><a class="cell-action" href="index.php?content=newEmployeeAccess.php&employeeAccessId='.$employeeAccess->getId().'&action=close"><span class="fa fa-hand-o-left text-primary"></span></a></td>';
                                echo '<td class="text-center clickble"><a class="cell-action" href="index.php?content=newEmployeeAccess.php&employeeAccessId='.$employeeAccess->getId().'&action=edit"><span class="fa fa-edit text-primary"></span></a></td>';
                            }
                            echo '<td class="text-center clickble"><a class="cell-action" href="index.php?content=newEmployeeAccess.php&employeeAccessId='.$employeeAccess->getId().'&action=view"><span class="fa fa-search text-primary"></span></a></td>';
                            echo '<td>'.$time.'</td>';
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
                            if($_SESSION['FUNCTION_ACCESS']['delete_access'] != 'hidden'){
                                echo '<td class="text-center clickble" data-toggle="modal" data-target="#emplyee-'.$employeeAccess->getId().'"><span class="fa fa-trash text-primary cell-action"></span></td>';
                            }
                            echo '</tr>';
                            echo '<div class="modal fade" id="emplyee-'.$employeeAccess->getId().'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form role="form-delete" method="post" action="#">
                                                <input type="hidden" name="employeeAccessId" value="'.$employeeAccess->getId().'">
                                                <input type="hidden" name="action" value="employee-delete" >
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Excluir</h4>
                                                </div>
                                                <div class="modal-body">
                                                    Tem certeza que deseja deletar o acesso em aberto de '.$employee->getName().'?
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
