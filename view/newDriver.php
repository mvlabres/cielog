<?php

require_once('../utils.php');
require_once('../model/driver.php');
require_once('../model/shippingCompany.php');
require_once('../controller/shippingCompanyController.php');
require_once('../controller/driverController.php');
require_once('../model/vehicleType.php');
require_once('../controller/vehicleTypeController.php');

if($_SESSION['FUNCTION_ACCESS']['register_driver'] == 'hidden') header('LOCATION:index.php');

$driverController = new DriverController($MySQLi);
$shippingCompanyController = new ShippingCompanyController($MySQLi);
$vehicleTypeController = new VehicleTypeController($MySQLi);
$driver = new Driver();

$action = 'save';
$title = 'Criar';
$disabledBlockReasonField = 'disabled';
$disabledPlateFields = 'disabled';

if(isset($_GET['action']) && $_GET['action'] != null){
    $action = $_GET['action'];
}

if(isset($_GET['driverId']) && $_GET['driverId'] != null){

    $driverId = $_GET['driverId'];
    $title = 'Editar';

    $result = $driverController->findById($driverId);
    
    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    
    $driver = $result->result;

    if($driver->getStatus() == 'block' ) $disabledBlockReasonField = '';
    if(!is_null($driver->getVehicleType())) $disabledPlateFields = '';
}

if(isset($_POST['name']) && $_POST['name'] != null){

    $action = $_POST['action'];
    $result = $driverController->save($_POST, $action);

    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    else echo "<script>window.location='index.php?content=driverList.php&action=save'</script>";
}

$driversResult = $driverController->findAll();
if($driversResult->hasError) errorAlert($driversResult->result.$driversResult->errorMessage);

$shippingCompanysResult = $shippingCompanyController->findAll();
if($shippingCompanysResult->hasError) errorAlert($shippingCompanysResult->result.$shippingCompanysResult->errorMessage);

$vehicleTypesResult = $vehicleTypeController->findAll();
if($vehicleTypesResult->hasError) errorAlert($vehicleTypesResult->result.$vehicleTypesResult->errorMessage);

?>

<div class="row">
    <div class="col-lg-12">
    <h3 class="page-header" >Motorista - <span id="title"><?=$title ?></span></h3>
    </div>                
</div>
<div class="row">
    <div class="col-lg-12">
         <div class="panel panel-default">
            <div class="panel-body">
                <form method="post" action="#">
                    <div class="row">
                        <div class="col-lg-6">
                            <input  type="hidden" name="id" value="<?=$driver->getId() ?>" >
                            <input  type="hidden" name="redirect" value="redirect" >
                            <input  type="hidden" name="action" value="<?=$action ?>" >
                            <div class="form-group">
                                <label>Nome</label><span class="required-icon">*<span>
                                <input class="form-control" name="name" placeholder="Nome" maxlength="100" value="<?=$driver->getName()  ?>" required>
                            </div>
                            <div class="form-group">
                                <label>CPF</label><span class="required-icon">*<span>
                                <input style="text-transform: uppercase" class="form-control" name="cpf" maxlength="14" minlength="14" placeholder="CPF" value="<?=$driver->getCpf() ?>" onkeyup="cpfMask(this)" required>
                            </div>
                            <div class="form-group">
                                <label>Tipo de cadastro</label><span class="required-icon">*<span>
                                <select name="recordType" class="form-control" onchange="manageCnhValidation(this, 'MOTORISTA')" required>
                                    <option value="">Selecione...</option>

                                    <?php 
                                    foreach ($DRIVER_RECORD_TYPES as $key => $value) {
                                        $selected = null;

                                        if($driver->getRecordType() == $key) $selected = 'selected';
                                        echo '<option value="'.$key.'" '.$selected.' >'.$value.'</option>';

                                    }
                                    
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>CNH</label><span id="requiredCnh" class="required-icon" hidden>*</span>
                                <input class="form-control" name="cnh" maxlength="20" placeholder="CNH" value="<?=$driver->getCnh() ?>" >
                            </div>
                            <div class="form-group">
                                <label>Vencimento CNH</label><span id="requiredCnhExpiration" class="required-icon" hidden>*</span>
                                <div class='input-group date' id='datetimepicker1'>
                                    <input type='text' data-date-format="DD/MM/YYYY" class="form-control" value="<?=$driver->getCnhExpiration() ?>" name="cnhExpiration" id="cnhExpiration" onblur="dateTimeHandleBlur(this)" minlength="19" maxlength="19"/>
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Transportadora</label>
                                <select name="shippingCompany" class="form-control">
                                    <option value="">Selecione...</option>
                                    <?php

                                    if(!$shippingCompanysResult->hasError){
                                        foreach ($shippingCompanysResult->result as $shippingCompany) {
                                            $selected = null;
                                            if($driver->getShippingCompany() == $shippingCompany->getName()) $selected = 'selected';

                                            echo '<option value="'.$shippingCompany->getName().'" '.$selected.' >'.$shippingCompany->getName().'</option>';

                                        }
                                    }
                                        
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Tipo de veículo</label>
                                <select name="vehicleType" class="form-control" onchange="manageVehicleTypes(this)">
                                    <option value="">Selecione...</option>
                                    <?php

                                    if(!$vehicleTypesResult->hasError){
                                        foreach ($vehicleTypesResult->result as $vehicleType) {
                                            $selected = null;
                                            if($driver->getVehicleType() == $vehicleType->getName()) $selected = 'selected';

                                            echo '<option value="'.$vehicleType->getName().'" '.$selected.' >'.$vehicleType->getName().'</option>';

                                        }
                                    }
                                        
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Placa do veículo</label>
                                <input style="text-transform: uppercase" class="form-control" name="vehiclePlate" id="vehiclePlate" maxlength="10" placeholder="Placa do veículo" value="<?=$driver->getVehiclePlate() ?>" <?=$disabledPlateFields ?> >
                            </div>

                            <div class="form-group">
                                <label>Placa do veículo (segunda placa)</label>
                                <input style="text-transform: uppercase" class="form-control" name="vehiclePlate2" id="vehiclePlate2" maxlength="10" placeholder="Segunda placa" value="<?=$driver->getVehiclePlate2() ?>" <?=$disabledPlateFields ?> >
                            </div>

                            <div class="form-group">
                            <label>Placa do veículo (terceira placa)</label>
                                <input style="text-transform: uppercase" class="form-control" name="vehiclePlate3" id="vehiclePlate3" maxlength="10" placeholder="Terceira placa" value="<?=$driver->getVehiclePlate3() ?>" <?=$disabledPlateFields ?> >
                            </div>

                            <div class="form-group">
                                <label>Status</label><span class="required-icon">*<span>
                                <select name="status" class="form-control" onchange="manegeFieldViewByValue(this, 'blockReason', true, 'block')" required>
                                    <option value="">Selecione...</option>
                                    <?php 
                                    foreach ($DRIVER_STATUS as $key => $value) {
                                        $selected = null;

                                        if($driver->getStatus() == $key) $selected = 'selected';
                                        echo '<option value="'.$key.'" '.$selected.' >'.$value.'</option>';

                                    }
                                    
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Motivo do bloqueio</label>
                                <textarea class="form-control" name="blockReason" id="blockReason" maxlength="200" placeholder="Motivo de bloqueio" <?=$disabledBlockReasonField ?>><?=$driver->getBlockReason() ?></textarea>
                                <p class="help-block">Descreva por qual motivo esse cadastro foi bloqueado.</p>
                            </div>
                        </div>
                    </div> 
                    <div class="btn-group-end">
                        <button type="submit" class="btn btn-primary" id="user-save-btn">Salvar</button>
                        <button type="button" class="btn btn-primary" id="user-save-btn" onclick="manageRedirect()">Salvar e criar acesso</button>
                        <button type="reset" class="btn btn-danger">Cancelar</button>
                    </div>   
                </form>
            </div>
        </div>
    </div>
</div>

       