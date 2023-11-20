<?php

require_once('../utils.php');
require_once('../model/employee.php');
require_once('../model/client.php');
require_once('../controller/clientController.php');
require_once('../controller/employeeController.php');

if($_SESSION['FUNCTION_ACCESS']['new_employee'] == 'hidden') header('LOCATION:index.php');

$employeeController = new EmployeeController($MySQLi);
$clientController = new ClientController($MySQLi);
$employee = new Employee();

$action = 'save';
$title = 'Criar';
$disabledField = 'disabled';
$viewMode = '';
$hiddenComponents = '';

if(isset($_GET['action']) && $_GET['action'] != null){
    $action = $_GET['action'];
}

if(isset($_GET['action']) && $_GET['action'] == 'view'){
    $viewMode = 'disabled';
    $hiddenComponents = 'hidden';
}

if(isset($_GET['employeeId']) && $_GET['employeeId'] != null){

    $employeeId = $_GET['employeeId'];
    $title = 'Editar';

    $result = $employeeController->findById($employeeId);
    
    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    
    $employee = $result->result;

    if(!is_null($employee->getVehicle())) $disabledField = '';
}

if(isset($_POST['name']) && $_POST['name'] != null){

    $action = $_POST['action'];
    $result = $employeeController->save($_POST, $action);

    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    else echo "<script>window.location='index.php?content=employeeList.php&action=save'</script>";
}

$clientsResult = $clientController->findAll();

if($clientsResult->hasError) errorAlert($clientsResult->result.$clientsResult->errorMessage);

?>

<div class="row">
    <div class="row row-space-between">
        <div class="col-lg-12">
            <h3 class="page-header" >Colaborador - <span id="title"><?=$title ?></span></h3>
        </div>  
        <div class="center-box">
            <a href="index.php?content=employeeList.php" type="button" class="btn btn-primary" id="user-save-btn">Lista de colaboradores</a>
        </div>             
    </div>              
</div>
<div class="row">
    <div class="col-lg-12">
         <div class="panel panel-default">
            <div class="panel-body">
                <form id="new-employee-post" role="form-new-user" method="post" action="#" onsubmit="return checkImageProfile()">
                    <div class="row">
                        <div class="col-lg-6">
                            <input  type="hidden" name="id" value="<?=$employee->getId() ?>" >
                            <input  type="hidden" name="redirect" id="redirect" value="no-redirect" >
                            <input  type="hidden" name="action" value="<?=$action ?>" >
                            <input  type="hidden" name="image-profile" id="image-profile" value="<?=$employee->getImageProfilePath() ?>" >
                            <div class="col-lg-4">
                                <div class="photo-box-action" id="photo-box-action">
                                    <img class="profile-image" id="profile-image" src="<?=$employee->getImageProfilePath() ?>"/>
                                    <p id="image-profile-feedback">Favor registrar foto para poder prosseguir</p>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#camera" onclick="startCamera()"><i class="fa fa-camera" aria-hidden="true"></i>&nbsp Capturar imagem</button>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="form-group">
                                    <label>Nome</label>
                                    <input class="form-control" name="name" placeholder="Nome" maxlength="100" value="<?=$employee->getName()  ?>" <?=$viewMode ?> required>
                                    <p class="help-block">Insira o nome completo do novo colaborador.</p>
                                </div>
                                <div class="form-group">
                                    <label>Matrícula</label>
                                    <input class="form-control" name="registration" maxlength="50" placeholder="Matrícula" value="<?=$employee->getRegistration() ?>" <?=$viewMode ?> required>
                                </div>
                                <div class="form-group">
                                    <label>CPF</label>
                                    <input style="text-transform: uppercase" class="form-control" name="cpf" maxlength="14" minlength="14" placeholder="CPF" value="<?=$employee->getCpf() ?>" onkeyup="cpfMask(this)" <?=$viewMode ?> required>
                                </div>
                                
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Veículo</label>
                                <input class="form-control" name="vehicle" maxlength="50" placeholder="Veículo" onkeyup="manageVehiclePlate(this)" value="<?=$employee->getVehicle() ?> " <?=$viewMode ?>>
                                <p class="help-block">Informe uma breve descrição do veículo do colaborador.</p>
                            </div>
                            <div class="form-group">
                                <label>Placa do veículo</label>
                                <input style="text-transform: uppercase" class="form-control" name="vehiclePlate" id="vehiclePlate" maxlength="50" placeholder="Placa do veículo" value="<?=$employee->getVehiclePlate() ?>" <?=$disabledField ?> <?=$viewMode ?>>
                            </div>
                            <div class="form-group">
                                <label>Empresa</label>
                                <select name="business" id="business" class="form-control" <?=$viewMode ?> required>
                                    <option value="">Selecione...</option>
                                    <?php

                                    if(!$clientsResult->hasError){
                                        foreach ($clientsResult->result as $client) {
                                            $selected = null;
                                            if($employee->getBusinessId() == $client->getId()) $selected = 'selected';

                                            echo '<option value="'.$client->getId().'" '.$selected.' >'.$client->getName().'</option>';

                                        }
                                    }
                                        
                                    ?>
                                </select>
                            </div>
                            <div class="btn-group-end" <?=$hiddenComponents ?>>
                                <button type="submit" class="btn btn-primary" id="user-save-btn">Salvar</button>
                                <button type="button" class="btn btn-primary" id="user-save-btn" onclick="manageRedirect('new-employee-post')">Salvar e criar acesso</button>
                                <button type="reset" class="btn btn-danger">Cancelar</button>
                            </div>
                        </div>
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
            </div>
        </div>
    </div>
</div>

       