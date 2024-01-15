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
$viewMode = '';
$hiddenComponents = '';

if(isset($_GET['action']) && $_GET['action'] != null){
    $action = $_GET['action'];
}

if(isset($_GET['action']) && $_GET['action'] == 'view'){
    $viewMode = 'disabled';
    $hiddenComponents = 'hidden';
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

$shippingCompanysResult = $shippingCompanyController->findAll();
if($shippingCompanysResult->hasError) errorAlert($shippingCompanysResult->result.$shippingCompanysResult->errorMessage);

$vehicleTypesResult = $vehicleTypeController->findAll();
if($vehicleTypesResult->hasError) errorAlert($vehicleTypesResult->result.$vehicleTypesResult->errorMessage);

?>

<div class="row">
    <div class="row row-space-between">
        <div class="col-lg-12">
            <h3 class="page-header" >Motorista - <span id="title"><?=$title ?></span></h3>
        </div>  
        <div class="center-box">
            <a href="index.php?content=driverList.php" type="button" class="btn btn-primary" id="user-save-btn">Lista de motoristas</a>
        </div>             
    </div>                    
</div>
<div class="row">
    <div class="col-lg-12">
         <div class="panel panel-default">
            <div class="panel-body">
                <form id="new-driver-post" method="post" action="#" onsubmit="return checkImageProfile()">
                    <div class="row">
                        <div class="col-lg-6">
                            <input  type="hidden" name="id" value="<?=$driver->getId() ?>" >
                            <input  type="hidden" name="redirect" id="redirect" value="no-redirect" >
                            <input  type="hidden" name="action" value="<?=$action ?>" >
                            <input  type="hidden" name="image-profile" id="image-profile" value="" >
                            <input type="hidden" id="image-profile-check" value="<?=$driver->getImageProfilePath() ?>">
                            <div class="col-lg-4">
                                <div class="photo-box-action">
                                    <img class="profile-image" id="profile-image" src="<?=$driver->getImageProfilePath() ?>"/>
                                    <p id="image-profile-feedback">Favor registrar foto para poder prosseguir</p>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#camera" onclick="startCamera()"><i class="fa fa-camera" aria-hidden="true"></i>&nbsp Capturar imagem</button>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="form-group">
                                    <label>Tipo de cadastro</label><span class="required-icon">*<span>
                                    <select name="recordType" class="form-control" onchange="manageCnhValidation(this, 'MOTORISTA')" <?=$viewMode ?> required>
                                        <option value="">Selecione...</option>

                                        <?php 
                                        foreach ($DRIVER_RECORD_TYPES as $key => $value) {
                                            $selected = null;

                                            if($driver->getRecordType() == $key) $selected = 'selected';


                                            if(is_null($driver->getRecordType()) && $key == 'driver') $selected = 'selected';
                                            echo '<option value="'.$key.'" '.$selected.' >'.$value.'</option>';

                                        }
                                        
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Nome</label><span class="required-icon">*<span>
                                    <input class="form-control" name="name" placeholder="Nome" maxlength="100" value="<?=$driver->getName()  ?>" <?=$viewMode ?> required>
                                </div>
                                <div class="form-group">
                                    <label>CPF</label><span class="required-icon">*<span>
                                    <input style="text-transform: uppercase" class="form-control" name="cpf" maxlength="14" minlength="14" placeholder="CPF" value="<?=$driver->getCpf() ?>" onkeyup="cpfMask(this)" <?=$viewMode ?> required>
                                </div>
                                <div class="form-group">
                                    <label>CNH</label><span id="requiredCnh" class="required-icon">*</span>
                                    <input class="form-control" name="cnh" maxlength="20" placeholder="CNH" value="<?=$driver->getCnh() ?>" <?=$viewMode ?> required>
                                </div>
                                <div class="form-group">
                                    <label>Vencimento CNH</label><span id="requiredCnhExpiration" class="required-icon">*</span>
                                    <div class='input-group date' id='datetimepicker1'>
                                        <input type='text' data-date-format="DD/MM/YYYY" class="form-control" value="<?=$driver->getCnhExpiration() ?>" name="cnhExpiration" id="cnhExpiration" onblur="dateTimeHandleBlur(this)" minlength="19" maxlength="19" <?=$viewMode ?> required/>
                                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-field-action">
                                    <div class="form-group">
                                        <label>Transportadora</label><span id="requiredCnhExpiration" class="required-icon">*</span>
                                        <select name="shippingCompany" id="shippingCompany" class="form-control" <?=$viewMode ?> required>
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
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#new-shipping-company">Criar</button>
                                </div>
                            </div>
                            
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Tipo de veículo</label><span id="requiredCnh" class="required-icon">*</span>
                                <select name="vehicleType" class="form-control" onchange="manageVehicleTypes(this)" <?=$viewMode ?> required>
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
                                <input style="text-transform: uppercase" class="form-control" name="vehiclePlate" id="vehiclePlate" maxlength="10" placeholder="Placa do veículo" value="<?=$driver->getVehiclePlate() ?>" onkeyup="plateMask(event, this)" <?=$disabledPlateFields ?> <?=$viewMode ?>>
                            </div>

                            <div class="form-group">
                                <label>Placa do veículo (segunda placa)</label>
                                <input style="text-transform: uppercase" class="form-control" name="vehiclePlate2" id="vehiclePlate2" maxlength="10" placeholder="Segunda placa" value="<?=$driver->getVehiclePlate2() ?>" onkeyup="plateMask(event, this)" <?=$disabledPlateFields ?> <?=$viewMode ?>>
                            </div>

                            <div class="form-group">
                            <label>Placa do veículo (terceira placa)</label>
                                <input style="text-transform: uppercase" class="form-control" name="vehiclePlate3" id="vehiclePlate3" maxlength="10" placeholder="Terceira placa" value="<?=$driver->getVehiclePlate3() ?>" onkeyup="plateMask(event, this)" <?=$disabledPlateFields ?> <?=$viewMode ?>>
                            </div>

                            <div class="form-group">
                                <label>Status</label><span class="required-icon">*<span>
                                <select name="status" class="form-control" onchange="manegeFieldViewByValue(this, 'blockReason', true, 'block')" <?=$viewMode ?> required>
                                    <option value="">Selecione...</option>
                                    <?php 
                                    foreach ($DRIVER_STATUS as $key => $value) {
                                        $selected = null;

                                        if($driver->getStatus() == $key) $selected = 'selected';

                                        if(is_null($driver->getStatus()) && $key == 'active') $selected = 'selected';
                                        echo '<option value="'.$key.'" '.$selected.' >'.$value.'</option>';

                                    }
                                    
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Motivo do bloqueio</label>
                                <textarea class="form-control" name="blockReason" id="blockReason" maxlength="200" placeholder="Motivo de bloqueio" <?=$disabledBlockReasonField ?> <?=$viewMode ?>><?=$driver->getBlockReason() ?></textarea>
                                <p class="help-block">Descreva por qual motivo esse cadastro foi bloqueado.</p>
                            </div>
                        </div>
                    </div> 
                    <div class="btn-group-end" <?=$hiddenComponents ?>>
                        <button type="submit" class="btn btn-primary" id="user-save-btn">Salvar</button>
                        <button type="button" class="btn btn-primary" id="user-save-btn" onclick="manageRedirect('new-driver-post')">Salvar e criar acesso</button>
                        <button type="reset" class="btn btn-danger">Cancelar</button>
                    </div>   
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="camera" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeCamera()"><span aria-hidden="true">&times;</span></button>
            </div>
            <div id="camera-container">
                <canvas id="canvas"></canvas>
                <video autoplay="true" id="videoElement"></video>
            </div>
            <div class="modal-footer">
                <div class="box-group">
                    <div class="box-btn-center">
                        <button class="camera-action btn btn-primary" type="button" onclick="takepicture()"><i class="fa fa-camera" aria-hidden="true"></i></button>
                    </div>
                    <div id="swap-camera">
                        <a title="Trocar câmera" data-toggle="tooltip" onclick="swapCamera()"><i class="fa fa-refresh fa-2x" aria-hidden="true"></i></a>
                    </div>
                </div>
                <div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="new-shipping-company" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Nova transportadora</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nome da transportadora</label>
                    <input style="text-transform: uppercase" class="form-control" id="new-shipping-company-name" placeholder="Nome" maxlength="50" value="" onkeyup="checkFieldHasValue(this, 'modalButton')">
                    <p class="feedback" id="feedback-modal"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">fechar</button>
                <button type="button" class="btn btn-primary" id="modalButton" onclick="ajaxNewShippingCompany()" disabled>Salvar</button>
            </div>
        </div>
    </div>
</div>


       