<?php

require_once('../model/errorHandler.php');
require_once('../model/driver.php');
require_once('../controller/driverController.php');
require_once('../utils.php');

$driverController = new DriverController($MySQLi);

// Obter parâmetros
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 50;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$orderBy = isset($_GET['sort']) ? $_GET['sort'] : 'name';

// Ação de delete
if(isset($_POST['action']) && $_POST['action'] == 'delete'){
    $driverId = $_POST['idDelete'];
    $result = $driverController->delete($driverId);
    if(!$result->hasError) {
        successAlert($result->result);
        header("Location: index.php?content=driverList.php&page=$page&search=".urlencode($search));
        exit;
    }else{
        errorAlert($result->result.$result->errorMessage);
    }
}

// Mensagem de sucesso após insert
if(isset($_GET['action']) && $_GET['action'] != null){
    successAlert('Registro salvo com sucesso');
}

// Buscar dados paginados
$driverResult = $driverController->findPaginated($page, $limit, $search, $orderBy);

?>

<div class="row row-space-between">
    <div class="col-lg-12">
        <h3 class="page-header">Motoristas, visitantes e outros</h3>
    </div>
    <div class="center-box">
        <a href="index.php?content=newDriver.php" type="button" class="btn btn-primary" id="user-save-btn">Novo</a>
    </div>
</div>

<!-- FILTRO DE BUSCA -->
<div class="row" style="margin-bottom: 20px;">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <form method="GET" action="">
                    <input type="hidden" name="content" value="driverList.php">

                    <div class="form-group">
                        <label>Buscar Motorista</label>
                        <input type="text" name="search" placeholder="Nome, CPF, Telefone ou Transportadora..."
                               value="<?php echo htmlspecialchars($search); ?>" class="form-control" style="margin-bottom: 10px;">
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i> Buscar
                        </button>
                        <a href="index.php?content=driverList.php" class="btn btn-default">
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
                                <th scope="column" class="td-120" style="white-space: nowrap;">Nome</th>
                                <th scope="column" class="td-40" style="white-space: nowrap;">CPF</th>
                                <th scope="column" class="td-70" style="white-space: nowrap;">Tipo cadastro</th>
                                <th scope="column" class="td-40" style="white-space: nowrap;">CNH</th>
                                <th scope="column" class="td-100" style="white-space: nowrap;">Vencimento CNH</th>
                                <th scope="column" class="td-70" style="white-space: nowrap;">Telefone</th>
                                <th scope="column" class="td-70" style="white-space: nowrap;">Transportadora</th>
                                <th scope="column" class="td-70" style="white-space: nowrap;">Tipo veículo</th>
                                <th scope="column" class="td-70" style="white-space: nowrap;">Placa Veículo</th>
                                <th scope="column" class="td-70" style="white-space: nowrap;">Segunda placa</th>
                                <th scope="column" class="td-70" style="white-space: nowrap;">Terceira placa</th>
                                <th scope="column" class="td-70" style="white-space: nowrap;">Status</th>
                                <th scope="column" class="td-100" style="white-space: nowrap;">Motivo do bloqueio</th>
                                <th scope="column" class="td-30" style="white-space: nowrap;">Excluir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if(!$driverResult->hasError && isset($driverResult->result['data'])){
                                $drivers = $driverResult->result['data'];

                                if(count($drivers) > 0){
                                    foreach ($drivers as $driver) {
                                        $driverId = $driver->getId();
                                        echo '<tr class="odd gradeX">';
                                        echo '<td class="text-center clickble"><a class="cell-action" href="index.php?content=newDriverAccess.php&driverId='.$driverId.'"><span class="fa fa-hand-o-right text-primary"></span></a></td>';
                                        echo '<td class="text-center clickble"><a class="cell-action" href="index.php?content=newDriver.php&driverId='.$driverId.'&action=view"><span class="fa fa-search text-primary"></span></a></td>';
                                        echo '<td class="text-center clickble"><a class="cell-action" href="index.php?content=newDriver.php&driverId='.$driverId.'&action=edit"><span class="fa fa-edit text-primary"></span></a></td>';
                                        echo '<td>'.$driver->getName().'</td>';
                                        echo '<td>'.$driver->getCpf().'</td>';
                                        echo '<td>'.$DRIVER_RECORD_TYPES[$driver->getRecordType()].'</td>';
                                        echo '<td>'.$driver->getCnh().'</td>';
                                        echo '<td>'.$driver->getCnhExpiration().'</td>';
                                        echo '<td>'.$driver->getPhone().'</td>';
                                        echo '<td>'.$driver->getShippingCompany().'</td>';
                                        echo '<td>'.$driver->getVehicleType().'</td>';
                                        echo '<td>'.$driver->getVehiclePlate().'</td>';
                                        echo '<td>'.$driver->getVehiclePlate2().'</td>';
                                        echo '<td>'.$driver->getVehiclePlate3().'</td>';
                                        echo '<td>'.$DRIVER_STATUS[$driver->getStatus()].'</td>';
                                        echo '<td>'.$driver->getBlockReason().'</td>';
                                        echo '<td class="text-center clickble" data-toggle="modal" data-target="#delete-modal-'.$driverId.'" style="cursor: pointer;"><span class="fa fa-trash text-primary"></span></td>';
                                        echo '</tr>';
                                    }
                                }else{
                                    echo '<tr><td colspan="17" class="text-center" style="padding: 20px;">Nenhum registro encontrado</td></tr>';
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
<?php if(!$driverResult->hasError && isset($driverResult->result['pages'])):
    $total_pages = $driverResult->result['pages'];
    $current_page = $driverResult->result['page'];
    $total = $driverResult->result['total'];
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
                                <a href="index.php?content=driverList.php&page=1&search=<?php echo urlencode($search); ?>">
                                    <span aria-hidden="true">&laquo;</span> Primeira
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- Anterior -->
                        <?php if($current_page > 1): ?>
                            <li>
                                <a href="index.php?content=driverList.php&page=<?php echo $current_page-1; ?>&search=<?php echo urlencode($search); ?>">
                                    <span aria-hidden="true">&lsaquo;</span> Anterior
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- Números das páginas -->
                        <?php
                        $start = max(1, $current_page - 2);
                        $end = min($total_pages, $current_page + 2);

                        if($start > 1){
                            echo '<li><a href="index.php?content=driverList.php&page=1&search='.urlencode($search).'">1</a></li>';
                            if($start > 2) echo '<li class="disabled"><span>...</span></li>';
                        }

                        for($i = $start; $i <= $end; $i++){
                            if($i == $current_page){
                                echo '<li class="active"><span>'.$i.'</span></li>';
                            }else{
                                echo '<li><a href="index.php?content=driverList.php&page='.$i.'&search='.urlencode($search).'">'.$i.'</a></li>';
                            }
                        }

                        if($end < $total_pages){
                            if($end < $total_pages - 1) echo '<li class="disabled"><span>...</span></li>';
                            echo '<li><a href="index.php?content=driverList.php&page='.$total_pages.'&search='.urlencode($search).'">'.$total_pages.'</a></li>';
                        }
                        ?>

                        <!-- Próxima -->
                        <?php if($current_page < $total_pages): ?>
                            <li>
                                <a href="index.php?content=driverList.php&page=<?php echo $current_page+1; ?>&search=<?php echo urlencode($search); ?>">
                                    Próxima <span aria-hidden="true">&rsaquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- Última página -->
                        <?php if($current_page < $total_pages): ?>
                            <li>
                                <a href="index.php?content=driverList.php&page=<?php echo $total_pages; ?>&search=<?php echo urlencode($search); ?>">
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
if(!$driverResult->hasError && isset($driverResult->result['data'])){
    foreach ($driverResult->result['data'] as $driver) {
        $driverId = $driver->getId();
        ?>
        <div class="modal fade" id="delete-modal-<?php echo $driverId; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form role="form-delete" method="post" action="#">
                        <input type="hidden" name="idDelete" value="<?php echo $driverId; ?>">
                        <input type="hidden" name="action" value="delete">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Excluir</h4>
                        </div>
                        <div class="modal-body">
                            Tem certeza que deseja deletar <?php echo htmlspecialchars($driver->getName()); ?>?
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
