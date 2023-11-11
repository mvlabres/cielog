<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

if(isset($_GET['action']) && $_GET['action'] != null){
    $action = $_GET['action'];
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
    <div class="col-lg-12">
    <h3 class="page-header" >Colaborador - <span id="title"><?=$title ?></span></h3>
    </div>                
</div>
<div class="row">
    <div class="col-lg-12">
         <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <form role="form-new-user" method="post" action="#">
                        <input  type="hidden" name="id" value="<?=$employee->getId() ?>" >
                        <input  type="hidden" name="action" value="<?=$action ?>" >
                            <div class="form-group">
                                <label>Nome</label>
                                <input class="form-control" name="name" placeholder="Nome" maxlength="100" value="<?=$employee->getName()  ?>" required>
                                <p class="help-block">Insira o nome completo do novo colaborador.</p>
                            </div>
                            <div class="form-group">
                                <label>Matrícula</label>
                                <input class="form-control" name="registration" maxlength="50" placeholder="Matrícula" value="<?=$employee->getRegistration() ?>" required>
                            </div>
                            <div class="form-group">
                                <label>CPF</label>
                                <input style="text-transform: uppercase" class="form-control" name="cpf" maxlength="14" minlength="14" placeholder="CPF" value="<?=$employee->getCpf() ?>" onkeyup="cpfMask(this)" required>
                            </div>
                            <div class="form-group">
                                <label>Empresa</label>
                                <select name="business" id="business" class="form-control">
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
                                <p class="help-block">Informe qual a empresa esse colaborador pertence.</p>
                            </div>
                            <div class="form-group">
                                <label>Veículo</label>
                                <input class="form-control" name="vehicle" maxlength="50" placeholder="Veículo" onkeyup="manageVehiclePlate(this)" value="<?=$employee->getVehicle() ?>">
                                <p class="help-block">Informe uma breve descrição do veículo do colaborador.</p>
                            </div>

                            <div class="form-group">
                                <label>Placa do veículo</label>
                                <input style="text-transform: uppercase" class="form-control" name="vehiclePlate" id="vehiclePlate" maxlength="50" placeholder="Placa do veículo" value="<?=$employee->getVehiclePlate() ?>" <?=$disabledField ?> >
                            </div>
                    
                            <button type="submit" class="btn btn-primary" id="user-save-btn">Salvar</button>
                            <button type="reset" class="btn btn-danger">Cancelar</button>
                        </form>
                    </div>
                </div>    
            </div>
        </div>
    </div>
</div>

       