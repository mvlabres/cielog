<?php

require_once('../conn.php');
require_once('../session.php');
require_once('../utils.php');
require_once('../controller/driverAccessController.php');
require_once('../controller/clientController.php');
require_once('../model/driverAccess.php');
require_once('../controller/businessClientController.php');

date_default_timezone_set("America/Sao_Paulo");

$driverAccessController = new DriverAccessController($MySQLi);
$clientController = new ClientController($MySQLi);
$businessClientController = new BusinessClientController($MySQLi);

// Obter parâmetros
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 50;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$startDate = isset($_GET['startDate']) ? trim($_GET['startDate']) : '01/'.date("m/Y");
$endDate = isset($_GET['endDate']) ? trim($_GET['endDate']) : date("d/m/Y");
$business = isset($_GET['business']) ? $_GET['business'] : getClientIfContains();
$businessClient = isset($_GET['businessClient']) ? $_GET['businessClient'] : null;
$withOpen = isset($_GET['startDate']) ? (isset($_GET['with-open']) && $_GET['with-open'] == '1') : true;

// Ação de delete
if(isset($_POST['action']) && $_POST['action'] == 'delete'){
    $driverAccessId = $_POST['deleteId'];
    $result = $driverAccessController->delete($driverAccessId);

    if(!$result->hasError) {
        successAlert($result->result);
        header("Location: index.php?content=driverAccessList.php&page=$page&search=".urlencode($search)."&startDate=".urlencode($startDate)."&endDate=".urlencode($endDate)."&business=".urlencode($business)."&businessClient=".urlencode($businessClient)."&with-open=".($withOpen ? '1' : '0'));
        exit;
    }else{
        errorAlert($result->result.$result->errorMessage);
    }
}

// Buscar dados paginados
$driversAccessResult = $driverAccessController->findByStartDateEndDateAndBusinessPaginated($startDate, $endDate, $business, $businessClient, $withOpen, $search, $page, $limit);
if($driversAccessResult->hasError) errorAlert($driversAccessResult->result.$driversAccessResult->errorMessage);

$clientsResult = $clientController->findAll();
if($clientsResult->hasError) errorAlert($clientsResult->result.$clientsResult->errorMessage);

$businessClientsResult = null;
if($business != null && $business != '' && $business != 'all'){
    $businessClientsResult = $businessClientController->findByClientId($business);
    if($businessClientsResult->hasError) errorAlert($businessClientsResult->result.$businessClientsResult->errorMessage);
}

$withOpenExport = ($withOpen) ? 'true': 'false';

?>
<div class="row" style="margin-bottom: 20px;">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><strong><i class="fa fa-filter"></i> Filtros</strong></div>
            <div class="panel-body">
                <form method="GET" id="panel-form" action="">
                    <input type="hidden" name="content" value="driverAccessList.php">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Buscar Acesso</label>
                                <input type="text" name="search" placeholder="Nome, CPF, Placa, Empresa visitada ou Empresa cliente..."
                                       value="<?php echo htmlspecialchars($search); ?>" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group">
                                <label>Data inicial</label>
                                <div class='input-group date' id='datetimepicker1'>
                                    <input name="startDate" id="startDate" type='text' data-date-format="DD/MM/YYYY" class="form-control" value="<?=htmlspecialchars($startDate)?>" onblur="dateTimeHandleBlur(this)" required minlength="10" maxlength="10" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group">
                                <label>Data final</label>
                                <div class='input-group date' id='datetimepicker1'>
                                    <input name="endDate" id="endDate" type='text' data-date-format="DD/MM/YYYY" class="form-control" value="<?=htmlspecialchars($endDate)?>" onblur="dateTimeHandleBlur(this)" required minlength="10" maxlength="10" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label>Empresa visitada</label>
                                <select name="business" id="business" class="form-control" onchange="getBusinessClient(this, true)">
                                    <?php
                                    if(checkUserPermission()){
                                        echo '<option value="all">Todas</option>';
                                    }

                                    if(!$clientsResult->hasError){
                                        foreach ($clientsResult->result as $client) {

                                            if(!checkClientDataPermission($client->getId())) continue;

                                            $selected = '';
                                            if($business == $client->getId()) $selected = 'selected';

                                            echo '<option value="'.$client->getId().'" '.$selected.' >'.$client->getName().'</option>';

                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label>Empresa cliente</label>
                                <select name="businessClient" id="businessClient" class="form-control">
                                    <option value="all">Todas</option>
                                    <?php
                                    if($businessClientsResult != null && !$businessClientsResult->hasError){
                                        foreach ($businessClientsResult->result as $businessClientValue) {
                                            $selected = '';
                                            if($businessClient == $businessClientValue->getId()) $selected = 'selected';
                                            echo '<option value="'.$businessClientValue->getId().'" '.$selected.' >'.$businessClientValue->getName().'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-12">
                            <div class="form-group">
                                <label class="hidden-xs hidden-sm">&nbsp;</label>
                                <div class="checkbox" style="margin: 0; min-height: 34px; display: flex; align-items: center;">
                                    <label for="openAccess" title="Registros em aberto e fechados" style="font-weight: normal;">
                                        <input type="checkbox" value="1" name="with-open" id="openAccess" <?=($withOpen ? 'checked' : '')?> />
                                        Com acessos em aberto
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search"></i> Buscar
                            </button>
                            <a href="index.php?content=driverAccessList.php" class="btn btn-default">
                                <i class="fa fa-times"></i> Limpar Filtros
                            </a>
                            <a class="pull-right" href="../export/driverAccessExport.php?startDate=<?=date("Y-m-d", strtotime(str_replace('/', '-', $startDate)))?>&endDate=<?=date("Y-m-d", strtotime(str_replace('/', '-', $endDate)))?>&business=<?=$business?>&businessClient=<?=$businessClient?>&open-access=<?=$withOpenExport?>"><button type="button" class="btn btn-secondary"><i class="fa fa-file-excel-o"></i> Exportar</button></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- TABELA DE DADOS -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-body" style="overflow-x: auto;">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                            <?php
                                if($_SESSION['FUNCTION_ACCESS']['edit_access'] != 'hidden') {
                                    echo '<td scope="column" class="td-30" style="white-space: nowrap;">Editar</td>';
                                }
                            ?>
                                <td scope="column" class="td-30" style="white-space: nowrap;">Detalhes</td>
                                <td scope="column" class="td-30" style="white-space: nowrap;">Turno</td>
                                <td scope="column" class="td-40" style="white-space: nowrap;">Entrada</td>
                                <td scope="column" class="td-40" style="white-space: nowrap;">CPF</td>
                                <td scope="column" class="td-100" style="white-space: nowrap;">Nome</td>
                                <td scope="column" class="td-70" style="white-space: nowrap;">Empresa visitada</td>
                                <td scope="column" class="td-70" style="white-space: nowrap;">Empresa cliente</td>
                                <td scope="column" class="td-30" style="white-space: nowrap;">CNH</td>
                                <td scope="column" class="td-70" style="white-space: nowrap;">Vencimento CNH</td>
                                <td scope="column" class="td-70" style="white-space: nowrap;">Telefone</td>
                                <td scope="column" class="td-70" style="white-space: nowrap;">Transportadora</td>
                                <td scope="column" class="td-40" style="white-space: nowrap;">Saída</td>
                                <td scope="column" class="td-70" style="white-space: nowrap;">Tipo veículo</td>
                                <td scope="column" class="td-70" style="white-space: nowrap;">Placa veículo</td>
                                <td scope="column" class="td-70" style="white-space: nowrap;">Segunda placa</td>
                                <td scope="column" class="td-70" style="white-space: nowrap;">Terceira placa</td>
                                <td scope="column" class="td-70" style="white-space: nowrap;">Operação</td>
                                <td scope="column" class="td-70" style="white-space: nowrap;">NF entrada</td>
                                <td scope="column" class="td-70" style="white-space: nowrap;">NF saída</td>
                                <td scope="column" class="td-70" style="white-space: nowrap;">Usuário (entrada)</td>
                                <td scope="column" class="td-70" style="white-space: nowrap;">Usuário (saída)</td>
                                <?php
                                    if($_SESSION['FUNCTION_ACCESS']['delete_access'] != 'hidden'){
                                        echo '<td scope="column" class="td-40" style="white-space: nowrap;">Excluir</td>';
                                    }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if(!$driversAccessResult->hasError && isset($driversAccessResult->result['data'])){
                                $driversAccess = $driversAccessResult->result['data'];

                                if(count($driversAccess) > 0){
                                    foreach ($driversAccess as $driverAccess) {
                                        echo '<tr class="odd gradeX">';
                                        if($_SESSION['FUNCTION_ACCESS']['edit_access'] != 'hidden') {
                                            echo '<td class="text-center"><a href="index.php?content=newDriverAccess.php&driverAccessId='.$driverAccess->getId().'&action=edit-all"><span class="fa fa-edit text-primary"></span></a></td>';
                                        }
                                        echo '<td class="text-center"><a href="index.php?content=newDriverAccess.php&driverAccessId='.$driverAccess->getId().'&action=view"><span class="fa fa-search text-primary"></span></a></td>';
                                        echo '<td>'.$driverAccess->getRotation().'</td>';
                                        echo '<td>'.$driverAccess->getStartDatetime().'</td>';
                                        echo '<td>'.$driverAccess->getCpf().'</td>';
                                        echo '<td>'.$driverAccess->getDriverName().'</td>';
                                        if($driverAccess->getBusinessClientId() != null){
                                            echo '<td>'.$driverAccess->getBusinessName().'</td>';
                                        }else echo '<td></td>';
                                        echo '<td>'.$driverAccess->getBusinessClientName().'</td>';
                                        echo '<td>'.$driverAccess->getCnh().'</td>';
                                        echo '<td>'.$driverAccess->getCnhExpiration().'</td>';
                                        echo '<td>'.$driverAccess->getPhone().'</td>';
                                        echo '<td>'.$driverAccess->getShippingCompany().'</td>';
                                        echo '<td>'.$driverAccess->getEndDatetime().'</td>';
                                        echo '<td>'.$driverAccess->getVehicleType().'</td>';
                                        echo '<td>'.$driverAccess->getVehiclePlate().'</td>';
                                        echo '<td>'.$driverAccess->getVehiclePlate2().'</td>';
                                        echo '<td>'.$driverAccess->getVehiclePlate3().'</td>';
                                        echo '<td>'.$driverAccess->getOperationType().'</td>';
                                        echo '<td>'.$driverAccess->getInboundInvoice().'</td>';
                                        echo '<td>'.$driverAccess->getOutboundInvoice().'</td>';
                                        echo '<td>'.$driverAccess->getCreatedByName().'</td>';
                                        if(!is_null($driverAccess->getEndDatetime())){
                                            echo '<td>'.$driverAccess->getUserOutboundName().'</td>';
                                        }else{
                                            echo '<td></td>';
                                        }

                                        if($_SESSION['FUNCTION_ACCESS']['delete_access'] != 'hidden'){
                                            echo '<td class="text-center" data-toggle="modal" data-target="#delete-modal-'.$driverAccess->getId().'" style="cursor: pointer;"><span class="fa fa-trash text-primary"></span></td>';
                                        }
                                        echo '</tr>';
                                    }
                                }else{
                                    $colCount = $_SESSION['FUNCTION_ACCESS']['edit_access'] != 'hidden' ? 23 : 22;
                                    echo '<tr><td colspan="'.$colCount.'" class="text-center" style="padding: 20px;">Nenhum registro encontrado</td></tr>';
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

<!-- PAGINAÇÃO -->
<?php if(!$driversAccessResult->hasError && isset($driversAccessResult->result['pages'])):
    $total_pages = $driversAccessResult->result['pages'];
    $current_page = $driversAccessResult->result['page'];
    $total = $driversAccessResult->result['total'];
?>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <p class="text-muted" style="margin-bottom: 15px;">
                    Mostrando <strong><?php echo min($limit, $total); ?></strong> de <strong><?php echo $total; ?></strong> registros
                    (Página <strong><?php echo $current_page; ?></strong> de <strong><?php echo max(1, $total_pages); ?></strong>)
                </p>

                <nav>
                    <ul class="pagination">
                        <!-- Primeira página -->
                        <?php if($current_page > 1): ?>
                            <li>
                                <a href="index.php?content=driverAccessList.php&page=1&search=<?php echo urlencode($search); ?>&startDate=<?php echo urlencode($startDate); ?>&endDate=<?php echo urlencode($endDate); ?>&business=<?php echo urlencode($business); ?>&businessClient=<?php echo urlencode($businessClient); ?>&with-open=<?php echo ($withOpen ? '1' : '0'); ?>">
                                    <span aria-hidden="true">&laquo;</span> Primeira
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- Anterior -->
                        <?php if($current_page > 1): ?>
                            <li>
                                <a href="index.php?content=driverAccessList.php&page=<?php echo $current_page-1; ?>&search=<?php echo urlencode($search); ?>&startDate=<?php echo urlencode($startDate); ?>&endDate=<?php echo urlencode($endDate); ?>&business=<?php echo urlencode($business); ?>&businessClient=<?php echo urlencode($businessClient); ?>&with-open=<?php echo ($withOpen ? '1' : '0'); ?>">
                                    <span aria-hidden="true">&lsaquo;</span> Anterior
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- Números das páginas -->
                        <?php
                        $start = max(1, $current_page - 2);
                        $end = min($total_pages, $current_page + 2);

                        if($start > 1){
                            echo '<li><a href="index.php?content=driverAccessList.php&page=1&search='.urlencode($search).'&startDate='.urlencode($startDate).'&endDate='.urlencode($endDate).'&business='.urlencode($business).'&businessClient='.urlencode($businessClient).'&with-open='.($withOpen ? '1' : '0').'">1</a></li>';
                            if($start > 2) echo '<li class="disabled"><span>...</span></li>';
                        }

                        for($i = $start; $i <= $end; $i++){
                            if($i == $current_page){
                                echo '<li class="active"><span>'.$i.'</span></li>';
                            }else{
                                echo '<li><a href="index.php?content=driverAccessList.php&page='.$i.'&search='.urlencode($search).'&startDate='.urlencode($startDate).'&endDate='.urlencode($endDate).'&business='.urlencode($business).'&businessClient='.urlencode($businessClient).'&with-open='.($withOpen ? '1' : '0').'">'.$i.'</a></li>';
                            }
                        }

                        if($end < $total_pages){
                            if($end < $total_pages - 1) echo '<li class="disabled"><span>...</span></li>';
                            echo '<li><a href="index.php?content=driverAccessList.php&page='.$total_pages.'&search='.urlencode($search).'&startDate='.urlencode($startDate).'&endDate='.urlencode($endDate).'&business='.urlencode($business).'&businessClient='.urlencode($businessClient).'&with-open='.($withOpen ? '1' : '0').'">'.$total_pages.'</a></li>';
                        }
                        ?>

                        <!-- Próxima -->
                        <?php if($current_page < $total_pages): ?>
                            <li>
                                <a href="index.php?content=driverAccessList.php&page=<?php echo $current_page+1; ?>&search=<?php echo urlencode($search); ?>&startDate=<?php echo urlencode($startDate); ?>&endDate=<?php echo urlencode($endDate); ?>&business=<?php echo urlencode($business); ?>&businessClient=<?php echo urlencode($businessClient); ?>&with-open=<?php echo ($withOpen ? '1' : '0'); ?>">
                                    Próxima <span aria-hidden="true">&rsaquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- Última página -->
                        <?php if($current_page < $total_pages): ?>
                            <li>
                                <a href="index.php?content=driverAccessList.php&page=<?php echo $total_pages; ?>&search=<?php echo urlencode($search); ?>&startDate=<?php echo urlencode($startDate); ?>&endDate=<?php echo urlencode($endDate); ?>&business=<?php echo urlencode($business); ?>&businessClient=<?php echo urlencode($businessClient); ?>&with-open=<?php echo ($withOpen ? '1' : '0'); ?>">
                                    Última <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- MODAIS DE DELEÇÃO -->
<?php
if(!$driversAccessResult->hasError && isset($driversAccessResult->result['data'])){
    foreach ($driversAccessResult->result['data'] as $driverAccess) {
        $driverAccessId = $driverAccess->getId();
        ?>
        <div class="modal fade" id="delete-modal-<?php echo $driverAccessId; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form role="form-delete" method="post" action="#">
                        <input type="hidden" name="deleteId" value="<?php echo $driverAccessId; ?>">
                        <input type="hidden" name="action" value="delete">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Excluir</h4>
                        </div>
                        <div class="modal-body">
                            Tem certeza que deseja deletar o acesso de <?php echo htmlspecialchars($driverAccess->getDriverName()); ?>?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Não</button>
                            <button type="submit" class="btn btn-primary" id="confirm">Sim</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
}
?>
