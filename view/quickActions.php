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

if($_GET['list-type'] && $_GET['list-type'] != null){
    if($_GET['list-type'] == 'employee') $listType = null;
}

$driversAccessResult = $driverAccessController->findByNullEndDate();
if($driversAccessResult->hasError) errorAlert($driversAccessResult->result.$driversAccessResult->errorMessage);

$employeesAccessResult = $employeeAccessController->findByNullEndDate();
if($employeesAccessResult->hasError) errorAlert($employeesAccessResult->result.$employeesAccessResult->errorMessage);

?>
<body>
    <div class="row quick-actions" >
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
    <div class="row">
        <input type="checkbox" id="access-type-toogle" <?=$listType ?> data-toggle="toggle" data-on="Veículos" data-off="Colaboradores" data-onstyle="success" data-offstyle="primary" onchange="manageListAccess()">
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel-title">
                <h3 class="display-2">Acessos de <b id="access-type-label">VEÍCULOS</b> em aberto</h3>
            </div>
            <table width="3290px" class="table table-striped table-bordered table-hover" id="dataTables-example">
                <thead class="vehicle-access">
                    <tr>
                        <th scope="column" class="td-30">Finalizar</th>
                        <th scope="column" class="td-30">Detalhes</th>
                        <th scope="column" class="td-40">Turno</th>
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

                    </tr>
                </thead>
                <thead class="employee-access" hidden>
                    <tr>
                        <th scope="column" class="td-30">Finalizar</th>
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

                    </tr>
                </thead>
                <tbody class="vehicle-access">

                    <?php
                    if(!$driversAccessResult->hasError){
                        foreach ($driversAccessResult->result as $driverAccess) {
                            echo '<tr class="odd gradeX">';
                            echo '<td class="text-center clickble"><a href="index.php?content=newDriverAccess.php&driverAccessId='.$driverAccess->getId().'&action=edit"><span class="fa fa-hand-o-left text-primary"></span></a></td>';
                            echo '<td class="text-center clickble"><a href="index.php?content=newDriverAccess.php&driverAccessId='.$driverAccess->getId().'&action=view"><span class="fa fa-search text-primary"></span></a></td>';
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
                        }
                    }
                    ?>
                </tbody>
                <tbody class="employee-access" hidden>

                    <?php
                    if(!$employeesAccessResult->hasError){
                        foreach ($employeesAccessResult->result as $employeeAccess) {

                            $employee = $employeeAccess->getEmployee();

                            echo '<tr class="odd gradeX">';
                            echo '<td class="text-center clickble"><a href="index.php?content=newEmployeeAccess.php&employeeAccessId='.$employeeAccess->getId().'&action=edit"><span class="fa fa-hand-o-left text-primary"></span></a></td>';
                            echo '<td class="text-center clickble"><a href="index.php?content=newEmployeeAccess.php&employeeAccessId='.$employeeAccess->getId().'&action=view"><span class="fa fa-search text-primary"></span></a></td>';
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
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
