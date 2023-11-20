<?php

require_once('../utils.php');
require_once('../model/employee.php');
require_once('../controller/employeeController.php');
require_once('../controller/employeeAccessController.php');
require_once('../model/employeeAccess.php');

$employeeController = new employeeController($MySQLi);
$employeeAccessController = new employeeAccessController($MySQLi);
$employee = new employee();
$employeeAccess = new employeeAccess();

$action = 'save';
$disabledPlateField = 'disabled';
$disabledEndDate = 'disabled';
$btnLabel = 'Criar acesso';

$dateNow = date("d/m/Y H:i");
$endDate = null;

$viewMode = '';
$hiddenComponents = '';

$rotation = setRotation();

if(isset($_GET['action']) && $_GET['action'] != null){
    $action = $_GET['action'];
}

if(isset($_GET['action']) && $_GET['action'] == 'view'){
    $viewMode = 'disabled';
    $hiddenComponents = 'hidden';
}

if(isset($_GET['action']) && $_GET['action'] == 'edit'){
    $endDate = null;
    $btnLabel = 'Salvar';
}

if(isset($_GET['action']) && $_GET['action'] == 'edit-all'){
    $btnLabel = 'Salvar';
    $disabledEndDate = '';
}

if(isset($_GET['action']) && $_GET['action'] == 'close'){
    $endDate = $dateNow;
    $btnLabel = 'Encerrar acesso';
    $disabledEndDate = '';
    $endDate = $dateNow;
}

if(isset($_GET['employeeAccessId']) && $_GET['employeeAccessId'] != null){

    $employeeAccessId = $_GET['employeeAccessId'];
    $result = $employeeAccessController->findById($employeeAccessId);

    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    
    $employeeAccess = $result->result;

    $employee = $employeeAccess->getemployee();
}

if(isset($_POST['employeeId']) && $_POST['employeeId'] != null){

    $result = $employeeAccessController->save($_POST, $action);

    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    else echo "<script>window.location='index.php?action=access-save&list-type=employee'</script>";
}

//verifica a entrada via get, quando vem da tela de lista de colaboradores
if(isset($_GET['employeeId']) && $_GET['employeeId'] != null){

    $employeeId = $_GET['employeeId'];
    $title = 'Editar';

    $result = $employeeController->findById($employeeId);
    
    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    
    $employee = $result->result;

    $employeeAccess->setVehicle($employee->getVehicle());
    $employeeAccess->setVehiclePlate($employee->getVehiclePlate());
}

$employeesResult = $employeeController->findAll();
if($employeesResult->hasError) errorAlert($employeesResult->result.$employeesResult->errorMessage);

?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header" >Acesso de colaboradores</h1>
    </div>
</div>
<div class="row">
    <div class="panel panel-default">
        <div class="panel-body">
            <form method="post" action="#" onsubmit="return checkEmployeeAccessSubmit()">
                <div class="row">
                    <div class="col-lg-12">
                        <input  type="hidden" name="accessId" value="<?=$employeeAccess->getId() ?>" >
                        <input  type="hidden" name="employeeId" value="<?=$employee->getId() ?>" >
                        <input  type="hidden" name="action" value="<?=$action ?>" >
                        <input type="hidden" name="rotation" value="<?=(is_null($employeeAccess->getRotation())) ? $rotation : $employeeAccess->getRotation() ?>">
                        <div class="col-lg-8">
                            <div class="col-lg-3">
                                <div class="photo-box-action">
                                    <img class="profile-image" src="<?=$employee->getImageProfilePath() ?>"/>
                                </div>
                            </div>
                            <div class="col-lg-9">

                                <?php
                                    if($action != 'edit' && $action != 'view' && $action != 'edit-all'){
                                        echo '<div class="btn-group-start">
                                            <a href="index.php?content=employeeList.php" class="btn btn-outline-primary">Alterar colaborador</a>
                                            <a href="index.php?content=newemployee.php&employeeId='.$employee->getId().'&action=edit" class="btn btn-outline-primary">Editar dados colaborador</a>
                                        </div>';   
                                    } 
                            
                                ?>
                                <div class="form-group">
                                    <label>Turno</label>
                                    <p><?=(is_null($employeeAccess->getRotation())) ? $rotation : $employeeAccess->getRotation() ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Nome</label>
                                    <input class="form-control" value="<?=$employee->getName() ?>"  disabled>
                                </div>
                                <div class="form-group">
                                    <label>CPF</label>
                                    <input class="form-control" value="<?=$employee->getCpf() ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Matrícula</label>
                                    <input class="form-control" value="<?=$employee->getRegistration() ?>"  disabled>
                                </div>
                                <div class="form-group">
                                    <label>Empresa</label>
                                    <input class="form-control" value="<?=$employee->getBusinessName() ?>" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Veículo</label>
                                <input class="form-control" name="vehicle" value="<?=$employee->getVehicle() ?>" maxlength="50" disabled>
                            </div>
                            <div class="form-group">
                                <label>Placa do veículo</label>
                                <input style="text-transform: uppercase" class="form-control" name="vehiclePlate" id="vehiclePlate" maxlength="10" placeholder="Placa do veículo" value="<?=$employee->getVehiclePlate() ?>" onkeyup="plateMask(event, this)" disabled>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-body color-gray">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="form-group">
                                                <label>Data/hora entrada</label><span class="required-icon">*</span>
                                                <div class='input-group date' id='datetimepicker1'>
                                                    <input type='text' data-date-format="DD/MM/YYYY HH:mm" class="form-control" value="<?=(is_null($employeeAccess->getStartDatetime())) ? $dateNow : $employeeAccess->getStartDatetime() ?>" name="startDate" onblur="dateTimeHandleBlur(this)" minlength="19" maxlength="19" <?=$viewMode ?>/>
                                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Data/hora saída</label>
                                                <div class='input-group date' id='datetimepicker1'>
                                                    <input type='text' data-date-format="DD/MM/YYYY HH:mm" class="form-control" value="<?=(is_null($employeeAccess->getEndDatetime())) ? $endDate : $employeeAccess->getEndDatetime() ?>" name="endDate" onblur="dateTimeHandleBlur(this)" onkeyup="manageEndDate(this)" minlength="19" maxlength="19" <?=$disabledEndDate ?> <?=$viewMode ?>/>
                                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
                <div class="btn-group-end" <?=$hiddenComponents ?>>
                    <button type="submit" class="btn btn-primary" id="user-save-btn" <?=$viewMode ?>><?=$btnLabel ?></button>
                    <button type="reset" class="btn btn-danger">Cancelar</button>
                </div>   
            </form>
        </div>
    </div>
</div>

       