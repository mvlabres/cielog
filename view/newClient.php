<?php

require_once('../model/client.php');
require_once('../model/errorHandler.php');
require_once('../controller/clientController.php');
require_once('../utils.php');

$clientController = new ClientController($MySQLi);
$client = new Client();
$result;
$action;

if(isset($_POST['name']) && $_POST['name'] != null ){

    $action = $_POST['action'];
    $result = $clientController->save($_POST, $action);

    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    else successAlert($result->result);
}

if(isset($_POST['idDelete']) && $_POST['idDelete'] != null ){

    $result = $clientController->delete($_POST['idDelete']);

    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    else successAlert($result->result);
}

$action = 'save';
$clientsResult = $clientController->findAll();

if($clientsResult->hasError) errorAlert($clientsResult->result.$clientsResult->errorMessage);

?>

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header" >Empresa cliente - <span id="title">Criar</span></h3>
    </div>                
</div>
<div class="row">
    <div class="col-lg-12">
         <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <form role="form-new-user" method="post" action="#">
                        <input  type="hidden" name="id" id="id" value="<?=$client->getId() ?>" >
                        <input  type="hidden" name="action" id="action" value="<?=$action ?>" >
                            <div class="form-group">
                                <label>Nome da empresa</label>
                                <input class="form-control" name="name" id="name" placeholder="Nome" maxlength="50" value="<?=$client->getName()  ?>" required>
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
<div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th scope="column" class="td-70">Nome</th>
                                    <th scope="column" class="td-70">Editar</th>
                                    <th scope="column" class="td-70">Excluir</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                if(!$clientsResult->hasError){
                                    foreach ($clientsResult->result as $clientValue) {
                                        echo '<tr class="odd gradeX">';
                                        echo '<td>'.$clientValue->getName().'</td>';
                                        echo '<td class="text-center clickble" onclick="editForm('.$clientValue->getId().', \''.$clientValue->getName().'\')"><span class="fa fa-edit text-primary"></span></td>';
                                        echo '<td class="text-center clickble" data-toggle="modal" data-target="#'.$clientValue->getId().'"><span class="fa fa-trash text-primary"></span></td>';
                                        echo '</tr>';
                                        echo '<div class="modal fade" id="'.$clientValue->getId().'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form role="form-delete" method="post" action="#">
                                                    <input type="hidden" name="idDelete" value="'.$clientValue->getId().'">
                                                    <input type="hidden" name="action" value="delete" >
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title" id="myModalLabel">Excluir</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        Tem certeza que deseja deletar '.$clientValue->getName().'?
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
    
       