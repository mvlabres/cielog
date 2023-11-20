<?php

require_once('../model/errorHandler.php');
require_once('../model/driver.php');
require_once('../controller/driverController.php');
require_once('../utils.php');

$driverController = new DriverController($MySQLi);
$driverResult = null;

if(isset($_GET['action']) && $_GET['action'] != null){
    successAlert('Registro salvo com sucesso'); 
}

if(isset($_POST['action']) && $_POST['action'] == 'delete'){

    $driverId = $_POST['idDelete'];
    $result = $driverController->delete($driverId);

    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    else successAlert($result->result);
}

$driverResult = $driverController->findAll();
if($driverResult->hasError) errorAlert($driverResult->result.$driverResult->errorMessage);


?>

<div class="row row-space-between">
    <div class="col-lg-12">
        <h3 class="page-header" >Motoristas, visitantes e outros</h3>
    </div>  
    <div class="center-box">
        <a href="index.php?content=newDriver.php" type="button" class="btn btn-primary" id="user-save-btn">Novo</a>
    </div>             
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-body">
                    <table style="width:2850px" class="table table-striped table-bordered table-hover" id="dataTables-example">
                        <thead>
                            <tr>
                                <td scope="column" class="td-30">Acesso</td>
                                <td scope="column" class="td-30">Detalhes</td>
                                <td scope="column" class="td-30">Editar</td>
                                <th scope="column" class="td-120">Nome</th>
                                <th scope="column" class="td-40">CPF</th>
                                <th scope="column" class="td-70">Tipo cadastro</th>
                                <th scope="column" class="td-40">CNH</th>
                                <th scope="column" class="td-100">Vencimento CNH</th>
                                <th scope="column" class="td-70">Transportadora</th>
                                <th scope="column" class="td-70">Tipo veículo</th>
                                <th scope="column" class="td-70">Placa Veículo</th>
                                <th scope="column" class="td-70">Segunda placa</th>
                                <th scope="column" class="td-70">Terceira placa</th>
                                <th scope="column" class="td-70">Status</th>
                                <th scope="column" class="td-100">Motivo do bloqueio</th>
                                <th scope="column" class="td-30">Excluir</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            if(!$driverResult->hasError){
                                foreach ($driverResult->result as $driver) {
                                    echo '<tr class="odd gradeX">';
                                    echo '<td class="text-center clickble"><a class="cell-action" href="index.php?content=newDriverAccess.php&driverId='.$driver->getId().'"><span class="fa fa-hand-o-right text-primary"></span></a></td>';
                                    echo '<td class="text-center clickble"><a class="cell-action" href="index.php?content=newDriver.php&driverId='.$driver->getId().'&action=view"><span class="fa fa-search text-primary"></span></a></td>';
                                    echo '<td class="text-center clickble"><a class="cell-action" href="index.php?content=newDriver.php&driverId='.$driver->getId().'&action=edit"><span class="fa fa-edit text-primary"></span></a></td>';
                                    echo '<td>'.$driver->getName().'</td>';
                                    echo '<td>'.$driver->getCpf().'</td>';
                                    echo '<td>'.$DRIVER_RECORD_TYPES[$driver->getRecordType()].'</td>';
                                    echo '<td>'.$driver->getCnh().'</td>';
                                    echo '<td>'.$driver->getCnhExpiration().'</td>';
                                    echo '<td>'.$driver->getShippingCompany().'</td>';
                                    echo '<td>'.$driver->getVehicleType().'</td>';
                                    echo '<td>'.$driver->getVehiclePlate().'</td>';
                                    echo '<td>'.$driver->getVehiclePlate2().'</td>';
                                    echo '<td>'.$driver->getVehiclePlate3().'</td>';
                                    echo '<td>'.$DRIVER_STATUS[$driver->getStatus()].'</td>';
                                    echo '<td>'.$driver->getBlockReason().'</td>';
                                    echo '<td class="text-center clickble" data-toggle="modal" data-target="#'.$driver->getId().'"><span class="fa fa-trash text-primary"></span></td>';
                                    echo '</tr>';
                                    echo '<div class="modal fade" id="'.$driver->getId().'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form role="form-delete" method="post" action="#">
                                                <input type="hidden" name="idDelete" value="'.$driver->getId().'">
                                                <input type="hidden" name="action" value="delete" >
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Excluir</h4>
                                                </div>
                                                <div class="modal-body">
                                                    Tem certeza que deseja deletar '.$driver->getName().'?
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
    
       