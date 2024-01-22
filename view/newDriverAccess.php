<?php

require_once('../utils.php');
require_once('../model/driver.php');
require_once('../controller/driverController.php');
require_once('../model/vehicleType.php');
require_once('../controller/vehicleTypeController.php');
require_once('../controller/driverAccessController.php');
require_once('../model/driverAccess.php');
require_once('../model/client.php');
require_once('../controller/clientController.php');

$driverController = new DriverController($MySQLi);
$vehicleTypeController = new VehicleTypeController($MySQLi);
$driverAccessController = new DriverAccessController($MySQLi);
$clientController = new ClientController($MySQLi);
$driver = new Driver();
$driverAccess = new DriverAccess();

$hasError = false;
$recordValidationMesssage = null;

$action = 'save';
$disabledBlockReasonField = 'disabled';
$disabledPlateFields = 'disabled';
$blockDisabled = '';
$disabledEndDate = 'disabled';
$btnLabel = 'Criar acesso';

$dateNow = date("d/m/Y H:i");
$endDate = null;
$hiddenComponents = '';

$rotation = setRotation();

if(isset($_GET['action']) && $_GET['action'] != null){
    $action = $_GET['action'];
}

if(isset($_GET['action']) && $_GET['action'] == 'edit'){
    $btnLabel = 'Salvar';
}

if(isset($_GET['action']) && $_GET['action'] == 'edit-all'){
    $btnLabel = 'Salvar';
    $disabledEndDate = '';
}

if(isset($_GET['action']) && $_GET['action'] == 'view'){
    $viewMode = 'disabled';
    $hiddenComponents = 'hidden';
}

if(isset($_GET['action']) && $_GET['action'] == 'close'){
    $endDate = $dateNow;
    $btnLabel = 'Encerrar acesso';
    $disabledEndDate = '';
}

if(isset($_GET['driverAccessId']) && $_GET['driverAccessId'] != null){

    $driverAccessId = $_GET['driverAccessId'];
    $result = $driverAccessController->findById($driverAccessId);

    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    
    $driverAccess = $result->result;

    $driver = $driverAccess->getDriver();
}

if(isset($_POST['driverId']) && $_POST['driverId'] != null){

    echo 'save';

    $result = $driverAccessController->save($_POST, $action);

    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    else echo "<script>window.location='index.php?action=access-save&list-type=driver'</script>";
}

//verifica a entrada via get, quando vem da tela de lista de motoristas
if(isset($_GET['driverId']) && $_GET['driverId'] != null){

    $driverId = $_GET['driverId'];
    $title = 'Editar';

    $result = $driverController->findById($driverId);
    
    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    
    $driver = $result->result;

    $driverAccess->setVehicleType($driver->getVehicleType());
    $driverAccess->setVehiclePlate($driver->getVehiclePlate());
    $driverAccess->setVehiclePlate2($driver->getVehiclePlate2());
    $driverAccess->setVehiclePlate3($driver->getVehiclePlate3());

    if($driver->getStatus() == 'block'){
        warningAlert('Motorista bloqueado');
        $blockDisabled = 'disabled';
    }

    $cnhExpirationDate = date("Y-m-d", strtotime(str_replace('/', '-', $driver->getCnhExpiration() )));

    if($cnhExpirationDate < date("Y-m-d")){
        $hasError = true;
        $recordValidationMesssage = 'Motorista com CNH vencida!';
        warningAlert($recordValidationMesssage);
        $blockDisabled = 'disabled';
    }else{
        if(is_null($driver->getPhone())){
            $hasError = true;
            $recordValidationMesssage = 'Motorista sem TELEFONE informado. Favor atualizar o cadastro!';
            warningAlert($recordValidationMesssage);
            $blockDisabled = 'disabled';
        }
    } 
}

$vehicleTypesResult = $vehicleTypeController->findAll();
if($vehicleTypesResult->hasError) errorAlert($vehicleTypesResult->result.$vehicleTypesResult->errorMessage);

$driversResult = $driverController->findAll();
if($driversResult->hasError) errorAlert($driversResult->result.$driversResult->errorMessage);

$clientsResult = $clientController->findAll();
if($clientsResult->hasError) errorAlert($clientsResult->result.$clientsResult->errorMessage);

?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header" >Acesso de veículos</h1>
        <?php 
        if($hasError){
            echo '<p class="text-danger">ATENÇÃO: '.$recordValidationMesssage.'<p>';
        }
        ?>
    </div>
</div>
<div class="row">
    <div class="panel panel-default">
        <div class="panel-body">
            <form method="post" action="#" onsubmit="return checkDriverAccessSubmit()">
                <div class="row">
                    <div class="col-lg-12">
                        <input type="hidden" name="accessId" value="<?=$driverAccess->getId() ?>" >
                        <input type="hidden" name="driverId" value="<?=$driver->getId() ?>" >
                        <input type="hidden" name="action" value="<?=$action ?>" >
                        <input type="hidden" name="rotation" value="<?=(is_null($driverAccess->getRotation())) ? $rotation : $driverAccess->getRotation() ?>">
                        <div class="col-lg-6">
                            <div class="col-lg-4">
                                <div class="photo-box-action">
                                    <img class="profile-image" src="<?=$driver->getImageProfilePath() ?>"/>
                                </div>
                                <div class="form-group">
                                    <label>CNH</label>
                                    <input class="form-control" value="<?=$driver->getCnh() ?>"  disabled>
                                </div>
                                <div class="form-group">
                                    <label>Vencimento CNH</label>
                                    <div class='input-group date' id='datetimepicker1'>
                                        <input type='text' data-date-format="DD/MM/YYYY" class="form-control" value="<?=$driver->getCnhExpiration() ?>" name="cnhExpiration" id="cnhExpiration" minlength="19" maxlength="19" disabled/>
                                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>CPF</label>
                                    <input class="form-control" value="<?=$driver->getCpf() ?>" disabled>
                                </div>
                            </div>
                            <div class="col-lg-8">

                                <?php
                                    if($action != 'edit' && $action != 'view' && $action != 'edit-all'){
                                        echo '<div class="btn-group-start">
                                            <a href="index.php?content=driverList.php" class="btn btn-outline-primary">Alterar motorista</a>
                                            <a href="index.php?content=newDriver.php&driverId='.$driver->getId().'&action=edit" class="btn btn-outline-primary">Editar dados motorista</a>
                                        </div>';   
                                    } 
                            
                                ?>
                                <div class="form-group">
                                    <label>Turno</label>
                                    <p><?=(is_null($driverAccess->getRotation())) ? $rotation : $driverAccess->getRotation() ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Nome</label>
                                    <input class="form-control"  value="<?=$driver->getName()  ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Transportadora</label>
                                    <input  class="form-control" value="<?=$driver->getShippingCompany() ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Status do cadastro</label>
                                    <input class="form-control" value="<?=$DRIVER_STATUS[$driver->getStatus()]  ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Motivo do bloqueio</label>
                                    <textarea class="form-control" disabled><?=$driver->getBlockReason()  ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Telefone</label>
                                    <input class="form-control" value="<?=$driver->getPhone() ?>"  disabled>
                                </div>
                                <div class="form-group">
                                    <label>Tipo de veículo</label>
                                    <select name="vehicleType" class="form-control" <?=$blockDisabled ?> <?=$viewMode ?>>
                                        <option value="">Selecione...</option>
                                        <?php

                                        echo 'tipo de veiculo: '.$driverAccess->getVehicleType();

                                        if(!$vehicleTypesResult->hasError){
                                            foreach ($vehicleTypesResult->result as $vehicleType) {
                                                $selected = null;
                                                if($driverAccess->getVehicleType() == $vehicleType->getName()) $selected = 'selected';

                                                echo '<option value="'.$vehicleType->getName().'" '.$selected.' >'.$vehicleType->getName().'</option>';

                                            }
                                        }
                                            
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Placa do veículo</label>
                                    <input style="text-transform: uppercase" class="form-control" name="vehiclePlate" id="vehiclePlate" maxlength="10" placeholder="Placa do veículo" value="<?=$driverAccess->getVehiclePlate() ?>" onkeyup="plateMask(event, this)" <?=$blockDisabled ?> <?=$viewMode ?>>
                                </div>

                                <div class="form-group">
                                    <label>Placa do veículo (segunda placa)</label>
                                    <input style="text-transform: uppercase" class="form-control" name="vehiclePlate2" id="vehiclePlate2" maxlength="10" placeholder="Segunda placa" value="<?=$driverAccess->getVehiclePlate2() ?>" onkeyup="plateMask(event, this)" <?=$blockDisabled ?> <?=$viewMode ?>>
                                </div>

                                <div class="form-group">
                                    <label>Placa do veículo (terceira placa)</label>
                                    <input style="text-transform: uppercase" class="form-control" name="vehiclePlate3" id="vehiclePlate3" maxlength="10" placeholder="Terceira placa" value="<?=$driverAccess->getVehiclePlate3() ?>" onkeyup="plateMask(event, this)" <?=$blockDisabled ?> <?=$viewMode ?>>
                                </div>

                                <div class="form-group">
                                    <label>Empresa visitada</label><span class="required-icon">*</span>
                                    <select name="business" class="form-control" <?=$blockDisabled ?> <?=$viewMode ?> required>
                                        <option value="">Selecione...</option>
                                        <?php

                                        if(!$clientsResult->hasError){
                                            foreach ($clientsResult->result as $business) {
                                                $selected = null;
                                                if($driverAccess->getBusinessId() == $business->getId()) $selected = 'selected';

                                                echo '<option value="'.$business->getId().'" '.$selected.' >'.$business->getName().'</option>';

                                            }
                                        }
                                            
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Tipo de operação</label><span class="required-icon">*</span>
                                    <select name="operationType" class="form-control" <?=$blockDisabled ?> <?=$viewMode ?> required>
                                        <option value="">Selecione...</option>
                                        <?php

                                        foreach ($OPERATION_TYPES as $operationType) {
                                            $selected = null;
                                            if($driverAccess->getOperationType() == $operationType) $selected = 'selected';

                                            echo '<option value="'.$operationType.'" '.$selected.' >'.$operationType.'</option>';

                                        }
   
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>NF de entrada</label>
                                    <input class="form-control" name="inboundInvoice" id="inboundInvoice" maxlength="150" placeholder="NF de entrada" value="<?=$driverAccess->getInboundInvoice() ?>" <?=$blockDisabled ?> <?=$viewMode ?>>
                                </div>
                                <div class="form-group">
                                    <label>NF de saída</label>
                                    <input class="form-control" name="outboundInvoice" id="outboundInvoice" maxlength="150" placeholder="NF de saída" value="<?=$driverAccess->getOutboundInvoice() ?>" <?=$blockDisabled ?> <?=$viewMode ?>>
                                </div>
                                <div class="panel panel-default">
                                    <div class="panel-body color-gray">
                                        <div class="col-lg-12">
                                            <div class="row">
                                                <div class="form-group">
                                                    <label>Data/hora entrada</label><span class="required-icon">*</span>
                                                    <div class='input-group date' id='datetimepicker1'>
                                                        <input type='text' data-date-format="DD/MM/YYYY HH:mm" class="form-control" value="<?=(is_null($driverAccess->getStartDatetime())) ? $dateNow : $driverAccess->getStartDatetime() ?>" name="startDate" onblur="dateTimeHandleBlur(this)" minlength="19" maxlength="19" <?=$blockDisabled ?> <?=$viewMode ?>/>
                                                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Data/hora saída</label>
                                                    <div class='input-group date' id='datetimepicker1'>
                                                        <input type='text' data-date-format="DD/MM/YYYY HH:mm" class="form-control" value="<?=(is_null($driverAccess->getEndDatetime())) ? $endDate : $driverAccess->getEndDatetime() ?>" name="endDate" onblur="dateTimeHandleBlur(this)" onkeyup="manageEndDate(this)" minlength="19" maxlength="19" <?=$blockDisabled ?> <?=$disabledEndDate ?> <?=$viewMode ?>/>
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
                </div> 
                <div class="btn-group-end" <?=$hiddenComponents ?>>
                    <button type="submit" class="btn btn-primary" id="user-save-btn" <?=$blockDisabled ?> <?=$viewMode ?>><?=$btnLabel ?></button>
                    <button type="reset" class="btn btn-danger">Cancelar</button>
                </div>   
            </form>
        </div>
    </div>
</div>

       