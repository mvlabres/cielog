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

$action = 'save';
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
        <h3 class="page-header" >Acesso</h3>
    </div>
</div>
<div class="row">
    <div class="panel panel-default">
        <div class="panel-body">
            <form method="post" action="#">
                <div class="row">
                    <div class="col-lg-12">
                        <input  type="hidden" name="id" value="<?=$driver->getId() ?>" >
                        <input  type="hidden" name="action" value="<?=$action ?>" >
                        <div class="col-lg-6">
                            <div class="col-lg-4">
                                <div>
                                    <img class="profile-image" src="../images/profile.jpg"/>
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
                                <div class="btn-group-start">
                                    <a href="index.php?content=driverList.php" class="btn btn-outline-primary">Alterar motorista</a>
                                    <a href="index.php?content=newDriver.php&driverId=<?=$driver->getId() ?>&action=edit" class="btn btn-outline-primary">Editar dados motorista</a>
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
                                    <label>Tipo de veículo</label>
                                    <select name="vehicleType" class="form-control">
                                        <option value="">Selecione...</option>
                                        <?php

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
                                    <input style="text-transform: uppercase" class="form-control" name="vehiclePlate" id="vehiclePlate" maxlength="10" placeholder="Placa do veículo" value="<?=$driverAccess->getVehiclePlate() ?>" >
                                </div>

                                <div class="form-group">
                                    <label>Placa do veículo (segunda placa)</label>
                                    <input style="text-transform: uppercase" class="form-control" name="vehiclePlate2" id="vehiclePlate2" maxlength="10" placeholder="Segunda placa" value="<?=$driverAccess->getVehiclePlate2() ?>" >
                                </div>

                                <div class="form-group">
                                    <label>Placa do veículo (terceira placa)</label>
                                    <input style="text-transform: uppercase" class="form-control" name="vehiclePlate3" id="vehiclePlate3" maxlength="10" placeholder="Terceira placa" value="<?=$driverAccess->getVehiclePlate3() ?>" >
                                </div>

                                <div class="form-group">
                                    <label>Empresa visitada</label>
                                    <select name="business" class="form-control" required>
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
                                    <label>NF de entrada</label>
                                    <input class="form-control" name="inboundInvoice" id="inboundInvoice" maxlength="20" placeholder="NF de entrada" value="<?=$driverAccess->getInboundInvoice() ?>" >
                                </div>
                                <div class="form-group">
                                    <label>NF de saída</label>
                                    <input class="form-control" name="outboundInvoice" id="outboundInvoice" maxlength="20" placeholder="NF de saída" value="<?=$driverAccess->getOutboundInvoice() ?>" >
                                </div>
                                <div class="panel panel-default">
                                    <div class="panel-body color-gray">
                                        <div class="col-lg-12">
                                            <div class="row">
                                                <div class="form-group">
                                                    <label>Data/hora entrada</label>
                                                    <div class='input-group date' id='datetimepicker1'>
                                                        <input type='text' data-date-format="DD/MM/YYYY HH:mm" class="form-control" value="<?=$driverAccess->getStartDatetime() ?>" name="startDate" onblur="dateTimeHandleBlur(this)" minlength="19" maxlength="19"/>
                                                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Data/hora saída</label>
                                                    <div class='input-group date' id='datetimepicker1'>
                                                        <input type='text' data-date-format="DD/MM/YYYY HH:mm" class="form-control" value="<?=$driverAccess->getEndDatetime() ?>" name="startDate" onblur="dateTimeHandleBlur(this)" minlength="19" maxlength="19"/>
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
                <div class="btn-group-end">
                    <button type="submit" class="btn btn-primary" id="user-save-btn">Criar acesso</button>
                    <button type="reset" class="btn btn-danger">Cancelar</button>
                </div>   
            </form>
        </div>
    </div>
</div>

       