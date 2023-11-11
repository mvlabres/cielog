<?php

require_once('../conn.php');
require_once('../session.php');
require_once('../utils.php'); 
require_once('../controller/driverAccessController.php');
require_once('../model/driverAccess.php');

$driverAccessController = new DriverAccessController($MySQLi);

$driversAccessResult = $driverAccessController->findAll();
if($driversAccessResult->hasError) errorAlert($driversAccessResult->result.$driversAccessResult->errorMessage);

?>
<body>
    <div class="row">
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
                <a href="#" class="quick-action-box box-orange1" >
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
        <div class="col-lg-12">
            <div class="panel-title">
                <h3 class="display-2">Acessos de <b>VEÍCULOS</b> em aberto</h3>
            </div>
            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                <thead>
                    <tr>
                        <th scope="column" class="td-40">Finalizar</th>
                        <th scope="column" class="td-40">Detalhes</th>
                        <th scope="column" class="td-70">Entrada</th>
                        <th scope="column" class="td-70">CPF</th>
                        <th scope="column" class="td-70">Nome</th>
                        <th scope="column" class="td-70">CNH</th>
                        <th scope="column" class="td-70">Vencimento CNH</th>
                        <th scope="column" class="td-70">Transportadora</th>
                        <th scope="column" class="td-70">Saída</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    if(!$driversAccessResult->hasError){
                        foreach ($driversAccessResult->result as $driverAccess) {
                            echo '<tr class="odd gradeX">';
                            echo '<td class="text-center clickble"><a href="index.php?content=newDriverAccess.php&driverAccessId='.$driverAccess->getId().'&action=edit"><span class="fa fa-hand-o-left text-primary"></span></a></td>';
                            echo '<td class="text-center clickble"><a href="index.php?content=newDriverAccess.php&driverAccessId='.$driverAccess->getId().'&action=view"><span class="fa fa-search text-primary"></span></a></td>';
                            echo '<td>'.$driverAccess->getStartDatetime().'</td>';
                            echo '<td>'.$driverAccess->getCpf().'</td>';
                            echo '<td>'.$driverAccess->getDriverName().'</td>';
                            echo '<td>'.$driverAccess->getCnh().'</td>';
                            echo '<td>'.$driverAccess->getCnhExpiration().'</td>';
                            echo '<td>'.$driverAccess->getShippingCompany().'</td>';
                            echo '<td>'.$driverAccess->getEndDatetime().'</td>';

                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
