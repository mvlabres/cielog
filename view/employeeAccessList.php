<?php

require_once('../conn.php');
require_once('../session.php');
require_once('../utils.php');
require_once('../controller/employeeAccessController.php');
require_once('../controller/clientController.php');
require_once('../model/employeeAccess.php');

date_default_timezone_set("America/Sao_Paulo");

$employeeAccessController = new employeeAccessController($MySQLi);
$clientController = new ClientController($MySQLi);

// Obter parâmetros
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 50;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$startDate = isset($_GET['startDate']) ? trim($_GET['startDate']) : '01/'.date("m/Y");
$endDate = isset($_GET['endDate']) ? trim($_GET['endDate']) : date("d/m/Y");
$business = isset($_GET['business']) ? $_GET['business'] : getClientIfContains();
$withOpen = isset($_GET['startDate']) ? (isset($_GET['with-open']) && $_GET['with-open'] == '1') : true;

// Ação de delete
if(isset($_POST['action']) && $_POST['action'] == 'delete'){
    $employeeAccessId = $_POST['deleteId'];
    $result = $employeeAccessController->delete($employeeAccessId);

    if(!$result->hasError) {
        successAlert($result->result);
        header("Location: index.php?content=employeeAccessList.php&page=$page&search=".urlencode($search)."&startDate=".urlencode($startDate)."&endDate=".urlencode($endDate)."&business=".urlencode($business)."&with-open=".($withOpen ? '1' : '0'));
        exit;
    }else{
        errorAlert($result->result.$result->errorMessage);
    }
}

// Buscar dados paginados
$employeesAccessResult = $employeeAccessController->findByStartDateEndDateAndBusinessPaginated($startDate, $endDate, $business, $withOpen, $search, $page, $limit);
if($employeesAccessResult->hasError) errorAlert($employeesAccessResult->result.$employeesAccessResult->errorMessage);

$clientsResult = $clientController->findAll();
if($clientsResult->hasError) errorAlert($clientsResult->result.$clientsResult->errorMessage);

$withOpenExport = ($withOpen) ? 'true': 'false';

?>
<div class="row" style="margin-bottom: 20px;">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><strong><i class="fa fa-filter"></i> Filtros</strong></div>
            <div class="panel-body">
                <form method="GET" id="panel-form" action="">
                    <input type="hidden" name="content" value="employeeAccessList.php">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Buscar Acesso</label>
                                <input type="text" name="search" placeholder="Nome, CPF, Matrícula, Placa ou Empresa..."
                                       value="<?php echo htmlspecialchars($search); ?>" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label>Data inicial</label>
                                <div class='input-group date' id='datetimepicker1'>
                                    <input name="startDate" id="startDate" type='text' data-date-format="DD/MM/YYYY" class="form-control" value="<?=htmlspecialchars($startDate)?>" onblur="dateTimeHandleBlur(this)" required minlength="10" maxlength="10" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
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
                                <select name="business" id="business" class="form-control">
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
                            <a href="index.php?content=employeeAccessList.php" class="btn btn-default">
                                <i class="fa fa-times"></i> Limpar Filtros
                            </a>
                            <a class="pull-right" href="../export/employeeAccessExport.php?startDate=<?=date("Y-m-d", strtotime(str_replace('/', '-', $startDate)))?>&endDate=<?=date("Y-m-d", strtotime(str_replace('/', '-', $endDate)))?>&business=<?=$business?>&open-access=<?=$withOpenExport?>"><button type="button" class="btn btn-secondary"><i class="fa fa-file-excel-o"></i> Exportar</button></a>
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
                                <td scope="column" class="td-70" style="white-space: nowrap;">Empresa</td>
                                <td scope="column" class="td-30" style="white-space: nowrap;">Matrícula</td>
                                <td scope="column" class="td-40" style="white-space: nowrap;">Saída</td>
                                <td scope="column" class="td-70" style="white-space: nowrap;">Veículo</td>
                                <td scope="column" class="td-70" style="white-space: nowrap;">Placa veículo</td>
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
                            if(!$employeesAccessResult->hasError && isset($employeesAccessResult->result['data'])){
                                $employeesAccess = $employeesAccessResult->result['data'];

                                if(count($employeesAccess) > 0){
                                    foreach ($employeesAccess as $employeeAccess) {
                                        $employee = $employeeAccess->getEmployee();

                                        echo '<tr class="odd gradeX">';
                                        if($_SESSION['FUNCTION_ACCESS']['edit_access'] != 'hidden') {
                                            echo '<td class="text-center"><a href="index.php?content=newEmployeeAccess.php&employeeAccessId='.$employeeAccess->getId().'&action=edit-all"><span class="fa fa-edit text-primary"></span></a></td>';
                                        }
                                        echo '<td class="text-center"><a href="index.php?content=newEmployeeAccess.php&employeeAccessId='.$employeeAccess->getId().'&action=view"><span class="fa fa-search text-primary"></span></a></td>';
                                        echo '<td>'.$employeeAccess->getRotation().'</td>';
                                        echo '<td>'.$employeeAccess->getStartDatetime().'</td>';
                                        echo '<td>'.$employee->getCpf().'</td>';
                                        echo '<td>'.$employee->getName().'</td>';
                                        echo '<td>'.$employee->getBusinessName().'</td>';
                                        echo '<td>'.$employee->getRegistration().'</td>';
                                        echo '<td>'.$employeeAccess->getEndDatetime().'</td>';
                                        echo '<td>'.$employeeAccess->getVehicle().'</td>';
                                        echo '<td>'.$employeeAccess->getVehiclePlate().'</td>';
                                        echo '<td>'.$employeeAccess->getCreatedByName().'</td>';
                                        if(!is_null($employeeAccess->getEndDatetime())){
                                            echo '<td>'.$employeeAccess->getUserOutboundName().'</td>';
                                        }else{
                                            echo '<td></td>';
                                        }

                                        if($_SESSION['FUNCTION_ACCESS']['delete_access'] != 'hidden'){
                                            echo '<td class="text-center" data-toggle="modal" data-target="#delete-modal-'.$employeeAccess->getId().'" style="cursor: pointer;"><span class="fa fa-trash text-primary"></span></td>';
                                        }
                                        echo '</tr>';
                                    }
                                }else{
                                    $colCount = $_SESSION['FUNCTION_ACCESS']['edit_access'] != 'hidden' ? 13 : 12;
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
<?php if(!$employeesAccessResult->hasError && isset($employeesAccessResult->result['pages'])):
    $total_pages = $employeesAccessResult->result['pages'];
    $current_page = $employeesAccessResult->result['page'];
    $total = $employeesAccessResult->result['total'];
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
                                <a href="index.php?content=employeeAccessList.php&page=1&search=<?php echo urlencode($search); ?>&startDate=<?php echo urlencode($startDate); ?>&endDate=<?php echo urlencode($endDate); ?>&business=<?php echo urlencode($business); ?>&with-open=<?php echo ($withOpen ? '1' : '0'); ?>">
                                    <span aria-hidden="true">&laquo;</span> Primeira
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- Anterior -->
                        <?php if($current_page > 1): ?>
                            <li>
                                <a href="index.php?content=employeeAccessList.php&page=<?php echo $current_page-1; ?>&search=<?php echo urlencode($search); ?>&startDate=<?php echo urlencode($startDate); ?>&endDate=<?php echo urlencode($endDate); ?>&business=<?php echo urlencode($business); ?>&with-open=<?php echo ($withOpen ? '1' : '0'); ?>">
                                    <span aria-hidden="true">&lsaquo;</span> Anterior
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- Números das páginas -->
                        <?php
                        $start = max(1, $current_page - 2);
                        $end = min($total_pages, $current_page + 2);

                        if($start > 1){
                            echo '<li><a href="index.php?content=employeeAccessList.php&page=1&search='.urlencode($search).'&startDate='.urlencode($startDate).'&endDate='.urlencode($endDate).'&business='.urlencode($business).'&with-open='.($withOpen ? '1' : '0').'">1</a></li>';
                            if($start > 2) echo '<li class="disabled"><span>...</span></li>';
                        }

                        for($i = $start; $i <= $end; $i++){
                            if($i == $current_page){
                                echo '<li class="active"><span>'.$i.'</span></li>';
                            }else{
                                echo '<li><a href="index.php?content=employeeAccessList.php&page='.$i.'&search='.urlencode($search).'&startDate='.urlencode($startDate).'&endDate='.urlencode($endDate).'&business='.urlencode($business).'&with-open='.($withOpen ? '1' : '0').'">'.$i.'</a></li>';
                            }
                        }

                        if($end < $total_pages){
                            if($end < $total_pages - 1) echo '<li class="disabled"><span>...</span></li>';
                            echo '<li><a href="index.php?content=employeeAccessList.php&page='.$total_pages.'&search='.urlencode($search).'&startDate='.urlencode($startDate).'&endDate='.urlencode($endDate).'&business='.urlencode($business).'&with-open='.($withOpen ? '1' : '0').'">'.$total_pages.'</a></li>';
                        }
                        ?>

                        <!-- Próxima -->
                        <?php if($current_page < $total_pages): ?>
                            <li>
                                <a href="index.php?content=employeeAccessList.php&page=<?php echo $current_page+1; ?>&search=<?php echo urlencode($search); ?>&startDate=<?php echo urlencode($startDate); ?>&endDate=<?php echo urlencode($endDate); ?>&business=<?php echo urlencode($business); ?>&with-open=<?php echo ($withOpen ? '1' : '0'); ?>">
                                    Próxima <span aria-hidden="true">&rsaquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- Última página -->
                        <?php if($current_page < $total_pages): ?>
                            <li>
                                <a href="index.php?content=employeeAccessList.php&page=<?php echo $total_pages; ?>&search=<?php echo urlencode($search); ?>&startDate=<?php echo urlencode($startDate); ?>&endDate=<?php echo urlencode($endDate); ?>&business=<?php echo urlencode($business); ?>&with-open=<?php echo ($withOpen ? '1' : '0'); ?>">
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
if(!$employeesAccessResult->hasError && isset($employeesAccessResult->result['data'])){
    foreach ($employeesAccessResult->result['data'] as $employeeAccess) {
        $employeeAccessId = $employeeAccess->getId();
        $employee = $employeeAccess->getEmployee();
        ?>
        <div class="modal fade" id="delete-modal-<?php echo $employeeAccessId; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form role="form-delete" method="post" action="#">
                        <input type="hidden" name="deleteId" value="<?php echo $employeeAccessId; ?>">
                        <input type="hidden" name="action" value="delete">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Excluir</h4>
                        </div>
                        <div class="modal-body">
                            Tem certeza que deseja deletar o acesso de <?php echo htmlspecialchars($employee->getName()); ?>?
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
