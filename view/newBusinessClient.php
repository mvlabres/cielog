<?php

require_once('../utils.php');
require_once('../model/businessClient.php');
require_once('../model/client.php');
require_once('../controller/clientController.php');
require_once('../controller/businessClientController.php');

if($_SESSION['FUNCTION_ACCESS']['register_business_client'] == 'hidden') header('LOCATION:index.php');

$businessClientController = new BusinessClientController($MySQLi);
$clientController = new ClientController($MySQLi);
$businessClient = new BusinessClient();

$action = 'save';
$title = 'Criar';
$disabledField = 'disabled';
$viewMode = '';
$hiddenComponents = '';

if(isset($_GET['clientAction']) && $_GET['clientAction'] != null){
    if($_GET['clientAction'] == 'saved') successAlert('Registro salvo com sucesso'); 
    if($_GET['clientAction'] == 'deleted') successAlert('Registro excluído com sucesso'); 
}

if(isset($_GET['action']) && $_GET['action'] != null){
    $action = $_GET['action'];
}

if(isset($_GET['action']) && $_GET['action'] == 'view'){
    $viewMode = 'disabled';
    $hiddenComponents = 'hidden';
}

if(isset($_GET['businessClientId']) && $_GET['businessClientId'] != null){ 
    $businessClientId = $_GET['businessClientId'];
    $title = 'Editar';
    $action = 'update';

    $result = $businessClientController->findById($businessClientId);
    
    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    
    $businessClient = $result->result;
}

if(isset($_POST['idDelete']) && $_POST['idDelete'] != null ){

    $result = $businessClientController->delete($_POST['idDelete']);

    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    else echo "<script>window.location='index.php?content=newBusinessClient.php&clientAction=deleted'</script>";
}

if(isset($_POST['name']) && $_POST['name'] != null){

    $action = $_POST['action'];
    $result = $businessClientController->save($_POST, $action);

    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    else echo "<script>window.location='index.php?content=newBusinessClient.php&clientAction=saved'</script>";
}

$businessClientsResult = $businessClientController->findAll();
if($businessClientsResult->hasError) errorAlert($businessClientsResult->result.$businessClientsResult->errorMessage);

$clientsResult = $clientController->findAll();
if($clientsResult->hasError) errorAlert($clientsResult->result.$clientsResult->errorMessage);

?>

<div class="row">
<div class="col-lg-12">
        <h3 class="page-header" >Cliente visitante - <span id="title"><?=$title ?></span></h3>
    </div>      
</div>
<div class="row">
    <div class="col-lg-12">
         <div class="panel panel-default">
            <div class="panel-body">
                <form role="form-new-user" method="post" action="#">
                    <input  type="hidden" name="id" value="<?=$businessClient->getId() ?>" >
                    <input  type="hidden" name="redirect" id="redirect" value="no-redirect" >
                    <input  type="hidden" name="action" value="<?=$action ?>" >
                    <div class="form-group">
                        <label>Nome cliente</label>
                        <input style="text-transform: uppercase" class="form-control" name="name" id="name" placeholder="Nome" maxlength="50" value="<?=$businessClient->getName() ?>" <?=$viewMode ?> required>
                    </div>
                    <div class="form-group">
                        <label>Empresa</label>
                        <select name="business" id="business" class="form-control" <?=$viewMode ?> required>
                            <option value="">Selecione...</option>
                            <?php

                            if(!$clientsResult->hasError){
                                foreach ($clientsResult->result as $client) {
                                    $selected = null;
                                    if($businessClient->getClientId() == $client->getId()) $selected = 'selected';

                                    echo '<option value="'.$client->getId().'" '.$selected.' >'.$client->getName().'</option>';
                                }
                            }    
                            ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="user-save-btn">Salvar</button>
                    <button type="reset" class="btn btn-danger">Cancelar</button>
                </form> 
            </div>
        </div>
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
                                    <th scope="column" class="td-70">Nome</th>
                                    <th scope="column" class="td-70">Empresa</th>
                                    <th scope="column" class="td-70">Criado por</th>
                                    <th scope="column" class="td-70">Editar</th>
                                    <th scope="column" class="td-70">Excluir</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                if(!$businessClientsResult->hasError){
                                    foreach ($businessClientsResult->result as $businessClientValue) {
                                        echo '<tr class="odd gradeX">';
                                        echo '<td>'.$businessClientValue->getName().'</td>';
                                        echo '<td>'.$businessClientValue->getClientName().'</td>';
                                        echo '<td>'.$businessClientValue->getCreatedBy().'</td>';
                                        echo '<td class="text-center clickble"><a href="index.php?content=newBusinessClient.php&businessClientId=3"><span class="fa fa-edit text-primary"></span></a></td>';
                                        echo '<td class="text-center clickble" data-toggle="modal" data-target="#'.$businessClientValue->getId().'"><span class="fa fa-trash text-primary"></span></td>';
                                        echo '</tr>';
                                        echo '<div class="modal fade" id="'.$businessClientValue->getId().'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form role="form-delete" method="post" action="#">
                                                    <input type="hidden" name="idDelete" value="'.$businessClientValue->getId().'">
                                                    <input type="hidden" name="action" value="delete" >
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title" id="myModalLabel">Excluir</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        Tem certeza que deseja deletar '.$businessClientValue->getName().'?
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
