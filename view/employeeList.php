<?php

require_once('../model/errorHandler.php');
require_once('../model/employee.php');
require_once('../model/client.php');
require_once('../controller/clientController.php');
require_once('../controller/employeeController.php');
require_once('../utils.php');

$clientController = new ClientController($MySQLi);
$employeeController = new EmployeeController($MySQLi);

// Obter parâmetros
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 50;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$businessId = isset($_GET['business']) ? $_GET['business'] : 'all';
$orderBy = isset($_GET['sort']) ? $_GET['sort'] : 'employee.name';

// Ação de delete
if(isset($_POST['action']) && $_POST['action'] == 'delete'){
    $employeeId = $_POST['idDelete'];
    $result = $employeeController->delete($employeeId);
    if(!$result->hasError) {
        successAlert($result->result);
        header("Location: index.php?content=employeeList.php&page=$page&business=".urlencode($businessId)."&search=".urlencode($search));
        exit;
    }else{
        errorAlert($result->result.$result->errorMessage);
    }
}

// Mensagem de sucesso após insert
if(isset($_GET['action']) && $_GET['action'] != null){
    successAlert('Colaborador salvo com sucesso');
}

// Buscar dados paginados (com filtro de business se necessário)
$businessIdForQuery = ($businessId == 'all') ? null : $businessId;
$employeeResult = $employeeController->findPaginated($page, $limit, $search, $businessIdForQuery, $orderBy);

// Buscar empresas para o filtro
$clientsResult = $clientController->findAll();
if($clientsResult->hasError) errorAlert($clientsResult->result.$clientsResult->errorMessage);

?>

<div class="row row-space-between">
    <div class="col-lg-12">
        <h3 class="page-header">Colaboradores</h3>
    </div>
    <div class="center-box">
        <a href="index.php?content=newEmployee.php" type="button" class="btn btn-primary" id="user-save-btn">Novo Colaborador</a>
    </div>
</div>

<!-- FILTROS -->
<div class="row" style="margin-bottom: 20px;">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <form method="GET" action="">
                    <input type="hidden" name="content" value="employeeList.php">

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Buscar Colaborador</label>
                                <input type="text" name="search" placeholder="Nome, CPF ou Matrícula..."
                                       value="<?php echo htmlspecialchars($search); ?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Empresa</label>
                                <select name="business" class="form-control">
                                    <option value="all">Todas as Empresas</option>
                                    <?php
                                    if(!$clientsResult->hasError){
                                        foreach ($clientsResult->result as $client) {
                                            $selected = ($client->getId() == $businessId) ? 'selected' : '';
                                            echo '<option value="'.$client->getId().'" '.$selected.'>'.$client->getName().'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i> Buscar
                        </button>
                        <a href="index.php?content=employeeList.php" class="btn btn-default">
                            <i class="fa fa-times"></i> Limpar Filtros
                        </a>
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
                                <td scope="column" class="td-30" style="white-space: nowrap;">Acesso</td>
                                <td scope="column" class="td-30" style="white-space: nowrap;">Detalhes</td>
                                <td scope="column" class="td-30" style="white-space: nowrap;">Editar</td>
                                <th scope="column" class="td-70" style="white-space: nowrap;">Nome</th>
                                <th scope="column" class="td-70" style="white-space: nowrap;">Matrícula</th>
                                <th scope="column" class="td-70" style="white-space: nowrap;">CPF</th>
                                <th scope="column" class="td-70" style="white-space: nowrap;">Empresa</th>
                                <th scope="column" class="td-70" style="white-space: nowrap;">Veículo</th>
                                <th scope="column" class="td-70" style="white-space: nowrap;">Placa Veículo</th>
                                <th scope="column" class="td-70" style="white-space: nowrap;">Status</th>
                                <th scope="column" class="td-30" style="white-space: nowrap;">Excluir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if(!$employeeResult->hasError && isset($employeeResult->result['data'])){
                                $employees = $employeeResult->result['data'];

                                if(count($employees) > 0){
                                    foreach ($employees as $employee) {
                                        $employeeId = $employee->getId();
                                        echo '<tr class="odd gradeX">';
                                        echo '<td class="text-center clickble"><a class="cell-action" href="index.php?content=newEmployeeAccess.php&employeeId='.$employeeId.'"><span class="fa fa-hand-o-right text-primary"></span></a></td>';
                                        echo '<td class="text-center clickble"><a class="cell-action" href="index.php?content=newEmployee.php&employeeId='.$employeeId.'&action=view"><span class="fa fa-search text-primary"></span></a></td>';
                                        echo '<td class="text-center clickble"><a class="cell-action" href="index.php?content=newEmployee.php&employeeId='.$employeeId.'&action=edit"><span class="fa fa-edit text-primary"></span></a></td>';
                                        echo '<td>'.$employee->getName().'</td>';
                                        echo '<td>'.$employee->getRegistration().'</td>';
                                        echo '<td>'.$employee->getCpf().'</td>';
                                        echo '<td>'.$employee->getBusinessName().'</td>';
                                        echo '<td>'.$employee->getVehicle().'</td>';
                                        echo '<td>'.$employee->getVehiclePlate().'</td>';
                                        echo '<td>'.$EMPLOYEE_STATUS[$employee->getStatus()].'</td>';
                                        echo '<td class="text-center clickble" data-toggle="modal" data-target="#delete-modal-'.$employeeId.'" style="cursor: pointer;"><span class="fa fa-trash text-primary"></span></td>';
                                        echo '</tr>';
                                    }
                                }else{
                                    echo '<tr><td colspan="11" class="text-center" style="padding: 20px;">Nenhum registro encontrado</td></tr>';
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
<?php if(!$employeeResult->hasError && isset($employeeResult->result['pages'])):
    $total_pages = $employeeResult->result['pages'];
    $current_page = $employeeResult->result['page'];
    $total = $employeeResult->result['total'];
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
                                <a href="index.php?content=employeeList.php&page=1&business=<?php echo urlencode($businessId); ?>&search=<?php echo urlencode($search); ?>">
                                    <span aria-hidden="true">&laquo;</span> Primeira
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- Anterior -->
                        <?php if($current_page > 1): ?>
                            <li>
                                <a href="index.php?content=employeeList.php&page=<?php echo $current_page-1; ?>&business=<?php echo urlencode($businessId); ?>&search=<?php echo urlencode($search); ?>">
                                    <span aria-hidden="true">&lsaquo;</span> Anterior
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- Números das páginas -->
                        <?php
                        $start = max(1, $current_page - 2);
                        $end = min($total_pages, $current_page + 2);

                        if($start > 1){
                            echo '<li><a href="index.php?content=employeeList.php&page=1&business='.urlencode($businessId).'&search='.urlencode($search).'">1</a></li>';
                            if($start > 2) echo '<li class="disabled"><span>...</span></li>';
                        }

                        for($i = $start; $i <= $end; $i++){
                            if($i == $current_page){
                                echo '<li class="active"><span>'.$i.'</span></li>';
                            }else{
                                echo '<li><a href="index.php?content=employeeList.php&page='.$i.'&business='.urlencode($businessId).'&search='.urlencode($search).'">'.$i.'</a></li>';
                            }
                        }

                        if($end < $total_pages){
                            if($end < $total_pages - 1) echo '<li class="disabled"><span>...</span></li>';
                            echo '<li><a href="index.php?content=employeeList.php&page='.$total_pages.'&business='.urlencode($businessId).'&search='.urlencode($search).'">'.$total_pages.'</a></li>';
                        }
                        ?>

                        <!-- Próxima -->
                        <?php if($current_page < $total_pages): ?>
                            <li>
                                <a href="index.php?content=employeeList.php&page=<?php echo $current_page+1; ?>&business=<?php echo urlencode($businessId); ?>&search=<?php echo urlencode($search); ?>">
                                    Próxima <span aria-hidden="true">&rsaquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- Última página -->
                        <?php if($current_page < $total_pages): ?>
                            <li>
                                <a href="index.php?content=employeeList.php&page=<?php echo $total_pages; ?>&business=<?php echo urlencode($businessId); ?>&search=<?php echo urlencode($search); ?>">
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
if(!$employeeResult->hasError && isset($employeeResult->result['data'])){
    foreach ($employeeResult->result['data'] as $employee) {
        $employeeId = $employee->getId();
        ?>
        <div class="modal fade" id="delete-modal-<?php echo $employeeId; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form role="form-delete" method="post" action="#">
                        <input type="hidden" name="idDelete" value="<?php echo $employeeId; ?>">
                        <input type="hidden" name="action" value="delete">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Excluir</h4>
                        </div>
                        <div class="modal-body">
                            Tem certeza que deseja deletar <?php echo htmlspecialchars($employee->getName()); ?>?
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
