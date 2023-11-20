<?php

require_once('../model/errorHandler.php');
require_once('../model/employee.php');
require_once('../model/client.php');
require_once('../controller/clientController.php');
require_once('../controller/employeeController.php');
require_once('../utils.php');

$clientController = new ClientController($MySQLi);
$employeeController = new EmployeeController($MySQLi);
$employeeResult;

$filterSelected = 'all';

if(isset($_GET['action']) && $_GET['action'] != null){
    successAlert('Colaborador salvo com sucesso'); 
}

if(isset($_POST['action']) && $_POST['action'] == 'delete'){

    $employeeId = $_POST['idDelete'];
    $result = $employeeController->delete($employeeId);

    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    else successAlert($result->result);
}

if($employeeResult->hasError) errorAlert($employeeResult->result.$employeeResult->errorMessage);

if(isset($_POST['businessFilter']) && $_POST['businessFilter'] != null && $_POST['businessFilter'] != 'all'){

    $filterSelected = $_POST['businessFilter'];
    $employeeResult = $employeeController->findByBusinessId($filterSelected);
    if($employeeResult->hasError) errorAlert($employeeResult->result.$employeeResult->errorMessage);
}

if($filterSelected == 'all'){
    $employeeResult = $employeeController->findAll();
    if($employeeResult->hasError) errorAlert($employeeResult->result.$employeeResult->errorMessage);
}

$clientsResult = $clientController->findAll();
if($clientsResult->hasError) errorAlert($clientsResult->result.$clientsResult->errorMessage);
?>

<div class="row row-space-between">
    <div class="col-lg-12">
        <h3 class="page-header" >Colaboradores</h3>
    </div>  
    <div class="center-box">
        <a href="index.php?content=newEmployee.php" type="button" class="btn btn-primary" id="user-save-btn">Novo</a>
    </div>             
</div>
<div class="row">
    <div class="functions-group">
        <form method="post" id="panel-form" action="index.php?content=employeeList.php">
            <div class="row-element-group">
                <div class="form-group">
                    <label>Empresa</label>
                    <select name="businessFilter" id="business" class="form-control">
                        <option value="">Selecione...</option>
                        <?php
                            if($filterSelected == 'all') echo '<option value="all" selected>Todas</option>';
                            else echo '<option value="all">Todas</option>';
                        ?>
                        <?php

                        if(!$clientsResult->hasError){
                            foreach ($clientsResult->result as $client) {
                                $selected = null;
                                if($client->getId() == $filterSelected) $selected = 'selected';
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
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-body">
                    <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                        <thead>
                            <tr>
                                <td scope="column" class="td-30">Acesso</td>
                                <td scope="column" class="td-30">Detalhes</td>
                                <td scope="column" class="td-30">Editar</td>
                                <th scope="column" class="td-70">Nome</th>
                                <th scope="column" class="td-70">Matrícula</th>
                                <th scope="column" class="td-70">CPF</th>
                                <th scope="column" class="td-70">Empresa</th>
                                <th scope="column" class="td-70">Veículo</th>
                                <th scope="column" class="td-70">Placa Veículo</th>
                                <th scope="column" class="td-30">Excluir</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            if(!$employeeResult->hasError){
                                foreach ($employeeResult->result as $employee) {
                                    echo '<tr class="odd gradeX">';
                                    echo '<td class="text-center clickble"><a class="cell-action" href="index.php?content=newEmployeeAccess.php&employeeId='.$employee->getId().'"><span class="fa fa-hand-o-right text-primary"></span></a></td>';
                                    echo '<td class="text-center clickble"><a class="cell-action" href="index.php?content=newEmployee.php&employeeId='.$employee->getId().'&action=view"><span class="fa fa-search text-primary"></span></a></td>';
                                    echo '<td class="text-center clickble"><a class="cell-action" href="index.php?content=newEmployee.php&employeeId='.$employee->getId().'&action=edit"><span class="fa fa-edit text-primary"></span></a></td>';
                                    echo '<td>'.$employee->getName().'</td>';
                                    echo '<td>'.$employee->getRegistration().'</td>';
                                    echo '<td>'.$employee->getCpf().'</td>';
                                    echo '<td>'.$employee->getBusinessName().'</td>';
                                    echo '<td>'.$employee->getVehicle().'</td>';
                                    echo '<td>'.$employee->getVehiclePlate().'</td>';
                                    echo '<td class="text-center clickble" data-toggle="modal" data-target="#'.$employee->getId().'"><span class="fa fa-trash text-primary"></span></td>';
                                    echo '</tr>';
                                    echo '<div class="modal fade" id="'.$employee->getId().'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form role="form-delete" method="post" action="#">
                                                <input type="hidden" name="idDelete" value="'.$employee->getId().'">
                                                <input type="hidden" name="action" value="delete" >
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Excluir</h4>
                                                </div>
                                                <div class="modal-body">
                                                    Tem certeza que deseja deletar '.$employee->getName().'?
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
        </div>
    </div>
</div>
    
       